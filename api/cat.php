<?php
include "config.php";
include "functions.php";

$prop = $_GET['cat'];
$limit=50;
if (isset($_GET['limit']))
	$limit = $_GET['limit'];
$props = array(
	"author" => "50",
	"director" => "57",
	"genre_movie" => "136",
	"starring" => "161",
	"language" => "364",
	"country" => "495",
	"genre_book" => "9136"
);


$res=array();
if ($props[$prop]!=""){
	$link = mysqli_connect ($host,$user,$pass,$db) or die ('Erreur : '.mysqli_error());
	mysqli_query($link,"SET NAMES 'utf8'");
	$sql = "SELECT qwd,nb FROM p".$props[$prop]." ORDER BY nb DESC LIMIT 0, ".$limit;
	$rep=mysqli_query($link,$sql);
	while ($rs = mysqli_fetch_assoc($rep)){
		$sg_res=array(
			"qwd"=> $rs['qwd'],
			"label"=> label_item($rs['qwd']),
			"nb"=> $rs['nb']
		);
		$res[]=$sg_res;
	}
}
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo json_encode($res);

?>