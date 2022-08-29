<?
require_once '../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$lang = new Lang([
['Salir','Go back'],
['Menú de la cafeteria','Cafeteria menu']
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
</head>

<body>

<div class="container-md mt-md-3 mb-md-5 px-0">

        <h1 class="text-center my-5"><?= $lang->translation("Menú de la cafeteria") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'][__LANG] ?>" <?= $button['target'] ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
                                    </div>
									<div class="col mt-1">
										<p class="mb-0 align-middle"><?= $button['desc'][__LANG] ?></p>
									</div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endforeach ?>

        </div>
		<div class="text-center">
		<a href="../" class="btn btn-primary"><?= $lang->translation("Salir") ?></a>
		</div>
    </div>
</body>

</html>