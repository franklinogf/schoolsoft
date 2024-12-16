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
                'name' => ["es" => 'Re-enviar recibo',   "en" => "Resend receipt"],
                'desc' => ['es' => 'Pantalla para reenviar recibos de los pagos automáticos.', 'en' => 'Screen to resend receipts for automatic payments.'],
                'link' => 'reenviar.php'
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
                'desc' => ['es' => 'Lista de deudores por meses por cuentas.', 'en' => 'List of debtors by month by accounts.'],
                'link' => 'deudores369.php'
            ],
            [
                'name' => ["es" => 'Carta de cobro', "en" => "Collection letter"],
                'desc' => ['es' => 'Puede seleccionar las cartas para imprimir o enviar por E-Mail.', 'en' => 'You can select letters to print or send by E-Mail.'],
                'link' => 'letter/index.php'
            ],
            [
                'name' => ["es" => 'Carta de Suspensión', "en" => "Suspension Letter"],
                'desc' => ['es' => 'Listado de deudores para suspensión.', 'en' => 'List of debtors for suspension.'],
                'link' => 'letter.php'
            ],
            [
                'name' => ["es" => 'Presupuesto', "en" => "Budget"],
                'desc' => ['es' => 'Informe para contabilizar las partidas de cada código.', 'en' => 'Report to account for the items of each code.'],
                'link' => 'inf_presupuesto_op.php'
            ],
            [
                'name' => ["es" => 'Pagos', "en" => "Payments"],
                'desc' => ['es' => 'Listado por grupo para saber quienes pagaron.', 'en' => 'List by group to know who paid.'],
                'link' => 'inf_pagos_op.php'
            ],
            [
                'name' => ["es" => 'Lista por fechas', "en" => "List by dates"],
                'desc' => ['es' => 'Informe de totales por meses por código.', 'en' => 'Monthly totals report by code.'],
                'link' => 'inf_por_fecha_op.php'
            ],
            [
                'name' => ["es" => 'Lista de pagos', "en" => "Payment list"],
                'desc' => ['es' => 'Lista de estudiantes que pagaron o no.', 'en' => 'List of students who paid or not.'],
                'link' => 'lista_pagos.php'
            ],
            [
                'name' => ["es" => 'Descripción', "en" => "Descripción"],
                'desc' => ['es' => 'Lista de deudores detallada por mes por cuentas.', 'en' => 'List of debtors detailed by month by accounts.'],
                'link' => 'sabana.php'
            ],
            [
                'name' => ["es" => 'Cobros', "en" => "Late Payment"],
                'desc' => ['es' => 'Pantalla para enviar mensajes de cobros a los deudores.', 'en' => 'Screen to send a late payment message to debtors.'],
                'link' => 'cobros_testos.php'
            ],
            [
                'name' => ["es" => 'Recibos', "en" => "Receipts"],
                'desc' => ['es' => 'Pantalla Para Buscar Recibos', 'en' => 'Screen to Search Receipts'],
                'link' => 'receipts.php'
            ],
            [
                'name' => ["es" => 'Pasar balances', "en" => "Pass balances"],
                'desc' => ['es' => 'Pantalla para pasar los balances de los padres de un año al otro.', 'en' => "Screen to transfer the parents' balance sheets from one year to the next."],
                'link' => 'pass_balances.php'
            ],
            [
                'name' => ["es" => 'Deudores', "en" => "Debtors"],
                'desc' => ['es' => 'Informe detallado por cuentas y año.', 'en' => 'Detailed report by accounts and year.'],
                'link' => 'deudores_op.php'
            ],
            [
                'name' => ["es" => 'Listado', "en" => "List"],
                'desc' => ['es' => 'Informe por descripción mensual.', 'en' => 'Report by monthly description.'],
                'link' => 'inf_desc_op.php'
            ],
            [
                'name' => ["es" => 'Matrícula estudiante', "en" => "Student registration"],
                'desc' => ['es' => 'Informe por deudas detalladas.', 'en' => 'Detailed debt report.'],
                'link' => 'inf_mat_est_op.php'
            ],
            [
                'name' => ["es" => 'Deuda salón hogar', "en" => "Debt for home room"],
                'desc' => ['es' => 'Informe por deudas por salón hogar.', 'en' => 'Debt report for home room.'],
                'link' => 'inf_deuda_salon_op.php'
            ],
            [
                'name' => ["es" => 'Reporte de pagos', "en" => "Payment report"],
                'desc' => ['es' => '', 'en' => ''],
                'link' => 'pdf/inf_reporte_pago.php',
                'target' => 'inf_reporte_pago'
            ],
            [
                'name' => ["es" => 'Descuentos', "en" => "Discounts"],
                'desc' => ['es' => 'Reporte totales de descuento por código.', 'en' => 'Total discount report by code.'],
                'link' => 'pdf/inf_reporte_descuentos.php',
                'target' => 'inf_reporte_descuentos'
            ],
            [
                'name' => ["es" => 'Pagos mensual', "en" => "Pagos mensual"],
                'desc' => ['es' => 'Informe mensual de pagos.', 'en' => 'Informe mensual de pagos.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'No Deudores', "en" => "Non-Debtors"],
                'desc' => ['es' => 'Informe pagado completo', 'en' => 'Complete paid report'],
                'link' => 'inf_pagados_op.php'
            ],
            [
                'name' => ["es" => 'Presupuesto por grado', "en" => "Budget by grade"],
                'desc' => ['es' => 'Informe presupuesto por grado.', 'en' => 'Budget report by grade.'],
                'link' => 'pdf/inf_presu.php',
                'target' => 'inf_presu'
            ],
            [
                'name' => ["es" => 'Presupuesto por matrícula', "en" => "Presupuesto por matrícula"],
                'desc' => ['es' => 'Informe presupuesto por matricula y mensualidad.', 'en' => 'Informe presupuesto por matricula y mensualidad.'],
                'link' => '#'
            ],
            [
                'name' => ["es" => 'Reporte descripción', "en" => "Report description"],
                'desc' => ['es' => 'Informe por descripciones.', 'en' => 'Report by description'],
                'link' => 'inf_descp_op.php'
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