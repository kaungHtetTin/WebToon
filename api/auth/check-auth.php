<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/auth.php');
    include_once('../../classes/user.php');
    include_once('../../classes/jwt.php');
    include_once('../../classes/util.php');
    
    $requestHeaders = apache_request_headers();
    $_token = $requestHeaders['Authorization'];

    $JWt = new JWt();
    $Util = new Util();

    $payload = $JWt->validateJWT($_token);
    if($payload){
        $user_id = $payload['userId'];

        $User=new User();
        $user=$User->details($user_id );

        $user = [
            'user_id'=>$user['id'],
            'first_name'=>$user['first_name'],
            'last_name'=>$user['last_name'],
            'email'=>$user['email'],
            'phone'=>$user['phone'],
            'image_url'=>$user['image_url'],
            'point'=>$user['point'],
        ];

        $response['auth']="success";
        $response['mobile_app_version_code']=$Util->mobileAppVersionCode();
        $response['user']=$user;
    }else{
        $response['auth']="fail";
    }

    echo json_encode($response);

?>