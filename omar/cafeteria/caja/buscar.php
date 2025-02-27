<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;

require_once '../../app.php';
$colegio = new School();
$year = $colegio->year();

if (isset($_POST['estudiante'])) {
	$code = $_POST['estudiante'];
	$student = DB::table('year')
		->select('cantidad,nombre,apellidos,tipo,rec1')
		->where([['year', $year], ['cbarra', $code]])
		->first();

	if ($student) {
		echo json_encode($student);
	} else {
		echo json_encode(null);
	}
} else if (isset($_POST['estu'])) {

	$code = $_POST['estu'];

	$students = DB::table('year')
		->select('cantidad,tipo,rec1')
		->where([['year', $year], ['ss', $code]])
		->first();

	echo json_encode($students);
} else {
	$code = $_POST['code'];
	$result = DB::table('inventario')->where('cbarra', $code)->first();

	echo json_encode($result);
}
