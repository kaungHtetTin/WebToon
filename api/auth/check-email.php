<?php
    include_once('../../classes/connect.php');
    
    $email = $_GET['email'];
    $DB = new Database();

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $DB->read($query);

    if($result){
        $res['email_exist']=true;
    }else{
        $res['email_exist']=false;
    }
    
    echo json_encode($res);
?>