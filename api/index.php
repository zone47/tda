<?php
/* / */
include "config.php";
include "functions.php";
include "open_conn.php";
include "init.php";
include "query.php";

$results=array();
while($data = mysqli_fetch_assoc($rep)) {
	$result=array();
	$id=$data['id'];
	$result['id']=$data['id'];
	$qwd=$data['qwd'];
	$result['qwd']="Q".$data['qwd'];
	if ($type=="movies"){
		$result['label']=label_item($qwd);
		$result['director']=list_get($id,57);
		$result['starring']=list_get($id,161);
		$result['date']=$data['publication'];
		$result['genre']=list_get($id,136);
		$result['country']=list_get($id,495);
		$result['language']=list_get($id,364);
		$result['imdb']=$data['imdb'];
		$result['poster']=$data['poster'];
		$result['url']=$data['url'];
		$result['books']=array();
		$ids_books=val_prop($id,144);
		for ($i=0;$i<count($ids_books);$i++){
			$book=array();
			$sql="SELECT * FROM books WHERE qwd=".$ids_books[$i];
			$rep_book=mysqli_query($link,$sql);
			$num_rows= mysqli_num_rows($rep_book);
			if ($num_rows!=0){
				$data_book = mysqli_fetch_assoc($rep_book);
				$id_book=$data_book['id'];
				$qwd_book=$data_book['qwd'];
				$book["id"]=$id_book;
				$book["qwd"]=$qwd_book;
				$book["title"]=label_item($qwd_book);
				$book["author"]=list_get($id_book,50);
				$book["publication"]=$data_book['publication'];
				$book["genre"]=list_get($id_book,9136);
				$book["thumbnail"]=$data_book['url'];
				$book["bnf"]=$data_book['bnf'];
				$result['books'][]=$book;
			}
		}
	}
	else{
		$result['title']=label_item($qwd);
		$result['author']=list_get($id,50);
		$result['publication']=$data['publication'];
		$result["genre"]=list_get($id,9136);
		$result['thumbnail']=$data['url'];
		$result['bnf']=$data['bnf'];
		$result['movies']=array();
		$ids_movies=val_prop($id,144);
		$sql="SELECT * FROM movies,artw_prop WHERE artw_prop.prop=144 AND artw_prop.id_prop=".$id." AND movies.id=artw_prop.id_artw";
		$rep_movie=mysqli_query($link,$sql);
		$num_rows= mysqli_num_rows($rep_movie);
		if ($num_rows!=0){
			while ($data_movie = mysqli_fetch_assoc($rep_movie)){
				$movie=array();
				$id_movie=$data_movie['id'];
				$qwd_movie=$data_movie['qwd'];
				$movie["id"]=$id_movie;
				$movie["qwd"]=$qwd_movie;
				$movie['label']=label_item($qwd_movie);
				$movie['director']=list_get($id_movie,57);
				$movie['starring']=list_get($id_movie,161);
				$movie['date']=$data_movie['publication'];
				$movie['genre']=list_get($id_movie,136);
				$movie['country']=list_get($id_movie,495);
				$movie['language']=list_get($id_movie,364);
				$movie['imdb']=$data_movie['imdb'];
				$movie['poster']=$data_movie['poster'];
				$movie['url']=$data_movie['url'];
				$result['movies'][]=$movie;
			}
		}
	}
	$results[]=$result;
}
include "close_conn.php";
//print_r($results);
header('Content-Type: application/json');
echo json_encode($results);

?>