<?php
require_once '../../control.php';
$ra = mysql_query("SELECT * FROM colegio where usuario = 'administrador'");
$colegio = mysql_fetch_object($ra);
$array = [];
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $res = mysql_query("SELECT * FROM compra_cafeteria_detalle WHERE id_compra = '$id'");
}

if (isset($res)) {
    while ($pay = mysql_fetch_object($res)) {

        $array[] = $pay;
    }
}

header('Content-Type: application/json');
echo json_encode($array);
