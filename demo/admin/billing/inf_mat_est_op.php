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
    ['Lista de Deudores Descripción', 'List of Debtors Description'],
    ['Fecha', 'Date'],
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
    ['Selección de código', 'Code selection'],
    ['En Orden', 'In order'],
    ['Cuentas', 'Accounts'],
    ['Estudiantes', 'Students'],
    ['Con linea', 'With line'],
    ['Sin linea', 'Without line'],
]);
$school = new School(Session::id());
$year = $school->info('year2');
$resultado2 = DB::table('presupuesto')->where([
    ['year', $year]
])->orderBy('codigo')->get();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <?php
    echo '<script language="JavaScript">';
    echo "document.oncontextmenu = function(){return false}";
    echo '</script>';
    ?>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {
            background-color: #CCCCCC;
        }

        .style3 {
            text-align: center;
            background-color: #CCCCCC;
        }

        .style4 {
            text-align: center;
        }

        .style6 {
            text-align: center;
            background-color: #FFFFCC;
        }
    </style>
    <?php
    $title = $lang->translation('Lista de Deudores Descripción');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Lista de Deudores Descripción') ?>
        </h1>
        <a href="<?= Route::url('/admin/billing/') ?>" class="text-left btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">

            <form action="pdf/inf_mat_est.php" method="post" target="_blank">
                <div class="style4">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                        <tr>
                            <td>
                                <strong><?= $lang->translation("Selección de código") ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <select name="desc" required style="width: 250px">
                                    <option value="Todos"><?= $lang->translation("Todos") ?></option>
                                    <?php foreach ($resultado2 as $row2) { ?>

                                    <?php
                                        echo '<option value="' . $row2->codigo . '">' . $row2->codigo . ' ' . $row2->descripcion . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= $lang->translation("Fecha") ?></strong></td>
                        </tr>
                        <tr>
                            <td>

                                <select name="estq">
                                    <option value="1">Mat. Est. N</option>
                                    <option value="2">Mat. Est. A</option>
                                    <option value="3">Mat. Est. G</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= $lang->translation("En Orden") ?></strong></td>
                        </tr>
                        <tr>
                            <td><select name="orden" style="width: 110px">
                                    <option value="1"><?= $lang->translation("Cuentas") ?></option>
                                    <option value="2"><?= $lang->translation("Estudiantes") ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td><select name="cl" style="width: 92px">
                                    <option value="1"><?= $lang->translation("Con linea") ?></option>
                                    <option value="0"><?= $lang->translation("Sin linea") ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br />
                    <strong>
                        <button name='buscar' type="submit" class="btn btn-primary d-block mx-auto">
                            <?= $lang->translation('Continuar') ?>
                        </button>

                    </strong><br />
                    <br />
                </div>
            </form>
        </div>
    </div>

</body>

</html>