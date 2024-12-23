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
    ["Lista de Totales por Mes", "List of Totals by Month"],
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
]);

$school = new School(Session::id());
$year = $school->info('year2');

$resultado3 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <?php
    $title = $lang->translation('Lista de Totales por Mes');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="style1">
        <?php
        Route::includeFile('/admin/includes/layouts/menu.php');
        ?>
        <div class="container-lg mt-lg-3 mb-5 px-0">
            <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de Totales por Mes') ?></h1>
            <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

                <form action="pdf/inf_por_fecha.php" method="post" target="_blank">
                    <div class="style4">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 50%">
                            <tr>
                                <td class="style1">
                                    <strong><?= $lang->translation('Selección de código') ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="style1">
                                    <select name="desc" required style="width: 250px">
                                        <option value="Selección, Selección"><?= $lang->translation('Selección') ?></option>
                                        <?php foreach ($resultado3 as $row3): ?>
                                            <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                        <?php endforeach ?>

                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="style1"><strong><?= $lang->translation('Desde') ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <center>
                                        <input type="date" class="form-control" id='cal-field-1' name=ft1 maxlength=10 size=10 tabindex='1' value='<?= date('Y-m-d') ?>' style="width: 140px" />
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td class="style1"><strong><?= $lang->translation('Hasta') ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <center>
                                        <input type="date" class="form-control" id='cal-field-2' name=ft2 maxlength=10 size=10 tabindex='1' value='<?= date('Y-m-d') ?>' style="width: 140px" />
                                    </center>
                                </td>

                            </tr>
                            <tr>
                                <td>&nbsp;</td>
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
    </div>

</body>

</html>