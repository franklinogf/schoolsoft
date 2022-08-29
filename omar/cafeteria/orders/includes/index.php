<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
Server::is_post();

$lang = new Lang([
    ['Foto de perfil', 'Profile picture'],
    ['Despachar orden', "Dispatch order"]
]);

if (isset($_POST['orderId'])) {
    $orderId = $_POST['orderId'];
    DB::table("cafeteria_orders")->where([["id", $orderId]])->update([
        "despachado" => true
    ]);
} else if (isset($_POST['updateOrders'])) {
    $orders = DB::table("cafeteria_orders")->where([["despachado", false]])->get();
    $ordersList = '';
    foreach ($orders as $order) {
        $student = new Student($order->ss);
        $orderDetails = DB::table("compra_cafeteria_detalle")->select("descripcion")->where([["id_compra", $order->id_compra]])->get();
        $ordersList .= '
        <li class="media bg-white mt-4 shadow-sm p-3">
        <img src="' . $student->profilePicture() . '" class="align-self-center mr-3" width="150px" height="150px" alt="' . $lang->translation("Foto de perfil") . '">
        <div class="media-body">
            <h5 class="mt-0 mb-1">' . $student->fullName() . '</h5>
            <ol>';
        foreach ($orderDetails as $detail) {
            $ordersList .= "<li>$detail->descripcion</li>";
        }
        $ordersList .= '</ol>
                        <button class="btn btn-primary float-right dispatch" data-order-id="' . $order->id . '">' . $lang->translation("Despachar orden") . '</button>
                    </div>
                </li>
                ';
    }
    echo $ordersList;
}
