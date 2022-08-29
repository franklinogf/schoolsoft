<?php 
include('../../control.php'); 
$id = $_POST['id'];
mysql_query("DELETE FROM T_cafeteria  WHERE id = '$id'");

header("Location: index.php");