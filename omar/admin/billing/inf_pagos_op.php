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
    ["Lista de Pagos por Grado", "Schedule of Payments by Grade"],
    ['Papel tamaño', 'Paper size'],
    ['Papel orientación', 'Paper orientation'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección de Papel', 'Paper Selection'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Hoja Legal', 'Legal Sheet'],
    ['Hoja carta', 'Letter Sheet'],
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
    ['Selección de código', 'Code selection'],
    ['Selección de Meses', 'Month Selection'],
    ['Selección de meses para el informe.', 'Selection of months for the report.'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$grades = $school->allGrades();

$resultado2 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Lista de deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

    <title>Untitled 1</title>

    <script language="JavaScript">
        document.oncontextmenu = function() {
            return false
        }

        function confirmar(mensaje) {
            return confirm(mensaje);
        }

        function cambiaPalabra() {
            var dis = document.algunNombre.desc.value;
            if (dis == 'D') {
                document.algunNombre.mes.disabled = false;
                document.algunNombre.ago.disabled = true;
                document.algunNombre.sep.disabled = true;
                document.algunNombre.oct.disabled = true;
                document.algunNombre.nov.disabled = true;
                document.algunNombre.dic.disabled = true;
                document.algunNombre.ene.disabled = true;
                document.algunNombre.feb.disabled = true;
                document.algunNombre.mar.disabled = true;
                document.algunNombre.abr.disabled = true;
                document.algunNombre.may.disabled = true;
                document.algunNombre.jun.disabled = true;
                document.algunNombre.jul.disabled = true;
            } else {
                document.algunNombre.mes.disabled = true;
                document.algunNombre.ago.disabled = false;
                document.algunNombre.sep.disabled = false;
                document.algunNombre.oct.disabled = false;
                document.algunNombre.nov.disabled = false;
                document.algunNombre.dic.disabled = false;
                document.algunNombre.ene.disabled = false;
                document.algunNombre.feb.disabled = false;
                document.algunNombre.mar.disabled = false;
                document.algunNombre.abr.disabled = false;
                document.algunNombre.may.disabled = false;
                document.algunNombre.jun.disabled = false;
                document.algunNombre.jul.disabled = false;
            }

        }
    </script>

    <style type="text/css">
        .style4 {
            text-align: center;
        }

        .style7 {
            text-align: left;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de Pagos por Grado') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">
                <form name="algunNombre" action="pdf/inf_pagos.php" method="post" target="_blank">
                    <div class="style4">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 80%">
                            <tr>
                                <td colspan="5">
                                    <strong><?= $lang->translation('Selección de código') ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <select name="desc" required style="width: 250px" onclick="return cambiaPalabra(); return true">
                                        <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                        <?php foreach ($resultado2 as $row3): ?>
                                            <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                        <?php endforeach ?>

                                        <option value="D">Desc. Mensualidad</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5"><br />
                                    <strong><?= $lang->translation('Selección de Papel') ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="height: 26px">

                                    <select name="pag1" style="width: 97px">
                                        <option value="Letter"><?= $lang->translation('Hoja Carta') ?></option>
                                        <option value="Legal"><?= $lang->translation('Hoja Legal') ?></option>
                                    </select>&nbsp;&nbsp;&nbsp;&nbsp;

                                    <select name="pag" style="width: 97px">
                                        <option value="P">Portrait</option>
                                        <option value="L">Landscape</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="height: 26"><br />
                                    <strong><?= $lang->translation('Selección de meses para el informe.') ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="style7"><input name="ago" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Agosto') ?></td>
                                <td class="style7"><input name="sep" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Septiembre') ?></td>
                                <td class="style7"><input name="oct" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Octubre') ?></td>
                                <td class="style7"><input name="nov" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Noviembre') ?></td>
                                <td class="style7"><input name="dic" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Diciembre') ?></td>
                            </tr>
                            <tr>
                                <td class="style7"><input name="ene" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Enero') ?></td>
                                <td class="style7"><input name="feb" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Febrero') ?></td>
                                <td class="style7"><input name="mar" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Marzo') ?></td>
                                <td class="style7"><input name="abr" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Abril') ?></td>
                                <td class="style7"><input name="may" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Mayo') ?></td>
                            </tr>
                            <tr>
                                <td class="style7"><input name="jun" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Junio') ?></td>
                                <td class="style7"><input name="jul" type="checkbox" value="1" style="width: 20px; height: 20px" />
                                    <?= $lang->translation('Julio') ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <br />
                        <strong>
                            <input class="btn btn-primary" name="buscar" style="width: 140px;" type="submit" value="<?= $lang->translation('Procesar') ?>" />
                        </strong>
                        <br />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script language="JavaScript">
        var dis = document.algunNombre.desc.value;
        if (dis == 'D') {
            document.algunNombre.mes.disabled = false;
            document.algunNombre.ago.disabled = true;
            document.algunNombre.sep.disabled = true;
            document.algunNombre.oct.disabled = true;
            document.algunNombre.nov.disabled = true;
            document.algunNombre.dic.disabled = true;
            document.algunNombre.ene.disabled = true;
            document.algunNombre.feb.disabled = true;
            document.algunNombre.mar.disabled = true;
            document.algunNombre.abr.disabled = true;
            document.algunNombre.may.disabled = true;
            document.algunNombre.jun.disabled = true;
            document.algunNombre.jul.disabled = true;
        } else {
            document.algunNombre.mes.disabled = true;
            document.algunNombre.ago.disabled = false;
            document.algunNombre.sep.disabled = false;
            document.algunNombre.oct.disabled = false;
            document.algunNombre.nov.disabled = false;
            document.algunNombre.dic.disabled = false;
            document.algunNombre.ene.disabled = false;
            document.algunNombre.feb.disabled = false;
            document.algunNombre.mar.disabled = false;
            document.algunNombre.abr.disabled = false;
            document.algunNombre.may.disabled = false;
            document.algunNombre.jun.disabled = false;
            document.algunNombre.jul.disabled = false;
        }
    </script>

</body>



</html>