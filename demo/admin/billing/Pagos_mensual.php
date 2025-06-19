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
    ["Informe mensual de pagos", "Monthly payment report"],
    ['Grabar', 'Save'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Borrar', 'Delete'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Activo', 'Active'],
    ['Costos', 'Costs'],
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
$resultado2 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {
            text-align: left;
        }

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
    <?php
    $title = $lang->translation('Informe mensual de pagos');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe mensual de pagos') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">

                <form action="pdf/Pagos_mensual.php" method="post" target="_blank">
                    <div class="style4">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                            <tr>
                                <td class="style3" colspan="5">
                                    <strong><?= $lang->translation("Selección de código") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="style6" colspan="5">
                                    <select name="desc" required style="width: 250px">
                                        <option value="Todos"><?= $lang->translation("Todos") ?></option>

                                        <?php foreach ($resultado2 as $row3): ?>
                                            <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="style3" colspan="5">
                                    <strong><?= $lang->translation("Selección del mes") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="style6" colspan="5" style="height: 26px">

                                    <select name="mes" style="width: 150px">
                                        <option value=""><?= $lang->translation("Selección") ?></option>
                                        <option value="01"><?= $lang->translation("Enero") ?></option>
                                        <option value="02"><?= $lang->translation("Febrero") ?></option>
                                        <option value="03"><?= $lang->translation("Marzo") ?></option>
                                        <option value="04"><?= $lang->translation("Abril") ?></option>
                                        <option value="05"><?= $lang->translation("Mayo") ?></option>
                                        <option value="06"><?= $lang->translation("Junio") ?></option>
                                        <option value="07"><?= $lang->translation("Julio") ?></option>
                                        <option value="08"><?= $lang->translation("Agosto") ?></option>
                                        <option value="09"><?= $lang->translation("Septiembre") ?></option>
                                        <option value="10"><?= $lang->translation("Octubre") ?></option>
                                        <option value="11"><?= $lang->translation("Noviembre") ?></option>
                                        <option value="12"><?= $lang->translation("Diciembre") ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <br />
                        <strong>
                            <input name="buscar" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                        </strong>
                        <br />
                        <br />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>