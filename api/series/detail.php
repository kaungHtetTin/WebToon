<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/visit.php');

    $id = $_GET['id'];
    $Series = new Series();
    $series=$Series->details($_GET);
    $myRating = 0;
    
    if(isset( $_GET['user_id'])){
        $user_id =  $_GET['user_id'];
        $Visit = new Visit();
        $Visit->add($user_id,$id);
        if($Series->isSaved($user_id,$id)){
            $series['saved']=true;
        }else{
            $series['saved']=false;
        }

        $myRating=$Series->getMyRating($user_id,$id);
        $series['my_rating'] = $myRating;

    }else{
        $series['saved']=false;
    }

    echo json_encode($series);

?>