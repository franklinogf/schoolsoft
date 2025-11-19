<?php

use App\Models\CafeteriaButton;

require_once __DIR__ . '/../../app.php';
$ids = $_POST['ids'];
$count = 1;

foreach ($ids as $id) {

	CafeteriaButton::find($id)->update([
		'orden' => $count,
	]);
	$count++;
}

var_dump($ids);
