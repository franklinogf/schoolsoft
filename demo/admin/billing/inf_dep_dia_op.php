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
    ["Informe de Depositos Diarios", "Daily Deposit Report"],
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
    ['Selección', 'Selection'],
    ['Borrar todos los Cargos', 'Eliminate all costs'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');

$tabla2 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();

$fec = date("Y-m-d");
date_default_timezone_set("America/puerto_rico");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <script language="Javascript">
        document.oncontextmenu = function() {
            return false
        }

        function cambiaPalabra() {
            var dis = document.algunNombre.ho.value;
            if (dis == '2') {
                document.algunNombre.t1.disabled = false;
                document.algunNombre.t2.disabled = false;
            } else {
                document.algunNombre.t1.disabled = true;
                document.algunNombre.t2.disabled = true;
            }

        }
    </script>


    <style type="text/css">
        .style1 {
            font-size: large;
            text-align: center;
        }

        .style3 {
            text-align: center;
        }

        .style4 {
            text-align: center;
        }
    </style>
    <?php
    $title = $lang->translation('Informe de Depositos Diarios') . ' ' . $year;
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe de Depositos Diarios') . ' ' . $year ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">

                <form id="algunNombre" name="algunNombre" method="post" action="pdf/inf_dep_dia.php" target="_blank">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 70%">
                        <tr>
                            <td class="style3"><strong><?= $lang->translation("Fecha desde:") ?></strong></td>
                            <td class="style3"><strong><?= $lang->translation("Fecha hasta:") ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style4" style="height: 23px">
                                <input type='date' id='ft1' name='ft1' maxlength=10 size=10 tabindex='1' value='<?= $fec; ?>' />
                            </td>
                            <td class="style4" style="height: 23px">
                                <input type='date' id='ft2' name='ft2' maxlength=10 size=10 tabindex='1' value='<?= $fec; ?>' />
                            </td>
                        </tr>
                        <tr>
                            <td class="style4" style="height: 23px" colspan="2">
                                <select name="ho" onclick="return cambiaPalabra(); return true">
                                    <option value="1"><?= $lang->translation("Sin Horario") ?></option>
                                    <option value="2"><?= $lang->translation("Con Horario") ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style4" style="height: 23px">
                                <input type="text" class="form-control" name=t1 id="time1" required disabled="disabled" value="15:00:01" maxlength="8" />
                            </td>
                            <td class="style4" style="height: 23px">
                                <input type="text" class="form-control" name=t2 id="time2" required disabled="disabled" value="15:00:00" maxlength="8" />
                            </td>
                        </tr>
                        <tr>
                            <td class="style3" colspan="2"><strong><?= $lang->translation("Selección") ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style4" colspan="2">
                                <select name="pagos" style="width: 119px">
                                    <option value="A"><?= $lang->translation("Detallado") ?></option>
                                    <option value="B"><?= $lang->translation("Resumen") ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style4" colspan="2">
                                <strong>
                                    <input name="reporte" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style4" colspan="2">
                                &nbsp;</td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

</body>

</html>