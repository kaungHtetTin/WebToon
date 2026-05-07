<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/jwt.php');
    
    $requestHeaders = apache_request_headers();
    $_token = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : '';

    $JWt = new JWt();
    $payload = $JWt->validateJWT($_token);

    if(!$payload){
        $response['status']="fail";
        $response['msg']="Authorization Fail";
        echo json_encode($response);
        exit;
    }

    if($_SERVER['REQUEST_METHOD']!="POST"){
        $response['status']="fail";
        $response['msg']="Method not allow";
        echo json_encode($response);
        exit;
    }

    $Series = new Series();
    $purchase_data = $_POST;
    $purchase_data['user_id'] = $payload['userId'];
    $purchase = $Series->saveSeriesByUser($purchase_data);
    if($purchase){
        $response['status']="success";
        $response['msg']="Purchase success";
    }else{
        $response['status']="fail";
        $response['msg']="Purchase Error";
    }

    echo json_encode($response);
    
?>