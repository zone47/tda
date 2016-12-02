<?php
/* / */
function label_item($qwd){
	global $link;
	$label="";
    $sql="SELECT label from label_page WHERE qwd=$qwd AND label!='' LIMIT 0,1";
	$rep_lab=mysqli_query($link,$sql);
	$num_rows= mysqli_num_rows($rep_lab);
	if ($num_rows!=0){
		$data_lab = mysqli_fetch_assoc($rep_lab);
		$label=$data_lab['label'];
	}	
	return $label;
}
function val_prop($id_artw,$prop){
	global $link;
	$vals=array();
	if ($prop!=144)
		$sql="SELECT p".$prop.".qwd as prop_qwd from artw_prop,p".$prop." WHERE artw_prop.prop=".$prop." AND  artw_prop.id_artw=$id_artw AND  artw_prop.id_prop=p".$prop.".id";
	else
		$sql="SELECT books.qwd as prop_qwd from artw_prop,books WHERE artw_prop.prop=".$prop." AND  artw_prop.id_artw=$id_artw AND  artw_prop.id_prop=books.id";
	$rep_prop=mysqli_query($link,$sql);
	$num_rows= mysqli_num_rows($rep_prop);
	if ($num_rows!=0){
		while ($data_prop = mysqli_fetch_assoc($rep_prop))
			$vals[]=intval($data_prop['prop_qwd']);
	}
	return $vals;
}
function list_get($id,$id_prop){
	$values=val_prop($id,$id_prop);
	$values=array_unique($values);		
	$res=array();
	if (count($values)>0){
		for ($i=0;$i<count($values);$i++){
			if (isset($values[$i])&&($values[$i]!=0)){
				$item_res=array();
				$item_res["qwd"]="Q".$values[$i];
				$item_res["label"]=label_item($values[$i]);
				$res[]=$item_res;
			}
		}
	}
	return $res;
}
?>