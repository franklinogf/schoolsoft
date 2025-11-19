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
    ["Pantalla para enviar mensajes de cobros a los deudores.", "Screen to send a late payment message to debtors."],
    ['Papel tamaño', 'Paper size'],
    ['Deuda en:', 'Debt on:'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Fecha de atraso', 'Late date'],
    ['Título', 'Title'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Mensaje', 'Message'],
    ['Enviar', 'Send'],
    ['Por Cuenta', 'By Account'],
    ['Por Grado', 'By Grade'],
    ['', ''],
    ['Opciones', 'Options'],
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
$grades = $school->allGrades();

$tabla12 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

    <title>Untitled 2</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {
            text-align: center;
        }

        .style3 {}

        .style4 {
            text-align: center;
            font-size: xx-small;
        }

        .style5 {
            text-align: center;
        }

        .style6 {
            text-align: center;
        }

        .style7 {
            font-size: xx-small;
        }
    </style>
    <?php
    $title = $lang->translation('Lista de deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Pantalla para enviar mensajes de cobros a los deudores.') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">
                <form name="algunNombre" action="pdf/cobros_testos_inf.php" method="post" target="_blank">
                    <div class="style6">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 27%">
                            <tr>
                                <td class="style2"><strong><?= $lang->translation('Opciones') ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style2"><strong><?= $lang->translation('Deuda en:') ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style3">
                                    <select name="desc" required style="width: 240px">
                                        <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                        <?php foreach ($tabla12 as $row2): ?>
                                            <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="style2"><strong><?= $lang->translation('Fecha de atraso') ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style5">
                                    <center>
                                        <input type="date" class="form-control" id='cal-field-1' name='fec1' maxlength=10 size=10 tabindex='1' value='<?= date('Y-m-d') ?>' style="width: 140px" />
                                    </center>
                                </td>
                            </tr>
                            <tr>
                                <td class="style2"><strong>E-Mail</strong></td>
                            </tr>
                            <tr>
                                <td class="style5"><select name="email" required style="width: 100px">
                                        <option value=""><?= $lang->translation('Selección') ?></option>
                                        <option value="E">E-Mail</option>
                                        <option value="C">SMS</option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td class="style2"><strong><?= $lang->translation('Título') ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style3">
                                    <input name="titulo" required style="width: 230px" type="text" />
                                </td>
                            </tr>
                            <tr>
                                <td class="style2"><strong><?= $lang->translation('Mensaje') ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style3">
                                    <textarea name="text" required style="width: 238px; height: 77px"></textarea>
                                </td>
                            </tr>
                        </table>
                        <p class="style7">&nbsp;</p>
                        <input class="btn btn-primary" name="Submit1" style="width: 140px;" type="submit" value="<?= $lang->translation('Enviar') ?>" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>