<?php

use Classes\DataBase\DB;

require_once '../../app.php';
$ids = $_POST['ids'];
$count = 1;

foreach ($ids as $id ) {
	DB::table('T_cafeteria')->where('id', $id)->update([
	'orden' => $count
	]);
	$count++;
}

var_dump($ids);