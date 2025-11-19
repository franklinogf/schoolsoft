<?php

use App\Models\CafeteriaOrder;
use App\Models\CafeteriaOrderItem;
use App\Models\Student;
use Classes\DataBase\DB;

require_once __DIR__ . '/../../app.php';

// general variables




if (isset($_POST['del'])) {
    $id = $_POST['id'];
    $ss = $_POST['ss'] ?? '';
    $total = $_POST['total'];
    $student = Student::bySS($ss)->first();
    $newTotal = $student->cantidad + $total;
    $student->update(['cantidad' => $newTotal]);
    CafeteriaOrder::where('id', $id)->delete();
} else {
    $id = $_POST['id'];
    $ss = $_POST['ss'] ?? '';
    $total = $_POST['total'];
    $beforeTotal = $_POST['beforeTotal'];
    CafeteriaOrder::where('id', $id)->update(['total' => $total, 'pago1' => $total]);
    if ($ss !== "") {
        $student = Student::bySS($ss)->first();
        $diference = $beforeTotal - $total;
        $newTotal = $student->cantidad + $diference;

        $student->update(['cantidad' => $newTotal]);
    }

    if (isset($_POST['items'])) {
        $items = json_decode(json_encode($_POST['items']));
        foreach ($items as $item) {
            if ($item->removed === 'true') {
                CafeteriaOrderItem::where('id', $item->id)->delete();
                continue;
            }
            CafeteriaOrderItem::where('id', $item->id)->update(['precio_final' => $item->price]);
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['total' => $total]);
}
