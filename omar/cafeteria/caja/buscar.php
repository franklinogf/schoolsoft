<?php
include('../../control.php');
$ra = mysql_query("SELECT * FROM colegio where usuario = 'administrador'");
$colegio = mysql_fetch_object($ra);
if (isset($_POST['estudiante'])) {
	$code = $_POST['estudiante'];
	$result = mysql_query("SELECT cantidad,nombre,apellidos,tipo,rec1 FROM year  WHERE year=
	'$colegio->year' and cbarra = '$code'");

	if (mysql_num_rows($result) > 0) {
		$student = mysql_fetch_object($result);

		$array = [
			"cantidad" => $student->cantidad,
			"nombre" => $student->nombre,
			"apellidos" => utf8_encode($student->apellidos),
			"tipo" => $student->tipo,
			"rec1" => $student->rec1
		];

		echo json_encode($array);
	}else{
		echo json_encode(null);
	}
} else if (isset($_POST['estu'])) {

	$code = $_POST['estu'];
	$result = mysql_query("SELECT cantidad,tipo,rec1 FROM year  WHERE year=
	'$colegio->year' and ss = '$code'");

	while ($row = mysql_fetch_object($result)) {
		$foods = $row;
	}

	$foods = json_encode($foods);

	print_r($foods);
} else {
	$code = $_POST['code'];
	$result = mysql_query("SELECT * FROM inventario  WHERE cbarra = '$code'");


	while ($row = mysql_fetch_object($result)) {
		$foods = $row;
	}

	$foods = json_encode($foods);

	print_r($foods);
}
