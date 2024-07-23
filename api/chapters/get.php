<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/chapter.php');

    $series_id = $_GET['series_id'];
    
    $Chapter = new Chapter();
    $chapters = $Chapter->get($series_id);

    echo json_encode($chapters);

?>