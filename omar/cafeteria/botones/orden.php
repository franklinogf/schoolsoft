<?php 
include('../../control.php'); 
$ids = $_POST['ids'];
$count = 1;

foreach ($ids as $id ) {
	mysql_query("UPDATE T_cafeteria SET orden = '$count' where id = '$id'");
	$count++;
}

