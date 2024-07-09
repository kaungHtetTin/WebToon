<?php 
include('../../classes/connect.php');

$email = $_GET['email'];

$DB = new Database();
$query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = $DB->read($query);

if($result){
    $response['status']="success";
    $response['user']=$result[0];
    echo json_encode($response);
}else{
    $response['status']="fail";
    $response['msg']="No account was found!";
    echo json_encode($response);

}



?>