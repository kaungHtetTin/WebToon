<?php 
include("classes/connect.php");
$DB = new Database();

$query = "SELECT * FROM chapters WHERE id<716";
$chapters = $DB->read($query);
foreach($chapters as $chapter){
    $id = $chapter['id'];
    $download_url = $chapter['download_url'];
    
    echo $download_url."<br>";
    
}


?>