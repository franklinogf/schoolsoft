<?php
require_once '../../control.php';
$ra = mysql_query("SELECT * FROM colegio where usuario = 'administrador'");
$colegio = mysql_fetch_object($ra);
// general variables
$id = $_POST['id'];
$ss = $_POST['ss'];
$total = $_POST['total'];

if (isset($_POST['del'])) {
    $res = mysql_query("SELECT cantidad FROM year WHERE ss = '$ss' and year = '$colegio->year'");
    $estu = mysql_fetch_object($res);
    $newTotal = $estu->cantidad + $total;
    mysql_query("UPDATE year SET cantidad = '$newTotal' WHERE ss = '$ss' and year = '$colegio->year'");
    mysql_query("DELETE FROM compra_cafeteria  WHERE id = '$id'");
} else {
    $beforeTotal = $_POST['beforeTotal'];
    mysql_query("UPDATE compra_cafeteria SET total = '$total', pago1 = '$total' WHERE id = '$id'");
    if ($ss !== "") {
        $res = mysql_query("SELECT cantidad FROM year WHERE ss = '$ss' and year = '$colegio->year'");
        $estu = mysql_fetch_object($res);
        $diference = $beforeTotal - $total;
        $newTotal = $estu->cantidad + $diference;
        mysql_query("UPDATE year SET cantidad = '$newTotal' WHERE ss = '$ss' and year = '$colegio->year'");
        
    } 
    if (isset($_POST['items'])) {
        $items = json_decode(json_encode($_POST['items']));
        foreach ($items as $item) {
            mysql_query("UPDATE compra_cafeteria_detalle SET precio_final = '$item->price' WHERE id = '$item->id'");
        }
    }

    $array = ['total' => $total];
    header('Content-Type: application/json');
    echo json_encode($array);
}
