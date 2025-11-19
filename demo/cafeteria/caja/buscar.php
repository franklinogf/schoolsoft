<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Util;

require_once __DIR__ . '/../../app.php';
$colegio = new School();
$year = $colegio->year();

if (isset($_POST['estudiante'])) {
	$code = $_POST['estudiante'];
	$student = DB::table('year')
		->where([['year', $year], ['cbarra', $code]])
		->first();

	if ($student) {
		$student=[
			'cantidad' => $student->cantidad,
			'tipo' => Util::studentProfilePicture($student),
			'nombre'=> $student->nombre,
			'apellidos'=> $student->apellidos,
			'rec1' => $student->rec1
	
		];
		echo json_encode($student);
	} else {
		echo json_encode(null);
	}
} else if (isset($_POST['estu'])) {

	$code = $_POST['estu'];

	$student = DB::table('year')		
		->where([['year', $year], ['ss', $code]])
		->first();

	$student=[
		'cantidad' => $student->cantidad,
		'tipo' => Util::studentProfilePicture($student),
		'rec1' => $student->rec1

	];

	echo json_encode($student);
} else {
	$code = $_POST['code'];
	$result = DB::table('inventario')->where('cbarra', $code)->first();

	echo json_encode($result);
}
