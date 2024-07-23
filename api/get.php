<?php
    include_once('../classes/connect.php');
    include_once('../classes/series.php');
    include_once('../classes/category.php');

  
    $Series = new Series();
    $Category = new Category();
    $categories = $Category->get();

    $series=$Series->index($_GET);
    $series['categories'] =$categories;
    echo json_encode($series);
?>