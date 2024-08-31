<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/chapter.php');
    include_once('../../classes/jwt.php');

    $chapter_id = $_GET['chapter_id'];
    $series_id = $_GET['series_id'];

    $Series = new Series();
    $Chapter = new Chapter();

    $series = $Series->details(array('id'=>$series_id));
    $chapter = $Chapter->details(array('id'=>$chapter_id));
    $contents = $Chapter->getContent($chapter_id);


    $JWT = new JWT();
    $requestHeaders = apache_request_headers();
    $jwt_auth_token =$requestHeaders['Authorization'];
    $user = $JWT->validateJWT($jwt_auth_token);
    $isSaved = false;

    if($user){
        $user_id = $user['userId'];
        $isSaved=$Series->isSaved($user_id,$series_id);
    }

    if($series['point']==0||$chapter['is_active']==0||$isSaved){
        $response['status']="success";
        $response['contents']=$contents;
    }else{
        $response['status']="fail";
        $response['msg']="Access Denied!";
    }

    echo json_encode($response);

?>