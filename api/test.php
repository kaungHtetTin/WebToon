<?php
    include_once('../classes/connect.php');
    $series_id = $_GET['series_id'];
    $query = "SELECT * FROM series WHERE id=$series_id";
 
    $DB = new Database();
    $result = $DB->read($query);

    echo json_encode($result);
    
?>