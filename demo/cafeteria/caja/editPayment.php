<?php

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\DataBase\DB;

require_once '../../app.php';

$school = new School();
$year = $school->year();
// general variables
$id = $_POST['id'];
$ss = $_POST['ss'] ?? '';
$total = $_POST['total'];

if (isset($_POST['del'])) {
    $student = new Student($ss);
    $newTotal = $student->cantidad + $total;
    DB::table('year')->where([['year',$year],['ss',$ss]])->update(['cantidad' => $newTotal]);
     DB::table('compra_cafeteria')->where('id', $id)->delete();
   
} else {
    $beforeTotal = $_POST['beforeTotal'];
    DB::table('compra_cafeteria')->where('id', $id)->update(['total' => $total, 'pago1' => $total]);
    if ($ss !== "") {
        $student = new Student($ss);
        $diference = $beforeTotal - $total;
        $newTotal = $student->cantidad + $diference;
        $student->cantidad = $newTotal;
        $student->save();  
    }

    if (isset($_POST['items'])) {
        $items = json_decode(json_encode($_POST['items']));
        foreach ($items as $item) {
            DB::table('compra_cafeteria_detalle')->where('id', $item->id)->update(['precio_final' => $item->price]);
        }
    }

    $array = ['total' => $total];
    header('Content-Type: application/json');
    echo json_encode($array);
}
