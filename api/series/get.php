<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/category.php');

    $page = $_GET['page'];
    $q = $_GET['q'];

    $Series = new Series();
    $Category = new Category();

    $categories = $Category->get();

    if($q=="popular"){
        $series=$Series->getPopularSeries($_GET);
    }else if($q=="trending"){
        $series=$Series->getTendingSeries($_GET);
    }else if($q=="recent"){
        $series=$Series->getNewSeries($_GET);
    }else{
        $series=$Series->getSeriesByCategory($_GET);
    }
    $series['categories'] =$categories;

   // $requestHeaders = apache_request_headers();
   
   // print_r($requestHeaders['Authorization']);

   echo json_encode($series);

?>