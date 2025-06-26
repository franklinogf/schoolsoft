<?php
require_once '../../../app.php';

use App\Models\CafeteriaOrderHistory;
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
    CafeteriaOrderHistory::find($orderId)->update([
        "despachado" => true
    ]);
} else if (isset($_POST['updateOrders'])) {
    $orders = CafeteriaOrderHistory::pending()->get();
    $ordersList = '';
    $listOfDetails = [];

    foreach ($orders as $order) {

        $orderDetails = DB::table("compra_cafeteria_detalle")->select("descripcion")->where([["id_compra", $order->id_compra]])->get();

        // Build the modern card structure
        $ordersList .= '
            <div class="order-card card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="student-avatar">
                                <img src="' . $order->student->profilePicture . '" 
                                     class="img-fluid rounded-circle" 
                                     alt="' . __("Foto de perfil") . '">
                            </div>
                        </div>
                        <div class="col-md-9 order-content">
                            <div class="student-info mb-3">
                                <h5 class="student-name text-primary font-weight-bold">
                                    ' . $order->student->fullName . '
                                </h5>
                            </div>
                            
                            <div class="order-items mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-utensils mr-1"></i>
                                    Artículos pedidos:
                                </h6>
                                <ol class="order-list pl-4">';

        // Add order items and build list of details
        foreach ($orderDetails as $detail) {
            $ordersList .= '<li class="order-item">' . $detail->descripcion . '</li>';

            // Build list of details for summary
            if (!isset($listOfDetails[$detail->descripcion])) {
                $listOfDetails[$detail->descripcion] = 0;
            }
            $listOfDetails[$detail->descripcion]++;
        }

        $ordersList .= '
                                </ol>
                            </div>
                            
                            <div class="order-actions d-flex justify-content-end flex-wrap">
                                <button class="btn btn-warning btn-sm mr-2 mb-2 dispatchUp">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    Despachar hacia arriba
                                </button>
                                <button class="btn btn-success btn-sm dispatch mb-2" data-order-id="' . $order->id . '">
                                    <i class="fas fa-check mr-1"></i>
                                    ' . __("Despachar orden") . '
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    }

    echo json_encode([
        'list' => $ordersList,
        "amount" => count($orders),
        "listOfDetails" => $listOfDetails
    ]);
}
