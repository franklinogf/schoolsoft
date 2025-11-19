<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Pantalla de deudores', 'Debtors screen'],
    ['Recibos', 'Receipts'],
    ['Continuar', 'Continue'],
    ['Atrás', 'Go back'],
    ['Todos', 'All'],
    ['Seleccionar', 'Selec'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
    ['Selección del Código', 'Code Selection'],
    ['Descripción', 'Description'],
    ['Cantidad', 'Quantity'],
    ['Cuenta', 'Account'],
    ['Número', 'Number'],
    ['Cheque', 'Check'],
    ['Nomina', 'Payroll'],
    ['Banco', 'Bank'],
    ['Recibo', 'Receipt'],
    ['Giro', 'Money Order'],
    ['Tarjeta C.', 'Credit Card'],
    ['', ''],
]);
$school = new School(Session::id());
$year = $school->info('year2');
$result2 = DB::table('presupuesto')->where([
    ['year', $year]
])->orderBy('codigo')->get();
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: x-large;
        }

        .style2 {
            background-color: #CCCCCC;
        }

        .style3 {
            background-color: #FFFFCC;
        }

        .style4 {
            text-align: center;
        }
    </style>
    <?php
    $title = $lang->translation('Pantalla de deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Pantalla de deudores') ?>
        </h1>
        <a href="<?= Route::url('/admin/billing/') ?>" class="text-left btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">

            <form method="post" action="pdf/deudores2.php" target="_blank">
                <div class="style4">

                    <table align="center" cellpadding="2" cellspacing="0" style="width: 31%">
                        <tr>
                            <td><strong><?= $lang->translation("Selección del Código") ?></strong></td>
                        </tr>
                        <tr>
                            <td><select name="cods" style="width: 259px">
                                    <option value="A"><?= $lang->translation("Todos") ?></option>
                                    <?php foreach ($result2 as $row2) { ?>

                                    <?php
                                        echo '<option value="' . $row2->codigo . '">' . $row2->codigo . ' ' . $row2->descripcion . '</option>';
                                    }
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <p>&nbsp;</p>
                    <button name='Submit1' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>

                </div>
            </form>
        </div>
    </div>

</body>

</html>