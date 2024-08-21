<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/payment.php');
    include_once('../../classes/jwt.php');


    if($_SERVER['REQUEST_METHOD']=="POST"){
        $Payment = new Payment();
        $JWT = new JWT();

        $requestHeaders = apache_request_headers();
        $jwt_auth_token =$requestHeaders['Authorization'];

        $user = $JWT->validateJWT($jwt_auth_token);

        if($user){
            $result = $Payment->add($_POST,$_FILES);
            echo json_encode($result);
        }else{  
            $reponse =['status'=>'Fail','message'=>'Cannot Authorize'];
            echo json_encode($reponse);
        }
    }else{
        $reponse =['status'=>'Fail','message'=>'Method Not Allow!'];
        echo json_encode($reponse);
    }

?>