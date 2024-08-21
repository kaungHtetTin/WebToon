<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/chapter.php');
    include_once('../../classes/jwt.php');

    $chapter_id = $_GET['chapter_id'];

    $JWT = new JWT();

    $requestHeaders = apache_request_headers();
    $jwt_auth_token =$requestHeaders['Authorization'];

    $Chapter = new Chapter();

    $user = $JWT->validateJWT($jwt_auth_token);

    if($user){
        $user_id = $user['userId'];
        $result=$Chapter->download($chapter_id,$user_id);

        echo json_encode($result);
    }else{  
        $reponse =['status'=>'Fail','message'=>'Cannot Authorize','code'=>3];
        echo json_encode($reponse);
    }


?>