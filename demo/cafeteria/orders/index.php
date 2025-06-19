<?php
require_once '../../app.php';

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
$lang = new Lang([
    ['Salir', 'Go back'],
    ['Ordenes', 'Orders'],
    ['Foto de perfil', 'Profile picture'],
    ['Despachar orden', "Dispatch order"]
]);
$school = new School();
$year = $school->year();
DB::table('cafeteria_orders')->alter('ADD COLUMN fecha DATE DEFAULT CURRENT_DATE');

DB::table('cafeteria_orders')->where('fecha', '<', date('Y-m-d'))->delete();


$orders = DB::table("cafeteria_orders")->where([["despachado", false]])->orderBy('id', 'asc')->get();
$listOfDetails = [];
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Ordenes");
    Route::includeFile('/cafeteria/includes/layouts/header.php');
    ?>
</head>

<body>

    <div class="container-fluid container-md mt-md-3 mb-md-5 px-0">
        <div class="row">
            <div class="col-12 col-md-8 order-2 order-md-1">
                <h1 class="text-center my-3"><?= $lang->translation("Ordenes") ?> <span id="ordersAmount" class="badge badge-info"><?= count($orders) ?></span></h1>
                <div class="mb-3">
                    <button id="dispatchAll" class="btn btn-primary">Despachar todos</button>
                    <a href="../menu.php" class="btn btn-secondary">Salir</a>
                </div>
                <div class="jumbotron">
                    <ul id="ordersList" class="list-unstyled">
                        <?php foreach ($orders as $order) :
                            $student = new Student($order->ss);
                            $orderDetails = DB::table("compra_cafeteria_detalle")->select("descripcion")->where([["id_compra", $order->id_compra]])->get();
                        ?>
                            <li class="media bg-white mt-4 shadow-sm p-3">
                                <img src="<?= $student->profilePicture() ?>" class="align-self-center mr-3" width="150px" height="150px" alt="<?= $lang->translation("Foto de perfil") ?>">
                                <div class="media-body">
                                    <h5 class="mt-0 mb-1"><?= $student->fullName() ?></h5>
                                    <ol>
                                        <?php foreach ($orderDetails as $detail) :
                                        if(!isset($listOfDetails[$detail->descripcion])) $listOfDetails[$detail->descripcion] = 0;
                                            $listOfDetails[$detail->descripcion]++
                                        ?>
                                            <li><?= $detail->descripcion ?></li>
                                        <?php endforeach ?>
                                    </ol>
                                    <div class="mt-2 d-flex flex-row-reverse">
                                        <button class="btn btn-sm btn-danger float-right dispatchUp">Despachar hacia arriba</button>
                                        <button class="btn btn-primary float-right dispatch mx-2" data-order-id="<?= $order->id ?>"><?= $lang->translation("Despachar orden") ?></button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>

                    </ul>
                </div>

                <div class="text-center">
                    <a href="../menu.php" class="btn btn-primary"><?= $lang->translation("Salir") ?></a>
                </div>
            </div>
            <div class="col-12 col-md-4 order-1 order-md-2">
                <h3 class="text-center my-3">Lista de comidas en espera</h3>

                <div class="container">
                    <ul id="listOfDetails" class="list-group">
                        <?php foreach ($listOfDetails as $detail => $amount) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail"><?= $detail ?></span>
                                <span class="badge badge-primary badge-pill amount"><?= $amount ?></span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>