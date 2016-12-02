<?php
/* Harvest WD */
set_time_limit(360000);
include "functions.php";
include "config_harvest.php";

list($g_usec, $g_sec) = explode(" ",microtime());
define ("t_start", (float)$g_usec + (float)$g_sec);

$link = mysqli_connect ($host,$user,$pass,$db) or die ('Erreur : '.mysqli_error());
mysqli_query($link,"SET NAMES 'utf8'");

mysqli_query($link,"TRUNCATE `books`");
mysqli_query($link,"TRUNCATE `artw_prop`");
mysqli_query($link,"TRUNCATE `label_page`");
mysqli_query($link,"TRUNCATE `movies`");
mysqli_query($link,"TRUNCATE `no_gallica`");
mysqli_query($link,"TRUNCATE `p50`");
mysqli_query($link,"TRUNCATE `p57`");
mysqli_query($link,"TRUNCATE `p136`");
mysqli_query($link,"TRUNCATE `p144`");
mysqli_query($link,"TRUNCATE `p161`");
mysqli_query($link,"TRUNCATE `p364`");
mysqli_query($link,"TRUNCATE `p495`");

exec("del /Q ".str_replace("/","\\",$fold)."harvest\\bnf\\*.*");
exec("del /Q ".str_replace("/","\\",$fold)."harvest\\imdb\\*.*");
exec("del /Q ".str_replace("/","\\",$fold)."harvest\\items\\books\\*.*");
exec("del /Q ".str_replace("/","\\",$fold)."harvest\\items\\movies\\*.*");
exec("del /Q ".str_replace("/","\\",$fold)."harvest\\items\\others\\*.*");

$cpt_movies=0;
$cpt_books=0;
$sparql="SELECT DISTINCT ?movie ?IMDb (GROUP_CONCAT(DISTINCT ?book; separator=\";\") as ?books)   
WHERE {
  ?movie wdt:P31/wdt:P279* wd:Q11424.
  ?movie wdt:P144 ?book.
  {?book wdt:P31/wdt:P279* wd:Q571} UNION {?book wdt:P31/wdt:P279* wd:Q7725634}
  ?book wdt:P268 ?IDBnF.  
  ?movie wdt:P345 ?IMDb
}GROUP BY ?movie ?IMDb";
//$sparql.=" LIMIT 10";


$responseArray=getSparQL($sparql);
$catmovies=array();
foreach ($responseArray["results"]["bindings"] as $key => $value){
	$WDmovie=$value["movie"]["value"];
	$IMDb=$value["IMDb"]["value"];
	$books=$value["books"]["value"];
	$gallica=false;
	$tabbooks=preg_split("/;/",$books);
	$p144=array();
	for ($i=0;$i<count($tabbooks);$i++){
		$qbook=str_replace("http://www.wikidata.org/entity/","",$tabbooks[$i]);
		$noqbook=str_replace("Q","",$qbook);
		$book_path=$fold."harvest/items/books/".$noqbook.".json";
		if (file_exists($book_path)){
			$gallica=true;
			$p144[]=$qbook;
		}
		else{
			$sql="SELECT id FROM no_gallica WHERE qwd=".$noqbook;
			$rep=mysqli_query($link,$sql);
			$found=false;
			if (mysqli_num_rows($rep)==0){		
				$sparql="SELECT DISTINCT ?IDBnF WHERE { <".$tabbooks[$i]."> wdt:P268 ?IDBnF}";
				$responseArray2=getSparQL($sparql);
				foreach ($responseArray2["results"]["bindings"] as $key2 => $value2){
					$IDBnF=$value2["IDBnF"]["value"];
					$IDGallica=dataToGallica($IDBnF);
					if ($IDGallica!=""){ 
						$gallica=true;
						$p144[]=$qbook;
					}
				}
				if (!$gallica)
					$rep=mysqli_query($link,"INSERT INTO no_gallica (qwd) VALUES ($noqbook)");
			}
		}
	}

	if ($gallica){
		$qmovie=str_replace("http://www.wikidata.org/entity/","",$WDmovie);
		$noqmovie=str_replace("Q","",$qmovie);
		$dfic=get_WDjson($noqmovie,"movies");
		$data = json_decode($dfic,true);
		$varlab=$data["entities"][$qmovie];
		$claims=$varlab["claims"];
		foreach ($claims["P345"] as $valprop)
			$imdb=$valprop["mainsnak"]["datavalue"]["value"];	
		$poster=poster($imdb);		
		if ($poster!=""){
			$publication="null";
			if ($claims["P577"])
				foreach ($claims["P577"] as $valprop)
					$publication=substr($valprop["mainsnak"]["datavalue"]["value"]["time"],1,4);
			
			$url="";
			if ($claims["P1651"])
				foreach ($claims["P1651"] as $valprop)
					$url="https://www.youtube.com/watch?v=".$valprop["mainsnak"]["datavalue"]["value"];
			else{
				if ($claims["P10"])
					foreach ($claims["P10"] as $valprop)
						$url="https://commons.wikimedia.org/wiki/File:".$valprop["mainsnak"]["datavalue"]["value"];
				else{
					if ($claims["P963"])
						foreach ($claims["P963"] as $valprop)
							$url=$valprop["mainsnak"]["datavalue"]["value"];
					else{
						if ($claims["P724"])
							foreach ($claims["P724"] as $valprop)
								$url="https://archive.org/details/".$valprop["mainsnak"]["datavalue"]["value"];
					}
				}
			}
				
			$sql="INSERT INTO movies (qwd,publication,imdb,poster,url) VALUES ($noqmovie,$publication,\"".$imdb."\",\"".$poster."\",\"".$url."\")";
			$rep=mysqli_query($link,$sql);
			$cpt_movies++;
			$sql="SELECT id FROM movies WHERE qwd=$noqmovie";
			$rep=mysqli_query($link,$sql);
			$row = mysqli_fetch_assoc($rep);
			$id_movie=$row['id'];
			insert_label_page(1,$noqmovie,$id_movie);
			
			$tab_multi=array(57,136,161,364,495);	
			for ($i=0;$i<count($tab_multi);$i++){
				if ($claims["P".$tab_multi[$i]]){
					foreach ($claims["P".$tab_multi[$i]] as $value){
						$val=intval($value["mainsnak"]["datavalue"]["value"]["numeric-id"]);
						$sql="SELECT id FROM p".$tab_multi[$i]." WHERE qwd=$val";
						$rep=mysqli_query($link,$sql);
						if (mysqli_num_rows($rep)==0){
							$sql="INSERT INTO p".$tab_multi[$i]." (qwd) VALUES ($val)";
							$rep=mysqli_query($link,$sql);
							$sql="SELECT id FROM p".$tab_multi[$i]." WHERE qwd=$val";
							$rep=mysqli_query($link,$sql);
							$row = mysqli_fetch_assoc($rep);
							$id_prop=$row['id'];
							insert_label_page($tab_multi[$i],$val,$id_prop);
						}
						else{			
							$row = mysqli_fetch_assoc($rep);
							$id_prop=$row['id'];
						}
						$sql="INSERT INTO artw_prop (prop,id_artw,id_prop) VALUES (".$tab_multi[$i].",$id_movie,$id_prop)";
						$rep=mysqli_query($link,$sql);
					}
				}
			}
			
			for ($i=0;$i<count($p144);$i++){
				$qbook=$p144[$i];
				$noqbook=str_replace("Q","",$qbook);
				$sql="SELECT id FROM books WHERE qwd=$noqbook";
				$rep=mysqli_query($link,$sql);
				if (mysqli_num_rows($rep)==0){	
					$dfic=get_WDjson($noqbook,"books");
					$data = json_decode($dfic,true);
					$varlab=$data["entities"][$qbook];
					$claims=$varlab["claims"];
					$creation="null";
					if ($claims["P571"])
						foreach ($claims["P571"] as $valprop)
							$creation=substr($valprop["mainsnak"]["datavalue"]["value"]["time"],1,4);
					$publication="null";
					if ($claims["P577"])
						foreach ($claims["P577"] as $valprop)
							$publication=substr($valprop["mainsnak"]["datavalue"]["value"]["time"],1,4);
					foreach ($claims["P268"] as $valprop)
						$bnf=$valprop["mainsnak"]["datavalue"]["value"];
					$url=thumb($bnf);
					
					$sql="INSERT INTO books (qwd,creation,publication,bnf,url) VALUES ($noqbook,$creation,$publication,\"".$bnf."\",\"".$url."\")";
					$rep=mysqli_query($link,$sql);
					$cpt_books++;
					$sql="SELECT id FROM books WHERE qwd=$noqbook";
					$rep=mysqli_query($link,$sql);
					$row = mysqli_fetch_assoc($rep);
					$id_book=$row['id'];
					insert_label_page(2,$noqbook,$id_book);	
						
					$tab_multi=array(50,136);	
					for ($i=0;$i<count($tab_multi);$i++){
						if ($claims["P".$tab_multi[$i]]){
							foreach ($claims["P".$tab_multi[$i]] as $value){
								$val=intval($value["mainsnak"]["datavalue"]["value"]["numeric-id"]);
								$qprop=$tab_multi[$i];
								if ($tab_multi[$i]==136)
									$qprop=9136;
								$sql="SELECT id FROM p".$qprop." WHERE qwd=$val";
								$rep=mysqli_query($link,$sql);
								if (mysqli_num_rows($rep)==0){
									$sql="INSERT INTO p".$qprop." (qwd) VALUES ($val)";
									$rep=mysqli_query($link,$sql);
									$sql="SELECT id FROM p".$qprop." WHERE qwd=$val";
									$rep=mysqli_query($link,$sql);
									$row = mysqli_fetch_assoc($rep);
									$id_prop=$row['id'];
									insert_label_page($qprop,$val,$id_prop);
								}
								else{			
									$row = mysqli_fetch_assoc($rep);
									$id_prop=$row['id'];
								}
								$sql="INSERT INTO artw_prop (prop,id_artw,id_prop) VALUES (".$qprop.",$id_book,$id_prop)";
								$rep=mysqli_query($link,$sql);
							}
						}
					}
				}
				else{			
					$row = mysqli_fetch_assoc($rep);
					$id_book=$row['id'];
				}
				$sql="INSERT INTO artw_prop (prop,id_artw,id_prop) VALUES (144,$id_movie,$id_book)";
				$rep=mysqli_query($link,$sql);
			}
		}
	}
}
$cat_path=$fold."harvest/data.json";
$cat = fopen($cat_path, 'w');
fputs ($cat, json_encode($catmovies));
fclose($cat);

echo "\nHarvest done - $cpt_movies movies - $cpt_books books - ";

list($g2_usec, $g2_sec) = explode(" ",microtime());
define ("t_end", (float)$g2_usec + (float)$g2_sec);
print round (t_end-t_start, 1)." secondes"; 
?>
