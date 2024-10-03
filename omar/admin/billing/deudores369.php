<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Lista de deudores 30, 60, 90", "List of debtors 30, 60, 90"],
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
    ['Selección de código', 'Code selection'],
    ['Opciones', 'Options'],
    ['En Orden', 'In order'],
    ['Fecha', 'Date'],
    ['Cuentas', 'Accounts'],
    ['Estudiantes', 'Students'],
    ['Con Teléfonos', 'With Telephones'],
    ['Con linea', 'With line'],
    ['Sin linea', 'Without line'],
    ['', ''],
    ['', ''],
    ['', ''],
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

$tabla12 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
    $title = $lang->translation('Lista de deudores 30, 60, 90');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de deudores 30, 60, 90') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">

                <form action="pdf/deudores369_inf.php" method="post" target="_blank">
                    <div>
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                            <tr>
                                <td>
                                    <strong><?= $lang->translation('Selección de código') ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="desc" required style="width: 250px">
                                        <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                        <?php foreach ($tabla12 as $row2): ?>
                                            <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Fecha') ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" class="form-control" name=ft1 maxlength=10 size=10 tabindex='1' value='<?= date('Y-m-d') ?>' style="width: 140px" />
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('En Orden') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="orden" style="width: 110px">
                                        <option value="1"><?= $lang->translation('Cuentas') ?></option>
                                        <option value="2"><?= $lang->translation('Estudiantes') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><select name="cl" style="width: 92px">
                                        <option value="1"><?= $lang->translation('Con linea') ?></option>
                                        <option value="0"><?= $lang->translation('Sin linea') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Con Teléfonos') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="ct">
                                        <option value="No">No</option>
                                        <option value="Si"><?= $lang->translation('Si') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <br />
                        <br />
                        <strong>
                            <center>
                                <input class="btn btn-primary form-control" name="buscar" type="submit" value="<?= $lang->translation('Procesar') ?>" style="width: 129px;" />
                            </center>
                        </strong><br />
                        <br />
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>