<?php
require_once '../../control.php';
$ra = mysql_query("SELECT * FROM colegio where usuario = 'administrador'");
$colegio = mysql_fetch_object($ra);
$date = $_POST['date'];
$array = [];
if (isset($_POST['ss'])) {
    $ss = $_POST['ss'];
    $res = mysql_query("SELECT * FROM compra_cafeteria WHERE ss = '$ss' and fecha = '$date'");
} else if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $result = mysql_query("SELECT ss FROM year WHERE year='$colegio->year' AND cbarra = '$code'");
    if (mysql_num_rows($result) > 0) {
        $estu = mysql_fetch_object($result);
        $ss = $estu->ss;
        $res = mysql_query("SELECT * FROM compra_cafeteria WHERE ss = '$ss' and fecha = '$date'");
    } else {
        $array = ['exist' => false];
    }
} else {
    $id = $_POST['id'];
    $res = mysql_query("SELECT * FROM compra_cafeteria WHERE id = '$id'");
    if (mysql_num_rows($res) === 0) {
        $array = ['exist' => false];
    }
}


if (isset($res)) {
    while ($pay = mysql_fetch_object($res)) {

        $array[] = $pay;
    }
}

header('Content-Type: application/json');
echo json_encode($array);
