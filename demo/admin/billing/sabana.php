<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Lista de Deudores Descripción 30, 60, 90", "List of Debtors Description 30, 60, 90"],
    ['Hasta', 'Until'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección', 'Select'],
    ['Desde', 'From'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Fechas de Posteo', 'Posting Dates'],
    ['Fechas de Pagos', 'Payment Dates'],
    ['Sin Matrícula', 'No registration'],
    ['Procesar', 'Process'],
    ['Selección de código', 'Code selection'],
    ['Opciones', 'Options'],
    ['Fecha', 'Date'],
    ['En Orden', 'In order'],
    ['Cuentas', 'Accounts'],
    ['Estudiantes', 'Students'],
    ['Con linea', 'With line'],
    ['Sin linea', 'Without line'],
    ['', ''],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$resultado2 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <?php
    $title = $lang->translation('List of Debtors Description 30, 60, 90');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {}

        .style3 {
            text-align: center;
        }

        .style4 {
            text-align: center;
        }

        .style6 {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de Deudores Descripción 30, 60, 90') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">


            <form action="pdf/sabana_inf.php" method="post" target="_blank">
                <div class="style4">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                        <tr>
                            <td class="style3">
                                <strong><?= $lang->translation('Selección de código') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style6">
                                <select name="desc" required style="width: 250px">
                                    <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                    <?php foreach ($resultado2 as $row3): ?>
                                        <option value="<?= $row3->codigo . ', ' . $row3->descripcion ?>"><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3"><strong><?= $lang->translation('Fecha') ?></strong></td>
                        </tr>
                        <tr>
                            <td>
                                <center>
                                    <input type="date" class="form-control" type=text id='cal-field-1' name=ft1 maxlength=10 size=10 tabindex='1' value='<?= date('Y-m-d') ?>' style="width: 150px" />
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3"><strong><?= $lang->translation('En Orden') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style6"><select name="orden" style="width: 115px">
                                    <option value="1"><?= $lang->translation('Cuentas') ?></option>
                                    <option value="2"><?= $lang->translation('Estudiantes') ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="style6"><select name="cl" style="width: 115px">
                                    <option value="1"><?= $lang->translation('Con linea') ?></option>
                                    <option value="0"><?= $lang->translation('Sin linea') ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td class="style2">&nbsp;</td>
                        </tr>
                    </table>
                    <br />
                    <strong>
                        <input name="buscar" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Procesar") ?>" />
                    </strong><br />
                    <br />
                </div>
            </form>

        </div>
    </div>

</body>

</html>