<?php
include_once('../classes/util.php');

$Util = new Util();

$data['status']="The latest version is 1";
$data['version_code']= $Util->mobileAppVersionCode();
$data['update_link']="http://wordofwebtoonmmsub.com";

echo json_encode($data);

?>