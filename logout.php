<?php

session_start();
if(isset($_SESSION['webtoon_userid'])){
	 
	unset($_SESSION['webtoon_userid']);
}


header("Location: login.php");
die;