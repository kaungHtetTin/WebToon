<?php
    include_once('../classes/connect.php');
    include_once('../classes/series.php');
    
    $series_id = $_GET['series_id'];
    
    // Use Series class to get series with categories
    $Series = new Series();
    $result = $Series->details(['id' => $series_id]);
    
    if($result){
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Series not found']);
    }
    
?>