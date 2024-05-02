<?php
include('../../classes/connect.php');

$email = $_POST['email'];
$password = $_POST['password'];
$code = $_POST['code'];

$query = "SELECT * FROM users WHERE email= '$email' and otp=$code  LIMIT 1";
$DB = new Database();
$result = $DB->read($query);
if($result){
    $password=hash("md5", $password);
    $query = "UPDATE users SET password ='$password' WHERE email = '$email' ";
    $DB->save($query);
    $response['status']="success";
}else{
    $response['status']="fail";
}

echo json_encode($response);

?>