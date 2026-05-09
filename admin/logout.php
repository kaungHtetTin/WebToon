<?php

@include 'config.php';

session_start();

unset($_SESSION['admin_id']);
unset($_SESSION['admin_permissions']);

session_unset();
session_destroy();

header('location:login.php');
exit;
?>
