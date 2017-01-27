<?php
/* / */
echo "\nSubs and parts of";
$test=false;
if ($test){
	include "functions.php";
	include "config_harvest.php";
}

$link = mysqli_connect ($host,$user,$pass,$db) or die ('Erreur : '.mysqli_error());

$tab_props=array(50,57,136,144,161,364,495,9136);
for ($i=0;$i<count($tab_props);$i++){
	$prop=$tab_props[$i];
	echo "\ntable p".$prop;
	$sql="SELECT id, qwd from p$prop";
	if ($prop==144)
		$sql="SELECT id, qwd from books";
	$rep=mysqli_query($link,$sql);

	while($data = mysqli_fetch_assoc($rep)) {
		$id_prop=$data['id'];
		$qwd=$data['qwd'];
		$sub_query="";
		if (($prop==136)||($prop==9136)){
			$res = get_query($prop,$qwd);
			if (count($res)>0){
				for ($j=0;$j<count($res);$j++){
					$sql="SELECT id from p$prop WHERE qwd=".$res[$j];
					$rep2=mysqli_query($link,$sql);
					if (mysqli_num_rows($rep2)>0){
						$row = mysqli_fetch_assoc($rep2);
						$id_sub=$row['id'];	
						$rep3=mysqli_query($link,"INSERT INTO prop_sub (prop,id_prop,id_sub) VALUES (".$prop.",".$id_prop.",".$id_sub.") ");
						$sub_query.=" OR id_prop=".$id_sub;
					}
				}
			}
		}

		$sql="SELECT count(distinct id_artw) as total from artw_prop  WHERE prop=$prop and (id_prop=".$id_prop.$sub_query.")";
		if ($test) echo  "<br />".$sql;
		$rep2=mysqli_query($link,$sql);
		$data2=mysqli_fetch_assoc($rep2);
		$nbartworks=$data2['total'];
		
		$sql="UPDATE p$prop SET nb=".$nbartworks." WHERE id=".$id_prop;
		if ($prop==144)
			$sql="UPDATE books SET nb=".$nbartworks." WHERE id=".$id_prop;
		if ($test) echo  "<br />".$sql;
		mysqli_query($link,$sql);
	}
}

$sql="ALTER TABLE `label_page` ADD INDEX(`id_art_or_prop`)";
$rep=mysqli_query($link,$sql);

for ($i=0;$i<count($tab_props);$i++){
	$prop=$tab_props[$i];
	echo "\ntable p".$prop;
	$sql="SELECT id,nb FROM p".$prop;
	if ($prop==144){
		$sql="SELECT id,nb FROM books";
		$prop=2;
	}
	$rep=mysqli_query($link,$sql);
	while($data = mysqli_fetch_assoc($rep)) {
		$sql="UPDATE label_page SET nb=".$data["nb"]." WHERE id_art_or_prop=".$data["id"]." AND prop=".$prop;
		mysqli_query($link,$sql);
	}
}
$sql="ALTER TABLE label_page DROP INDEX id_art_or_prop;";
$rep=mysqli_query($link,$sql);

$sql="UPDATE label_page SET nb=1 WHERE prop=1";
mysqli_query($link,$sql);

mysqli_close($link);

echo "\nOccurences done";

?>