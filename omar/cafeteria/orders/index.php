<?
require_once '../../app.php';

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
$orders = DB::table("cafeteria_orders")->where([["despachado", false]])->get();
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

    <div class="container-md mt-md-3 mb-md-5 px-0">

        <h1 class="text-center my-3"><?= $lang->translation("Ordenes") ?></h1>
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
                                <?php foreach ($orderDetails as $detail) : ?>
                                    <li><?= $detail->descripcion ?></li>
                                <?php endforeach ?>
                            </ol>
                            <button class="btn btn-primary float-right dispatch" data-order-id="<?= $order->id ?>"><?= $lang->translation("Despachar orden") ?></button>
                        </div>
                    </li>
                <?php endforeach ?>

            </ul>
        </div>

        <div class="text-center">
            <a href="../menu.php" class="btn btn-primary"><?= $lang->translation("Salir") ?></a>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>