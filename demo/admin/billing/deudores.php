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
    ["Lista de deudores", "List of debtors"],
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

<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    document.oncontextmenu = function() {
        return false
    }

    function confirmar(mensaje) {
        return confirm(mensaje);
    }
</script>

<head>
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
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de deudores') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">
                <form name="algunNombre" action="pdf/deudores_inf.php" method="post" target="_blank">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 29%">
                        <tr>
                            <td class="style3">
                                <strong><?= $lang->translation('Opciones') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <select name="desc" style="width: 215px" onclick="return cambiaPalabra(); return true">
                                    <option value="Selección"><?= $lang->translation('Selección') ?></option>
                                    <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                    <?php foreach ($tabla12 as $row2): ?>
                                        <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                    <?php endforeach ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <strong><?= $lang->translation('Papel tamaño') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <select name="pag1" style="width: 119px">
                                    <option value="Letter"><?= $lang->translation('Hoja Carta') ?></option>
                                    <option value="Legal"><?= $lang->translation('Hoja Legal') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <strong><?= $lang->translation('Papel orientación') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <select name="pag" style="width: 106px">
                                    <option value="P">Portrait</option>
                                    <option value="L">Landscape</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <strong><?= $lang->translation('Con Cantidad') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <select name="cct" style="width: 61px">
                                    <option value="1"><?= $lang->translation('Si') ?></option>
                                    <option value="2"><?= $lang->translation('No') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <select name="gru" style="width: 136px">
                                    <option value="A"><?= $lang->translation('Por Grado') ?></option>
                                    <option value="B"><?= $lang->translation('Por Cuenta') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style6">
                                <strong><?= $lang->translation('Selección de Meses') ?></strong>
                            </td>
                        </tr>
                    </table>
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 550px">
                        <tr>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Agosto') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Septiembre') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Octubre') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Noviembre') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Diciembre') ?></strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <center>
                                    <input name="ago" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="sep" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="oct" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="nov" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="dic" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Enero') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Febrero') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Marzo') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Abril') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Mayo') ?></strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <center>
                                    <input name="ene" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="feb" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="mar" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="abr" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="may" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Matri/Junio') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong><?= $lang->translation('Julio') ?></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong></strong></center>
                            </td>
                            <td class="style1">
                                <center><strong></strong></center>
                            </td>
                        </tr>
                        <tr>
                            <td class="style5">
                                <center>
                                    <input name="jun" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                                <center>
                                    <input name="jul" type="checkbox" value="1" style="width: 20px; height: 20px">
                                </center>
                            </td>
                            <td class="style5">
                            </td>
                            <td class="style5">
                            </td>
                            <td class="style5">
                            </td>
                        </tr>
                        <tr>
                            <td class="style3" colspan="5">
                                <center><strong>
                                        <input class="btn btn-primary" name="pro" style="width: 140px;" type="submit" value="<?= $lang->translation('Procesar') ?>" /></strong></center>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>
        <?php
        $jqMask = true;
        Route::includeFile('/includes/layouts/scripts.php', true);
        ?>

</body>

</html>