<?php
$props = array(
	"1" => "movie",
	"2" => "book",
	"50" => "author",
	"57" => "director",
	"136" => "genre (movie)",
	"161" => "starring",
	"364" => "language",
	"495" => "country",
	"9136" => "genre (book)"
);

include "config.php";
$link = mysqli_connect ($host,$user,$pass,$db) or die ('Erreur : '.mysqli_error());
mysqli_query($link,"SET NAMES 'utf8'");

$keyword = $_GET['keyword'];
$img="img";
$cpt=0;
$ls ="";
$sql = "SELECT prop, qwd, label, id_art_or_prop FROM label_page WHERE label LIKE '%".$keyword ."%' AND nb!=0 GROUP BY prop, qwd ORDER BY nb DESC LIMIT 0, 5";
$rep=mysqli_query($link,$sql);
$res=array();
while ($rs = mysqli_fetch_assoc($rep)){
	$img="";
	if (($rs['prop']==1)||($rs['prop']==2)){
		if ($rs['prop']==1)
			$sql="SELECT poster as img from movies where id=".$rs['id_art_or_prop'];
		else
			$sql="SELECT url as img from books where id=".$rs['id_art_or_prop'];
		$rep2=mysqli_query($link,$sql);
		while ($data2 = mysqli_fetch_assoc($rep2))
			$img=str_replace(".highres",".thumbnail",$data2['img']);
			
	}
	$sg_res=array(
		"prop"=> $props[$rs['prop']],
		"qwd"=> $rs['qwd'],
		"label"=> $rs['label'],
		"img"=> $img
	);
	$res[]=$sg_res;
}
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo json_encode($res);

?>