<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/category.php');

  
    $Series = new Series();
 
    $series=$Series->search($_GET);
 
    if($series){
        $response['status']="success";
        $response['series']= $series;
    }else{
        $response['status']="fail";
        $response['msg']= "No result was found";
    }
    echo json_encode($response);
?>