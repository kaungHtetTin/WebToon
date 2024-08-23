<?php 
include_once('../../classes/connect.php');
include_once('../../classes/jwt.php');
include_once('../../classes/user.php');

if($_SERVER['REQUEST_METHOD']=="POST"){
        $User = new User();
        $JWT = new JWT();

        $requestHeaders = apache_request_headers();
        $jwt_auth_token =$requestHeaders['Authorization'];

        $user = $JWT->validateJWT($jwt_auth_token);

        if($user){
           $req['user_id'] = $user['userId'];
           $req['first_name'] = $_POST['first_name'];
           $req['last_name'] = $_POST['last_name'];
           $req['phone'] = $_POST['phone'];

           $result = $User->update($req,$_FILES);
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