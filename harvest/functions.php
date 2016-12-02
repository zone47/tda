<?php
function getSparQL($query){
	$sparqlurl=urlencode($query);
	$req="https://query.wikidata.org/sparql?format=json&query=".$sparqlurl;
	$res  = file_get_contents($req);
	return json_decode($res,true);	
}
function get_WDjson($wdq,$type="others"){
	if (strtoupper(substr($wdq,0,1))!="Q") $wdq="Q".$wdq;
	global $fold;
	$qitem_path=$fold."harvest/items/".$type."/".$wdq.".json";
	if (file_exists($qitem_path))
		return file_get_contents($qitem_path,true);
	else{
		copy("https://www.wikidata.org/w/api.php?action=wbgetentities&ids=".$wdq."&format=json", $qitem_path);
		return file_get_contents($qitem_path,true);
	}
}
function dataToGallica($BnFID){
	global $fold;
	$BnF_path=$fold."harvest/bnf/".$BnFID.".txt";
	if (file_exists($BnF_path))
		return file_get_contents($BnF_path,true);
	else{
		$queryBnF="http://data.bnf.fr/sparql?default-graph-uri=&query=SELECT+DISTINCT+%3Fgallica+WHERE+%7B%3Fmanif+rdarelationships%3AworkManifested+%3Chttp%3A%2F%2Fdata.bnf.fr%2Fark%3A%2F12148%2Fcb".$BnFID."%23frbr%3AWork%3E%3B+rdarelationships%3AelectronicReproduction+%3Fgallica%7D+LIMIT+1&format=json&timeout=0&should-sponge=&debug=on";
		$res=file_get_contents($queryBnF,true);
		$responseArray3=json_decode($res,true);
		$GallicaID="";
		foreach ($responseArray3["results"]["bindings"] as $key3 => $value3)
			if ($value3["gallica"]["value"]){
				$GallicaID=$value3["gallica"]["value"];
		}
		if ($GallicaID!=""){
			$ficBnF = fopen($BnF_path, 'w');
			fputs ($ficBnF, $GallicaID);
			fclose($ficBnF);
		}
		return $GallicaID;
	}
}
function label($qwd,$l="fr",$type="others"){
	if (strtoupper(substr($qwd,0,1))!="Q") $qwd="Q".$qwd;
	global $fold;
	$qitem_path=$fold."harvest/items/".$type."/".$qwd.".json";
	if (!(file_exists($qitem_path)))
		copy("https://www.wikidata.org/w/api.php?action=wbgetentities&ids=".$qwd."&format=json", $qitem_path);
	$dfic =file_get_contents($qitem_path,true);
	$data_item=json_decode($dfic,true);
	$ent_qwd=$data_item["entities"][$qwd]["labels"];
	$label="";
	if ($ent_qwd[$l]["value"])
		$label=$ent_qwd[$l]["value"];
	else{
		if ($ent_qwd["en"]["value"])
			$label=$ent_qwd["en"]["value"];
		else{
			if ($ent_qwd)
				$label=$ent_qwd[key($ent_qwd)]["value"];
		}
	}
	if ($label!="")
		return $label;
	else 
		return $qwd;

}
function poster($id){
	global $fold;
	$imdb_path=$fold."harvest/imdb/".$id.".txt";
	if (file_exists($imdb_path))
		return file_get_contents($imdb_path,true);
	else{
		$queryimdb =file_get_contents("http://www.omdbapi.com/?i=".$id,true);
		$data_imdb=json_decode($queryimdb,true);
		$url_poster="";
		if ($data_imdb["Poster"])
			if ($data_imdb["Poster"]!="N/A")
				$url_poster=$data_imdb["Poster"];
		$ficIMDb = fopen($imdb_path, 'w');
		fputs($ficIMDb,$url_poster);
		fclose($ficIMDb);
		return $url_poster;
	}
}
function thumb($IDBnF){
	global $fold;
	$bnf_path=$fold."harvest/bnf/".$IDBnF.".txt";
	if (file_exists($bnf_path))
		return file_get_contents($bnf_path,true).".highres";
	else
		return "";
}
function esc_dblq($text){
	return str_replace("\"","\\\"",$text);
}
function insert_label_page($prop,$val_item,$id_art_or_prop){
	global $lg,$link; 
	$type="others";
	if ($prop==1)
		$type="movies";
	elseif ($prop==2)
		$type="books";
	$dfic=get_WDjson($val_item,$type);
		
	$data_item=json_decode($dfic,true);
	$ent_qwd=$data_item["entities"]["Q".$val_item];

	// label
	$lab=label($val_item,$lg,$type);
	
	if ($lab!=""){
		$sql="INSERT INTO label_page (prop,qwd,label,id_art_or_prop) VALUES ($prop,$val_item,\"".esc_dblq($lab)."\",$id_art_or_prop)";
		$rep=mysqli_query($link,$sql);
	}
}

?>