<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();

$options = [
    [
        'title' => ["es" => 'Opciones', "en" => 'Options'],
        'buttons' => [
            [
                'name' => ["es" => 'Presupuesto',   "en" => "Budget"],
                'desc' => ['es' => 'Para definir los códigos y las descripciones.', 'en' => 'To define codes and descriptions.'],
                'link' => 'budget.php'
            ],
            [
                'name' => ["es" => 'Costos',   "en" => "Costs"],
                'desc' => ['es' => 'Para definir los cargos a los grados.', 'en' => 'To define costs to grades.'],
                'link' => 'Costs.php'
            ],
            [
                'name' => ["es" => 'Crear cargos',   "en" => "Create costs"],
                'desc' => ['es' => 'Pantalla para crear los diferentes cargos a las cuentas de los estudiantes.', 'en' => 'Screen to create the different costs to the student accounts.'],
                'link' => 'Create_costs.php'
            ],
            [
                'name' => ["es" => 'Entrar pagos',   "en" => "Entrar pagos"],
                'desc' => ['es' => 'Pantalla para realizar los pagos a las cuentas.', 'en' => 'Pantalla para realizar los pagos a las cuentas.'],
                'link' => 'payments/'
            ],
            [
                'name' => ["es" => 'Ver Pagos',   "en" => "View transactions"],
                'desc' => ['es' => 'Ver y borrar transacciones.', 'en' => 'View and delete transactions.'],
                'link' => 'Ver_pagos.php'
            ],
            [
                'name' => ["es" => 'Recargos',   "en" => "Surcharges"],
                'desc' => ['es' => 'Pantalla para aplicarle recargos a las cuentas atrazadas.', 'en' => 'Screen to apply surcharges to overdue accounts.'],
                'link' => 'recargos.php'
            ],
            [
                'name' => ["es" => 'Posteos',   "en" => "Posteos"],
                'desc' => ['es' => 'Pantalla para agregar pagos automáticos.', 'en' => 'Pantalla para agregar pagos automáticos.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pagos automaticos',   "en" => "Pagos automaticos"],
                'desc' => ['es' => 'Pantalla para procesar pagos automáticos.', 'en' => 'Pantalla para procesar pagos automáticos.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Re-enviar recibo',   "en" => "Re-enviar recibo"],
                'desc' => ['es' => 'Pantalla para reenviar recibos de los pagos automáticos.', 'en' => 'Pantalla para reenviar recibos de los pagos automáticos.'],
                'link' => '#'
            ],


        ]
    ],
    [
        'title' => ["es" => 'Informes', "en" => 'Reports'],
        'buttons' => [
            [
                'name' => ["es" => 'Pagos diarios', "en" => "Daily payments"],
                'desc' => ['es' => 'Pagos Realizados, Cuadre del día, puede seleccionar las fechas.', 'en' => 'Payments Made, Square of the day, you can select the dates.'],
                'link' => 'Daily_payments.php'
            ],
            [
                'name' => ["es" => 'Estado de cuenta', "en" => "Statement"],
                'desc' => ['es' => 'Imprimir o enviar el informe de estado de cuentas a los padres.', 'en' => 'Print or send the account status report to parents.'],
                'link' => 'Statement.php'
            ],
            [
                'name' => ["es" => 'Lista de deudores', "en" => "List of debtors"],
                'desc' => ['es' => 'Informe de deudores por grado puede seleccionar código.', 'en' => 'Debtors report by grade you can select code.'],
                'link' => 'deudores.php'
            ],
            [
                'name' => ["es" => '30, 60, 90', "en" => "30, 60, 90"],
                'desc' => ['es' => 'Lista de deudores por meses por cuentas.', 'en' => 'Lista de deudores por meses por cuentas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Carta de cobro', "en" => "Carta de cobro"],
                'desc' => ['es' => 'Puede seleccionar las cartas para imprimir o enviar por E-Mail.', 'en' => 'Puede seleccionar las cartas para imprimir o enviar por E-Mail.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Libreta de pago', "en" => "Libreta de pago"],
                'desc' => ['es' => 'Imprime las libretas de pagos a los padres.', 'en' => 'Imprime las libretas de pagos a los padres.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Presupuesto', "en" => "Presupuesto"],
                'desc' => ['es' => 'Informe para contabilizar las partidas de cada código.', 'en' => 'Informe para contabilizar las partidas de cada código.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pagos', "en" => "Pagos"],
                'desc' => ['es' => 'Listado por grupo para saber quienes pagaron.', 'en' => 'Listado por grupo para saber quienes pagaron.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Lista por fechas', "en" => "Lista por fechas"],
                'desc' => ['es' => 'Informe de totales por meses por código.', 'en' => 'Informe de totales por meses por código.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Lista de pagos', "en" => "Lista de pagos"],
                'desc' => ['es' => 'Lista de estudiantes que pagaron o no.', 'en' => 'Lista de estudiantes que pagaron o no.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Descripción', "en" => "Descripción"],
                'desc' => ['es' => 'Lista de deudores detallada por mes por cuentas.', 'en' => 'Lista de deudores detallada por mes por cuentas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cobros', "en" => "Cobros"],
                'desc' => ['es' => 'Pantalla para enviar mensajes de cobros a los deudores.', 'en' => 'Pantalla para enviar mensajes de cobros a los deudores.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Recibos', "en" => "Recibos"],
                'desc' => ['es' => 'Pantalla Para Buscar Recibos', 'en' => 'Pantalla Para Buscar Recibos'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pasar balances', "en" => "Pasar balances"],
                'desc' => ['es' => 'Pantalla para pasar los balances de los padres de un año al otro.', 'en' => 'Pantalla para pasar los balances de los padres de un año al otro.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deudores', "en" => "Deudores"],
                'desc' => ['es' => 'Informe detallado por cuentas y año.', 'en' => 'Informe detallado por cuentas y año.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Listado', "en" => "Listado"],
                'desc' => ['es' => 'Informe por descripción mensual.', 'en' => 'Informe por descripción mensual.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Matrícula estudiante', "en" => "Matrícula estudiante"],
                'desc' => ['es' => 'Informe por deudas detalladas.', 'en' => 'Informe por deudas detalladas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deuda salón', "en" => "Deuda salón"],
                'desc' => ['es' => 'Informe por deudas por salón hogar.', 'en' => 'Informe por deudas por salón hogar.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Reporte de pagos', "en" => "Reporte de pagos"],
                'desc' => ['es' => 'Reporte totales por código por año.', 'en' => 'Reporte totales por código por año.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Descuentos', "en" => "Descuentos"],
                'desc' => ['es' => 'Reporte totales de descuento por código.', 'en' => 'Reporte totales de descuento por código.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Pagos mensual', "en" => "Pagos mensual"],
                'desc' => ['es' => 'Informe mensual de pagos.', 'en' => 'Informe mensual de pagos.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deudores', "en" => "Deudores"],
                'desc' => ['es' => 'Informe pagado completo', 'en' => 'Informe pagado completo'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Presupuesto por grado', "en" => "Presupuesto por grado"],
                'desc' => ['es' => 'Informe presupuesto por grado.', 'en' => 'Informe presupuesto por grado.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Presupuesto por matrícula', "en" => "Presupuesto por matrícula"],
                'desc' => ['es' => 'Informe presupuesto por matricula y mensualidad.', 'en' => 'Informe presupuesto por matricula y mensualidad.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Misma descripcion', "en" => "Misma descripcion"],
                'desc' => ['es' => 'Informe por la misma descripciones.', 'en' => 'Informe por la misma descripciones.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Deuda familia', "en" => "Deuda familia"],
                'desc' => ['es' => 'Informe por deudas por familias.', 'en' => 'Informe por deudas por familias.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Depositos', "en" => "Depositos"],
                'desc' => ['es' => 'Informe depósitos cafeteria por fechas.', 'en' => 'Informe depósitos cafeteria por fechas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Balances', "en" => "Balances"],
                'desc' => ['es' => 'Informe balances depositados a los estudiantes.', 'en' => 'Informe balances depositados a los estudiantes.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Re-matrícula pagada', "en" => "Re-matrícula pagada"],
                'desc' => ['es' => 'Informe Matrículas pagadas.', 'en' => 'Informe Matrículas pagadas.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Cafeteria', "en" => "Cafeteria"],
                'desc' => ['es' => 'Detalle de compras de articulos de los estudiantes', 'en' => 'Detalle de compras de articulos de los estudiantes'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Descuento mensual', "en" => "Descuento mensual"],
                'desc' => ['es' => 'Informe mensual detallado por Código', 'en' => 'Informe mensual detallado por Código'],
                'link' => '#'
            ],            
        ]
    ],
   



];


$lang = new Lang([
    ["Cuentas a cobrar", "Billing statement"]
]);


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Cuentas a cobrar");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Cuentas a cobrar") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option) : ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'][__LANG] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button) : ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'][__LANG] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'][__LANG], 'UTF-8') ?></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            <?php endforeach ?>



        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>