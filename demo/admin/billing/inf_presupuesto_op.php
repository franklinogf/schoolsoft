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
    ["Informe de Presupuesto", "Budget Report"],
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
    ['Con Matrícula', 'With registration'],
    ['', ''],
    ['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$resultado3 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Códigos de Presupuesto');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <title>Untitled 2</title>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe de Presupuesto') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

            <form action="pdf/inf_presupuesto.php" method="post" target="_blank">
                <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                    <tr>
                        <td>
                            <strong><?= $lang->translation('Código') ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select name="desc" required style="width: 190px">
                                <option value="Selección"><?= $lang->translation('Selección') ?></option>
                                <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                <?php foreach ($resultado3 as $row3): ?>
                                    <option><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?= $lang->translation('Tipo de Fechas') ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select name="sfec" style="width: 144px">
                                <option value="1"><?= $lang->translation('Fechas de Posteo') ?></option>
                                <option value="2"><?= $lang->translation('Fechas de Pagos') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->translation('Desde') ?></strong></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="date" class="form-control" id='cal-field-1' name='fec1' size='10' maxlength='10' value='<?= date('Y-m-d') ?>' />
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->translation('Hasta') ?></strong></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="date" class="form-control" id='cal-field-2' name='fec2' size='10' maxlength='10' value='<?= date('Y-m-d') ?>' />
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><select name="codi">
                                <option value="3"><?= $lang->translation('Todos') ?></option>
                                <option value="1"><?= $lang->translation('Sin Matrícula') ?></option>
                                <option value="2"><?= $lang->translation('Solo Matrícula') ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <p>
                </p>
                <input name="Submit1" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
            </form>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>