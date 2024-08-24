<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/jwt.php');


    if($_SERVER['REQUEST_METHOD']=="POST"){
        $Series = new Series();
        $JWT = new JWT();

        $requestHeaders = apache_request_headers();
        $jwt_auth_token =$requestHeaders['Authorization'];

        $user = $JWT->validateJWT($jwt_auth_token);

        if($user){
            // rate the series
            $req['user_id'] = $user['userId'];
            $req['series_id'] = $_POST['series_id'];
            
            $Series->deleteRating($req);
            $reponse = ['status'=>'success'];
            echo json_encode($reponse);

        }else{  
            $reponse =['status'=>'Fail','message'=>'Cannot Authorize'];
            echo json_encode($reponse);
        }
    }else{
        $reponse =['status'=>'Fail','message'=>'Method Not Allow!'];
        echo json_encode($reponse);
    }

?>