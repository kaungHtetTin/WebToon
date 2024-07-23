<?php
    include_once('../../classes/connect.php');
    include_once('../../classes/series.php');
    include_once('../../classes/jwt.php');
 
    
    $Series = new Series();
    $JWT = new JWT();

    $requestHeaders = apache_request_headers();
    $jwt_auth_token =$requestHeaders['Authorization'];

    $user = $JWT->validateJWT($jwt_auth_token);

    if($user){
        $series=$Series->getMySeries($user['userId']);
        echo json_encode($series);
    }else{  
        $reponse =['status'=>'Fail','message'=>'Cannot Authorize'];
        echo json_encode($reponse);
    }
?>