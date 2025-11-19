<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Informe de presupuesto por código y sumando otros", "Budget report by code and adding others"],
    ['Papel tamaño', 'Paper size'],
    ['Papel orientación', 'Paper orientation'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Con Cantidad', 'With Quantity'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Hoja Legal', 'Legal Sheet'],
    ['Hoja carta', 'Letter Sheet'],
    ['Por Cuenta', 'By Account'],
    ['Por Grado', 'By Grade'],
    ['OPCIONES', 'OPTIONS'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Por Familia', 'Per Family'],
    ['Procesar', 'Process'],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Cambiar estado', 'Change Status'],
    ['Selección de Meses', 'Month Selection'],
    ['Borrar todos los Cargos', 'Eliminate all costs'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$resultado1 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Presupuesto</title>
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
    $title = $lang->translation('Informe de presupuesto por código y sumando otros');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe de presupuesto por código y sumando otros') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">

                <form method="post" action="pdf/inf_presu2.php" target="_blank">

                    <table align="center" cellpadding="2" cellspacing="1" style="width: 50%">
                        <tr>
                            <td class="style1" style="height: 32px" colspan="3"><strong><?= $lang->translation("OPCIONES") ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <select name="desc1" required style="width: 190px">
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <?php foreach ($resultado1 as $row2): ?>
                                        <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                    <?php endforeach ?>

                                </select>
                            </td>
                            <td class="style3">
                                <select name="desc2" style="width: 190px">
                                    <option value=""><?= $lang->translation("Selección") ?></option>

                                    <?php foreach ($resultado1 as $row2): ?>
                                        <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                    <?php endforeach ?>

                                </select>
                            </td>
                            <td class="style3">
                                <select name="desc3" style="width: 190px">
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <?php foreach ($resultado1 as $row2): ?>
                                        <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                    <?php endforeach ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <input name="ca1" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cb1" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cc1" size="3" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <input name="ca2" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cb2" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cc2" size="3" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <input name="ca3" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cb3" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cc3" size="3" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <input name="ca4" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cb4" size="3" type="text" />
                            </td>
                            <td class="style3">
                                <input name="cc4" size="3" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                            </td>
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
    </div>


</body>

</html>