<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/auth.php');
    include_once('../../classes/user.php');
    include_once('../../classes/jwt.php');
    include_once('../../classes/util.php');

    // email and password are required in request;
   
    $Auth=new Auth();
    $Util = new Util();
    if($_SERVER['REQUEST_METHOD']=="POST"){
        $error=$Auth->login($_POST);
        if($error=="") {
            $User=new User();
            $user=$User->details($_SESSION['webtoon_userid']);

            $header = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = ['userId' =>$user['id'], 'username' =>$user['first_name'].' '.$user['first_name']];
            
            $user = [
                'user_id'=>$user['id'],
                'first_name'=>$user['first_name'],
                'last_name'=>$user['last_name'],
                'email'=>$user['email'],
                'phone'=>$user['phone'],
                'image_url'=>$user['image_url'],
                'point'=>$user['point'],
            ];

            $JWT = new JWT();
            $jwt =  $JWT->createJWT($header, $payload);

            $response['status']="success";
            $response['user']=$user;
            $response['_token']=$jwt;
            $response['mobile_app_version_code']=$Util->mobileAppVersionCode();

            echo json_encode($response);
            
        }else{
            $res['status']="fail";
            $res['error']=$error;
            echo json_encode($res);
        }
    }
?>