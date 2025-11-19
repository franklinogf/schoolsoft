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
    ["Informe por descripción", "Report by description"],
    ['Hasta', 'Until'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección', 'Select'],
    ['Desde', 'From'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['OPCIONES', 'OPTIONS'],
    ['Fechas de Pagos', 'Payment Dates'],
    ['Sin Matrícula', 'No registration'],
    ['Con Matrícula', 'With registration'],
    ['', ''],
    ['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$resultado1 = DB::table('pagos')->select("DISTINCT desc1, codigo")->where([
    ['year', $year]
])->orderBy('desc1')->get();

$resultado2 = $resultado1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <style type="text/css">
        .style1 {
            text-align: center;
        }

        .style3 {
            text-align: center;
        }

        .style4 {
            text-align: center;
        }

        .style5 {
            text-align: center;
            font-size: large;
        }
    </style>
    <?php
    $title = $lang->translation('Informe por descripción');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe por descripción') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

            <form method="post" action="pdf/inf_descp.php" target="_blank">

                <table align="center" cellpadding="2" cellspacing="1" style="width: 50%">
                    <tr>
                        <td class="style1" style="height: 32px" colspan="2"><strong><?= $lang->translation("OPCIONES") ?></strong></td>
                    </tr>
                    <tr>
                        <td class="style3">
                            &nbsp;</td>
                        <td class="style3">
                            &nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <select name="desc1" required style="width: 190px">
                                <option value=""><?= $lang->translation("Selección") ?></option>
                                <?php foreach ($resultado1 as $row3): ?>
                                    <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->desc1 ?></option>
                                <?php endforeach ?>

                            </select>
                        </td>
                        <td class="style3">
                            <select name="desc2" style="width: 190px">
                                <option value=""><?= $lang->translation("Selección") ?></option>
                                <?php foreach ($resultado2 as $row3): ?>
                                    <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->desc1 ?></option>
                                <?php endforeach ?>

                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="style3">
                        </td>
                        <td class="style3">
                        </td>
                    </tr>
                </table>

                <br />
                <div class="style4">
                    <input name="Submit1" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                </div>
            </form>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>