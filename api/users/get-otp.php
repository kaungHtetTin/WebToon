<?php
include('../../classes/connect.php');

$email = $_GET['email'];

$code=substr(time(),6,10);
mail($email,"World of webtoon MM Sub",$code,"Confirmation code");

$query = "UPDATE users SET otp=$code WHERE email = '$email'";

$DB = new Database();
$DB->save($query);

?>