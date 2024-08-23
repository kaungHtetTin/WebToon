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
            $user=$User->details($user['userId']);
            $req['email']=$user['email'];
            $req['password'] = $_POST['password'];
            $req['user_id'] = $user['id'];
            $result = $User->deleteAccount($req);
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