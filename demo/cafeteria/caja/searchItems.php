<?php
use Classes\DataBase\DB;

require_once '../../app.php';

$data = [];
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $result = DB::table('compra_cafeteria_detalle')
        ->where('id_compra', $id)
        ->get();
    if (count($result) === 0) {
        $data = ['exist' => false];
    } else {
        $data = ['exist' => true, 'data' => $result];
    }
}



header('Content-Type: application/json');
echo json_encode($data);
