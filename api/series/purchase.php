<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/jwt.php');
    
    $requestHeaders = apache_request_headers();
    $_token = $requestHeaders['Authorization'];

    $JWt = new JWt();
    $payload = $JWt->validateJWT($_token);
   
    if(!$payload){
        $response['status']="fail";
        $response['msg']="Authorization Fail";
    }

    $Series = new Series();
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $purchase = $Series-> saveSeriesByUser($_POST);
        if($purchase){
            $response['status']="success";
            $response['msg']="Authorization Fail";
        }else{
            $response['status']="fail";
            $response['msg']="Purchase Error";
        }
    }else{
        $response['status']="success";
        $response['msg']="Method not allow";
    }

    echo json_encode($response);
    
?>