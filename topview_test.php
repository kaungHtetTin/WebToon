<?php 
include('classes/connect.php');
include('classes/view_history.php');

$ViewHistory = new ViewHistory();

$dayView = $ViewHistory->topViewDay();
$weekView = $ViewHistory->topViewWeek();
$monthView = $ViewHistory->topViewMonth();
$yearView = $ViewHistory->topViewYear();




echo "<pre>";
print_r($dayView);
echo "=========";
print_r($weekView);
echo "=============";
print_r($monthView);
echo "===========";
print_r($yearView);
echo "</pre>";


?>