<?php 
/* / */
set_time_limit(120);
$l="fr"; 
$s=""; // Search
$y1=-2000;
$y2=2017;
$random=false;
$nb_random=20;
$q=""; // Wikidata item
$bnf="";
$imdb="";
$type="movies";
if (isset($_GET["type"]))
	$type=$_GET["type"];	
if (isset($_GET["q"]))
	$q=str_ireplace("q","",$_GET["q"]);	
if (isset($_GET["bnf"])){
	$bnf=str_ireplace("cb","",$_GET["bnf"]);
	$type="books";
}
if (isset($_GET["imdb"])){
	$imdb=$_GET["imdb"];	
	$type="movies";
}
if (isset($_GET["nb"]))
	$nb_random=$_GET["nb"];	
$tab_idx = array(
	"p50" => "",// author
	"p57"=> "",// director
	"p136"=> "",// movie genre
	"p161"=> "",// starring 
	"p364"=> "",// language
	"p495"=> "",// country
	"p9136"=> "",// book genre
);
if (isset($_GET["author"]))
	$tab_idx["p50"]=str_ireplace("q","",$_GET["author"]);	
if (isset($_GET["director"]))
	$tab_idx["p57"]=str_ireplace("q","",$_GET["director"]);	
if (isset($_GET["starring"]))
	$tab_idx["p161"]=str_ireplace("q","",$_GET["starring"]);	
if (isset($_GET["language"]))
	$tab_idx["p364"]=str_ireplace("q","",$_GET["language"]);	
if (isset($_GET["country"]))
	$tab_idx["p495"]=str_ireplace("q","",$_GET["country"]);	
if (isset($_GET["genre"])){
	if ($type=="movies")
		$tab_idx["p136"]=str_ireplace("q","",$_GET["genre"]);
	else
		$tab_idx["p9136"]=str_ireplace("q","",$_GET["genre"]);
}
if (isset($_GET["p136"])){
	if ($type=="books"){
		$tab_idx["p9136"]=$_GET["p136"];
		$_GET["p136"]="";
	}
}
		
foreach($tab_idx as $key=>$value)
	if (isset($_GET[$key]))
		$tab_idx[$key]=str_ireplace("q","",$_GET[$key]);	

if (isset($_GET['s'])){
	$s=$_GET['s'];
	$s= preg_replace('/\p{C}+/u', "", $s);
	$s=trim(str_replace("\"","",urldecode($s)));
}

if (isset($_GET['y1']))
	if (is_int(intval($_GET['y1']))) 
		$y1=intval($_GET['y1']);
if (isset($_GET['y2']))
	if (is_int(intval($_GET['y2']))) 
		$y2=intval($_GET['y2']);
if (isset($_GET['period'])){
	$years=split("-",$_GET['period']);
	if ((count($years)==2)&&(is_int(intval($years[0])))&&(is_int(intval($years[1])))){
		$y1=intval($years[0]);
		$y2=intval($years[1]);
	}
}
	
?>