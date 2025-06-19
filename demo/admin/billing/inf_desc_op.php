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
    ["Listado por descripción mensual", "List by monthly description"],
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
    ['Mes', 'Month'],
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
]);

$school = new School(Session::id());
$year = $school->info('year2');
$resultado3 = DB::table('presupuesto')->where([
    ['year', $year]
])->orderBy('codigo')->get();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: x-large;
        }

        .style2 {}

        .style3 {}

        .style4 {
            text-align: center;
        }
    </style>
</head>
<?php
$title = $lang->translation('Listado por descripción mensual');
Route::includeFile('/admin/includes/layouts/header.php');
?>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Listado por descripción mensual') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

            <form method="post" action="pdf/inf_desc_in.php" target="_blank">
                <div class="style4">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 37%">
                        <tr>
                            <td class="style2"><strong><?= $lang->translation("Código") ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style3">
                                <select name="codi" style="width: 353px" required>
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <option value="T"><?= $lang->translation("Todos") ?></option>
                                    <?php foreach ($resultado3 as $row3): ?>
                                        <option value="<?= $row3->codigo ?>"><?= $row3->codigo . ', ' . $row3->descripcion ?></option>
                                    <?php endforeach ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style2"><strong><?= $lang->translation("Mes") ?></strong></td>
                        </tr>
                        <tr>
                            <td class="style3"><select name="mes" style="width: 97px" required>
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
                                </select></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br />
                    <input name="Submit1" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                    <br />
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