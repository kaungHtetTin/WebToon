<?php
include('../../classes/connect.php');

$email = $_GET['email'];
$code = $_GET['code'];

$query = "SELECT otp FROM users WHERE email='$email' LIMIT 1";

$DB = new Database();
$result = $DB->read($query);
$opt = $result[0]['otp'];

if($opt==$code){
    $response ['status']="success";
}else {
    $response['status']="fail";
}

echo json_encode($response);

?>