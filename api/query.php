<?php 
/* / */
$sql="";
$res_s=array();
$prim_query=true;
$search_query=false;
if ($s!=""){	
	$tab_keywords=explode(" ",$s);
	$stopwords=array("a", "an", "and", "in", "of", "on", "or", "so", "the", "to", "up","à","au", "de", "de", "des", "du", "en", "et", "la", "le", "les", "ou","un","une");
	for ($i=0;$i<count($tab_keywords);$i++){
		for ($j=0;$j<count($stopwords);$j++){
			if ($tab_keywords[$i]==$stopwords[$j])
				array_splice($tab_keywords,$i,1);
		}
	}
	if ($type=="movies")
		$prop=1;
	else
		$prop=2;
	for ($i=0;$i<count($tab_keywords);$i++){
		if (($tab_keywords[$i]!="")&&(strlen($tab_keywords[$i])>2)){
			$search_query=true;
			$sql_s="
			(
			SELECT distinct ".$type.".id as id
			FROM label_page, artw_prop, ".$type."
			WHERE label_page.prop !=1
			AND label_page.prop !=2
			AND label_page.label LIKE \"%".$tab_keywords[$i]."%\"
			AND label_page.id_art_or_prop = artw_prop.id_prop
			AND label_page.prop = artw_prop.prop
			AND artw_prop.id_artw = ".$type.".id)
			UNION (
			SELECT distinct ".$type.".id as id
			FROM label_page, ".$type."
			WHERE label_page.label LIKE \"%".$tab_keywords[$i]."%\"
			AND label_page.id_art_or_prop = ".$type.".id
			AND label_page.prop =".$prop.")";

			$rep_s=mysqli_query($link,$sql_s);
			$new_s="";
			while($data_s = mysqli_fetch_assoc($rep_s)) {
				if ($prim_query){
					$res_s[]=$data_s['id'];
					if ($new_s!="")
						$new_s.=";";
					$new_s.=$data_s['id'];
				}
				else
					if (in_array ($data_s['id'],$res_s)){
						if ($new_s!="")
							$new_s.=";";
						$new_s.=$data_s['id'];
					}
			}
			unset($res_s);
			$res_s=array();
			if ($prim_query)
				$prim_query=false;
			if ($new_s!="")
				$res_s=explode(";",$new_s);
			else
				break;
		}
	}
}

foreach($tab_idx as $key=>$value){
	if ($value!=""){
		$search_query=true;
		
		$where="(".$key.".qwd=".$value;
		$sql_sub="SELECT id FROM ".$key." WHERE ".$key.".qwd=".$value;
		$rep_sub=mysqli_query($link,$sql_sub);
		while($data = mysqli_fetch_assoc($rep_sub))
			$where.=" OR ".$key.".id=".$data['id'];
		$where.=")";
		
		$sql_s="select distinct ".$type.".id as id 
		from ".$key.", artw_prop, ".$type." WHERE ".$where." AND artw_prop.id_prop=".$key.".id AND artw_prop.prop=".str_replace("p","",$key)." AND ".$type.".id=artw_prop.id_artw";
		$rep_s=mysqli_query($link,$sql_s);
		$new_s="";

		
		while($data_s = mysqli_fetch_assoc($rep_s)) {
			if ($prim_query){
				$res_s[]=$data_s['id'];
				if ($new_s!="")
						$new_s.=";";
					$new_s.=$data_s['id'];
			}
			else
				if (in_array ($data_s['id'],$res_s)){
					if ($new_s!="")
						$new_s.=";";
					$new_s.=$data_s['id'];
				}
		}
		unset($res_s);
		$res_s=array();
		if ($new_s!="")
			$res_s=explode(";",$new_s);
		else
			break;
		if ($prim_query)
			$prim_query=false;
	}
}
$search_date=false;
if (!(($y1==-2000)&&($y2==2017)))
	$search_date=true;
if (($search_query)||($search_date)){
	if (($search_query)&&(!(count($res_s)>0)))
		$sql="SELECT * from ".$type." WHERE id=0";
	else{
		$sql="SELECT * from ".$type." WHERE ";
		$sql_c="";
		for ($i=0;$i<count	($res_s);$i++){
			if ($sql_c!="")
				$sql_c.=" OR ";
			else
				$sql_c.="(";
			$sql_c.="id=".$res_s[$i];
		}
		if ($sql_c!="")
			$sql_c.=")";
		if ($search_date){
			if ($sql_c!="")
				$sql_c.=" AND ";
			$sql_c.=" publication>=$y1 AND publication<=$y2 ";
		}
		$sql.=$sql_c;
	}
}

if ($q!=""){
	$random=false;
	$sql="SELECT * from ".$type." WHERE qwd=$q";
}
if ($bnf!=""){
	$random=false;
	$sql="SELECT movies.id from books,movies,artw_prop WHERE books.bnf=\"".$bnf."\" AND artw_prop.id_prop=books.id AND artw_prop.prop=144 AND movies.id=artw_prop.id_artw";
}
if ($imdb!=""){
	$random=false;
	$sql="SELECT books.id from books,movies,artw_prop WHERE movies.imdb=\"".$imdb."\" AND artw_prop.id_prop=books.id AND artw_prop.prop=144 AND movies.id=artw_prop.id_artw";
}
if ($sql==""){
	$random=true;
}
if ($random){
	$sql="SELECT * from ".$type." ORDER BY RAND() LIMIT 0,".$nb_random;
	$num_rows=$nb_random;
}
$rep=mysqli_query($link,$sql);
$num_rows_ec = mysqli_num_rows($rep);
?>