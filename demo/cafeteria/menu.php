<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$lang = new Lang([
    ['Salir', 'Go back'],
    ['Menú de la cafeteria', 'Cafeteria menu']
]);
$options = [
    [
        'title' => ["es" => 'Opciones', "en" => 'Options'],
        'buttons' => [
            [
                'name' => ["es" => 'Inventario', "en" => "Inventory"],
                'desc' => ['es' => 'Entrada de articulos', 'en' => 'Items entry'],
                'link' => 'inventario.php'
            ],
            [
                'name' => ["es" => 'Botones', "en" => "Buttons"],
                'desc' => ['es' => 'Definición de botones.', 'en' => 'Create buttons.'],
                'link' => 'botones/'
            ],
            [
                'name' => ["es" => 'Caja', "en" => "Cash register"],
                'desc' => ['es' => 'Ventas a los estudiantes.', 'en' => 'Students sales.'],
                'link' => 'caja/'
            ],
            [
                'name' => ["es" => 'Ordenes', "en" => "Orders"],
                'desc' => ['es' => 'Ordenes de los estudiantes.', 'en' => 'Students orders.'],
                'link' => 'orders/'
            ],
        ]
    ],
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [
            [
                'name' => ["es" => 'Informe',   "en" => "Report"],
                'desc' => ['es' => 'Informe de inventario.', 'en' => 'Inventory report.'],
                'link' => 'info_inventario.php',
                'target' => 'Informe de inventario'
            ],
            [
                'name' => ["es" => 'Ajuste de cuentas',   "en" => "Reckoning"],
                'desc' => ['es' => 'Informe de ajuste de cuenta.', 'en' => 'Account adjustment report.'],
                'link' => 'fechas.php?pdf=info_ajuste'
            ],
            [
                'name' => ["es" => 'Cuadre del dia',   "en" => "Daily account balance"],
                'desc' => ['es' => 'Informe del cuadre del dia.', 'en' => 'Daily account balance report.'],
                'link' => 'fechas.php?pdf=info_cuadre'
            ],
            [
                'name' => ["es" => 'Compras',   "en" => "Purchases"],
                'desc' => ['es' => 'Informe de compras.', 'en' => 'Purchasing Report.'],
                'link' => 'info_compra.php',
                'target' => 'compras'
            ]

        ]
    ],


];


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Menú de la cafeteria");
    Route::includeFile('/cafeteria/includes/layouts/header.php');
    ?>
    <link rel="stylesheet" href="menu.css">
</head>

<body class="bg-light">
    <!-- Hero Section -->
    <div class="cafeteria-hero">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-utensils mr-3"></i>
                    <?= $lang->translation("Menú de la cafeteria") ?>
                </h1>
                <p class="lead mb-0">Sistema de gestión integral para cafetería escolar</p>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Menu Cards -->
        <div class="row justify-content-center">
            <?php
            $cardIcons = [
                'Opciones' => 'fas fa-cogs',
                'Options' => 'fas fa-cogs',
                'Informes' => 'fas fa-chart-bar',
                'Reports' => 'fas fa-chart-bar'
            ];

            $buttonClasses = [
                'Inventario' => 'menu-btn-inventory',
                'Inventory' => 'menu-btn-inventory',
                'Botones' => 'menu-btn-buttons',
                'Buttons' => 'menu-btn-buttons',
                'Caja' => 'menu-btn-cash',
                'Cash register' => 'menu-btn-cash',
                'Ordenes' => 'menu-btn-orders',
                'Orders' => 'menu-btn-orders',
                'Informe' => 'menu-btn-report',
                'Report' => 'menu-btn-report',
                'Ajuste de cuentas' => 'menu-btn-reckoning',
                'Reckoning' => 'menu-btn-reckoning',
                'Cuadre del dia' => 'menu-btn-balance',
                'Daily account balance' => 'menu-btn-balance',
                'Compras' => 'menu-btn-purchases',
                'Purchases' => 'menu-btn-purchases'
            ];

            $buttonIcons = [
                'Inventario' => 'fas fa-boxes',
                'Inventory' => 'fas fa-boxes',
                'Botones' => 'fas fa-th-large',
                'Buttons' => 'fas fa-th-large',
                'Caja' => 'fas fa-cash-register',
                'Cash register' => 'fas fa-cash-register',
                'Ordenes' => 'fas fa-clipboard-list',
                'Orders' => 'fas fa-clipboard-list',
                'Informe' => 'fas fa-file-alt',
                'Report' => 'fas fa-file-alt',
                'Ajuste de cuentas' => 'fas fa-balance-scale',
                'Reckoning' => 'fas fa-balance-scale',
                'Cuadre del dia' => 'fas fa-calculator',
                'Daily account balance' => 'fas fa-calculator',
                'Compras' => 'fas fa-shopping-cart',
                'Purchases' => 'fas fa-shopping-cart'
            ];
            ?>

            <?php foreach ($options as $option) : ?>
                <div class="col-lg-6 col-xl-5 mb-4">
                    <div class="card menu-card">
                        <div class="menu-card-header">
                            <i class="<?= $cardIcons[$option['title'][__LANG]] ?? 'fas fa-folder' ?> menu-icon"></i>
                            <?= $option['title'][__LANG] ?>
                        </div>
                        <div class="card-body p-4">
                            <?php foreach ($option['buttons'] as $button) : ?>
                                <div class="menu-option">
                                    <div class="row align-items-center">
                                        <div class="col-md-5 mb-2 mb-md-0">
                                            <a href="<?= $button['link'] ?>"
                                                class="btn menu-btn <?= $buttonClasses[$button['name'][__LANG]] ?? 'btn-primary' ?> btn-block"
                                                title="<?= $button['desc'][__LANG] ?>"
                                                <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?>>
                                                <i class="<?= $buttonIcons[$button['name'][__LANG]] ?? 'fas fa-link' ?> mr-2"></i>
                                                <?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?>
                                            </a>
                                        </div>
                                        <div class="col-md-7">
                                            <p class="menu-description">
                                                <?= $button['desc'][__LANG] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <!-- Exit Button -->
        <div class="text-center mt-5 mb-4">
            <a href="../" class="btn exit-btn">
                <i class="fas fa-arrow-left mr-2"></i>
                <?= $lang->translation("Salir") ?>
            </a>
        </div>
    </div>
</body>

</html>