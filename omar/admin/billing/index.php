<?php
require_once '../../app.php';


use Classes\Route;
use Classes\Session;

Session::is_logged();

$options = [
    [
        'title' => __("Opciones"),
        'buttons' => [
            [
                'name' => __("Presupuesto"),
                'desc' => __('Para definir los códigos y las descripciones.'),
                'link' => 'budget.php',
            ],
            [
                'name' => __("Costos"),
                'desc' => __('Para definir los cargos a los grados.'),
                'link' => 'Costs.php',
            ],
            [
                'name' => __("Crear cargos"),
                'desc' => __('Pantalla para crear los diferentes cargos a las cuentas de los estudiantes.'),
                'link' => 'Create_costs.php',
            ],
            [
                'name' => __("Entrar pagos"),
                'desc' => __('Pantalla para realizar los pagos a las cuentas.'),
                'link' => 'payments/',
            ],
            [
                'name' => __("Ver Pagos"),
                'desc' => __('Ver y borrar transacciones.'),
                'link' => 'Ver_pagos.php',
            ],
            [
                'name' => __("Recargos"),
                'desc' => __('Pantalla para aplicarle recargos a las cuentas atrazadas.'),
                'link' => 'recargos.php',
            ],
            [
                'name' => __("Posteos"),
                'desc' => __('Pantalla para agregar pagos automáticos.'),
                'link' => 'posteos/',
            ],
            [
                'name' => __("Pagos automaticos"),
                'desc' => __('Pantalla para procesar pagos automáticos.'),
                'link' => '#',
            ],
            [
                'name' => __("Re-enviar recibo"),
                'desc' => __('Pantalla para reenviar recibos de los pagos automáticos.'),
                'link' => 'reenviar.php',
            ],
            [
                'name' => __("Pasar balances"),
                'desc' => __('Pantalla para pasar los balances de los padres de un año al otro.'),
                'link' => 'pass_balances.php',
            ],
            [
                'name' => __("Activaciones"),
                'desc' => __('Pantalla para activación y desactivación de pantallas.'),
                'link' => 'activacion.php',
            ],
        ],
    ],
    [
        'title' => __("Informes"),
        'buttons' => [
            [
                'name' => __("Pagos diarios"),
                'desc' => __('Pagos Realizados, Cuadre del día, puede seleccionar las fechas.'),
                'link' => 'Daily_payments.php',
            ],
            [
                'name' => __("Estado de cuenta"),
                'desc' => __('Imprimir o enviar el informe de estado de cuentas a los padres.'),
                'link' => 'Statement.php',
            ],
            [
                'name' => __("Lista de deudores"),
                'desc' => __('Informe de deudores por grado puede seleccionar código.'),
                'link' => 'deudores.php',
            ],
            [
                'name' => __("30, 60, 90"),
                'desc' => __('Lista de deudores por meses por cuentas.'),
                'link' => 'deudores369.php',
            ],
            [
                'name' => __("Carta de cobro"),
                'desc' => __('Puede seleccionar las cartas para imprimir o enviar por E-Mail.'),
                'link' => 'letter/index.php',
            ],
            [
                'name' => __("Carta de Suspensión"),
                'desc' => __('Listado de deudores para suspensión.'),
                'link' => 'letter.php',
            ],
            [
                'name' => __("Presupuesto"),
                'desc' => __('Informe para contabilizar las partidas de cada código.'),
                'link' => 'inf_presupuesto_op.php',
            ],
            [
                'name' => __("Pagos"),
                'desc' => __('Listado por grupo para saber quienes pagaron.'),
                'link' => 'inf_pagos_op.php',
            ],
            [
                'name' => __("Lista por fechas"),
                'desc' => __('Informe de totales por meses por código.'),
                'link' => 'inf_por_fecha_op.php',
            ],
            [
                'name' => __("Lista de pagos"),
                'desc' => __('Lista de estudiantes que pagaron o no.'),
                'link' => 'lista_pagos.php',
            ],
            [
                'name' => __("Descripción"),
                'desc' => __('Lista de deudores detallada por mes por cuentas.'),
                'link' => 'sabana.php',
            ],
            [
                'name' => __("Cobros"),
                'desc' => __('Pantalla para enviar mensajes de cobros a los deudores.'),
                'link' => 'cobros_testos.php',
            ],
            [
                'name' => __("Recibos"),
                'desc' => __('Pantalla Para Buscar Recibos'),
                'link' => 'receipts.php',
            ],
            [
                'name' => __("Pasar balances"),
                'desc' => __('Pantalla para pasar los balances de los padres de un año al otro.'),
                'link' => 'pass_balances.php',
            ],
            [
                'name' => __("Deudores"),
                'desc' => __('Informe detallado por cuentas y año.'),
                'link' => 'deudores_op.php',
            ],
            [
                'name' => __("Listado"),
                'desc' => __('Informe por descripción mensual.'),
                'link' => 'inf_desc_op.php',
            ],
            [
                'name' => __("Matrícula estudiante"),
                'desc' => __('Informe por deudas detalladas.'),
                'link' => 'inf_mat_est_op.php',
            ],
            [
                'name' => __("Deuda salón hogar"),
                'desc' => __('Informe por deudas por salón hogar.'),
                'link' => 'inf_deuda_salon_op.php',
            ],
            [
                'name' => __("Reporte de pagos"),
                'desc' => __(''),
                'link' => 'pdf/inf_reporte_pago.php',
                'target' => 'inf_reporte_pago',
            ],
            [
                'name' => __("Descuentos"),
                'desc' => __('Reporte totales de descuento por código.'),
                'link' => 'pdf/inf_reporte_descuentos.php',
                'target' => 'inf_reporte_descuentos',
            ],
            [
                'name' => __("Pagos mensual"),
                'desc' => __('Informe mensual de pagos.'),
                'link' => 'Pagos_mensual.php',
            ],
            [
                'name' => __("No Deudores"),
                'desc' => __('Informe pagado completo'),
                'link' => 'inf_pagados_op.php',
            ],
            [
                'name' => __("Presupuesto por grado"),
                'desc' => __('Informe presupuesto por grado.'),
                'link' => 'pdf/inf_presu.php',
                'target' => 'inf_presu',
            ],
            [
                'name' => __("Presupuesto por matrícula"),
                'desc' => __('Informe presupuesto por matricula y mensualidad.'),
                'link' => 'inf_presu_op.php',
            ],
            [
                'name' => __("Reporte descripción"),
                'desc' => __('Informe por descripciones.'),
                'link' => 'inf_descp_op.php',
            ],
            [
                'name' => __("Deuda familia"),
                'desc' => __('Informe por deudas por familias.'),
                'link' => 'pdf/inf_lista_cta.php',
                'target' => 'inf_lista_cta',
            ],
            [
                'name' => __("Depositos"),
                'desc' => __('Informe depósitos cafeteria por fechas.'),
                'link' => 'inf_dep_dia_op.php',
            ],
            [
                'name' => __("Balances"),
                'desc' => __('Informe balances depositados a los estudiantes.'),
                'link' => 'pdf/inf_dep_balances.php',
                'target' => 'inf_dep_balances',
            ],
            [
                'name' => __("Re-matrícula pagadas"),
                'desc' => __('Informe Matrículas pagadas.'),
                'link' => 'pdf/re_pagadas.php',
                'target' => 're_pagadas',
            ],
            [
                'name' => __("Matrícula Estu. Sup."),
                'desc' => __('Informe Matrículas Estudios Supervisados.'),
                'link' => 'pdf/re_pagadas2.php',
                'target' => 're_pagadas2',
            ],
            [
                'name' => __("Lista Est. Sup."),
                'desc' => __('Lista por grado de Estudios Supervisados.'),
                'link' => 'pdf/re_pagadas3.php',
                'target' => 're_pagadas3',
            ],
            [
                'name' => __("Primera Comunión"),
                'desc' => __('Lista por grado de Primera Comunión..'),
                'link' => 'pdf/pc_pagadas.php',
                'target' => 'pc_pagadas',
            ],
            [
                'name' => __("Cafetería"),
                'desc' => __('Detalle de compras de articulos de los estudiantes.'),
                'link' => 'cafeteria_op.php',
            ],
            [
                'name' => __("Reporte de cuadre"),
                'desc' => __('Compras, depositos y balances.'),
                'link' => 'pdf/inf_cuadre.php',
            ],
            [
                'name' => __("Descuento mensual"),
                'desc' => __('Informe mensual detallado por Código'),
                'link' => '#',
            ],
            [
                'name' => __("Inf. Diarios"),
                'desc' => __('Informes diario por fechas y horas.'),
                'link' => 'inf_diario_op.php',
            ],
            [
                'name' => __("T-Shirts"),
                'desc' => __('Informes de compras por tamaños.'),
                'link' => 'pdf/compras_camisas.php',
                'target' => 'T-Shirts',
            ],
            [
                'name' => __("T-Shirts Size"),
                'desc' => __('Informe por tamaño de camisas por grado resumen.'),
                'link' => 'pdf/inf_size.php',
                'target' => 'T-Shirts',
            ],
            [
                'name' => __("Inf. Compras"),
                'desc' => __('Informe de compras en cafeterias por fechas.'),
                'link' => 'inf_compras_op.php',
            ],
        ],
    ],
];

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <?php
    $title = __("Cuentas a cobrar");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Cuentas a cobrar") ?></h1>

        <div class="row row-cols-1 row-cols-md-2 mx-2 mx-md-0 justify-content-around">

            <?php foreach ($options as $option): ?>
                <div class="col mb-4">
                    <fieldset class="border border-secondary rounded-bottom h-100 px-2">
                        <legend class="w-auto"><?= $option['title'] ?></legend>
                        <div class="pb-3">
                            <div class="row row-cols-2">
                                <?php foreach ($option['buttons'] as $button): ?>
                                    <div class="col mt-1">
                                        <a style="font-size: .8em;" title="<?= $button['desc'] ?>" <?= isset($button['target']) ? "target='{$button['target']}'" : '' ?> class="btn btn-primary btn-block" href="<?= $button['link'] ?>"><?= mb_strtoupper($button['name'], 'UTF-8') ?></a>
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