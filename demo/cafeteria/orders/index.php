<?php
require_once '../../app.php';

use App\Models\Admin;
use App\Models\CafeteriaOrderHistory;
use Classes\Route;
use Classes\Session;


Session::is_logged();

$admin = Admin::primaryAdmin();
$year = $admin->year;

CafeteriaOrderHistory::whereBeforeToday('fecha')->delete();


$orders = CafeteriaOrderHistory::pending()->get();
$listOfDetails = [];
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Ordenes");
    Route::includeFile('/cafeteria/includes/layouts/header.php');
    ?>
    <link rel="stylesheet" href="css/orders.css">
</head>

<body>
    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        <?= __("Ordenes") ?>
                        <span id="ordersAmount" class="badge badge-light text-primary ml-2"><?= count($orders) ?></span>
                        <small id="lastUpdate" class="text-light ml-3" style="font-size: 0.6rem; opacity: 0.8;"></small>
                    </h1>
                </div>
                <div class="col-md-4 d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                    <div class="btn-group" role="group" aria-label="Order Actions">
                        <button id="refreshOrders" class="btn btn-info mr-2">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Actualizar
                        </button>
                        <button id="dispatchAll" class="btn btn-success mr-2">
                            <i class="fas fa-shipping-fast mr-1"></i>
                            Despachar todos
                        </button>
                        <a href="../menu.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Salir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3">
        <div class="row">
            <!-- Orders Section -->
            <div class="col-12 col-lg-8 order-2 order-lg-1 mb-4">
                <div class="orders-container">
                    <div id="ordersList" class="orders-list">
                        <?php foreach ($orders as $order) : ?>
                            <div class="order-card card mb-3 shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            <div class="student-avatar">
                                                <img src="<?= $order->student->profilePicture ?>"
                                                    class="img-fluid rounded-circle"
                                                    alt="<?= __("Foto de perfil") ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-9 order-content">
                                            <div class="student-info mb-3">
                                                <h5 class="student-name text-primary font-weight-bold">
                                                    <?= $order->student->fullName ?>
                                                </h5>
                                            </div>

                                            <div class="order-items mb-3">
                                                <h6 class="text-muted mb-2">
                                                    <i class="fas fa-utensils mr-1"></i>
                                                    Artículos pedidos:
                                                </h6>
                                                <ol class="order-list pl-4">
                                                    <?php foreach ($order->items as $detail) :
                                                        if (!isset($listOfDetails[$detail->id])) $listOfDetails[$detail->id] = ['count' => 0, 'descripcion' => $detail->descripcion];
                                                        $listOfDetails[$detail->id]['count']++
                                                    ?>
                                                        <li class="order-item"><?= $detail->descripcion ?></li>
                                                    <?php endforeach ?>
                                                </ol>
                                            </div>

                                            <div class="order-actions d-flex justify-content-end flex-wrap">
                                                <button class="btn btn-warning btn-sm mr-2 mb-2 dispatchUp">
                                                    <i class="fas fa-arrow-up mr-1"></i>
                                                    Despachar hacia arriba
                                                </button>
                                                <button class="btn btn-success btn-sm dispatch mb-2" data-order-id="<?= $order->id ?>">
                                                    <i class="fas fa-check mr-1"></i>
                                                    <?= __("Despachar orden") ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <?php if (empty($orders)) : ?>
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-clipboard-check text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted">No hay órdenes pendientes</h4>
                            <p class="text-muted">Todas las órdenes han sido despachadas</p>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="col-12 col-lg-4 order-1 order-lg-2 mb-4">
                <div class="summary-card card shadow-sm sticky-top">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list-ul mr-2"></i>
                            Resumen de comidas
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <ul id="listOfDetails" class="list-group list-group-flush">
                            <?php foreach ($listOfDetails as $detail) : ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="detail font-weight-medium"><?= $detail['descripcion'] ?></span>
                                    <span class="badge badge-info badge-pill amount"><?= $detail['count'] ?></span>
                                </li>
                            <?php endforeach ?>
                        </ul>

                        <?php if (empty($listOfDetails)) : ?>
                            <div class="empty-summary text-center py-4">
                                <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted mb-0">No hay artículos en espera</p>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>