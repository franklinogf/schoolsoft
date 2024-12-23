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
    ['Pantalla para buscar Recibos', 'Screen to search for Receipts'],
    ['Recibos', 'Receipts'],
    ['Fecha hasta:', 'Date until:'],
    ['Atrás', 'Go back'],
    ['Todos', 'All'],
    ['Seleccionar', 'Selec'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
    ['Fecha', 'Date'],
    ['Descripción', 'Description'],
    ['Cantidad', 'Quantity'],
    ['Cuenta', 'Account'],
    ['Número', 'Number'],
    ['Cheque', 'Check'],
    ['Nomina', 'Payroll'],
    ['Banco', 'Bank'],
    ['Recibo', 'Receipt'],
    ['Giro', 'Money Order'],
    ['Tarjeta C.', 'Credit Card'],
    ['', ''],
]);
$school = new School(Session::id());
$year = $school->info('year2');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style2 {
            text-align: center;
        }

        .style4 {}

        .style5 {
            background-color: #D3D3D3;
            text-align: center;
        }

        .style6 {
            text-align: right;
        }

        .style8 {
            background-color: #FFFFCC;
            text-align: left;
        }

        .style9 {
            text-align: left;
        }
    </style>
    <?php
    $title = $lang->translation('Pantalla para buscar Recibos');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Pantalla para buscar Recibos') ?>
        </h1>
        <a href="<?= Route::url('/admin/billing/') ?>" class="text-left btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">

            <p>&nbsp;</p>
            <form method="post">
                <div class="style2">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 40%">
                        <tr>
                            <td><strong><?= $lang->translation("Número") ?></strong></td>
                        </tr>
                        <tr>
                            <td><select name="cn" style="width: 104px">
                                    <option value="nuchk"><?= $lang->translation("Cheque") ?></option>
                                    <option value="nuchk"><?= $lang->translation("ATH") ?></option>
                                    <option value="nuchk"><?= $lang->translation("Tarjeta C.") ?></option>
                                    <option value="nuchk"><?= $lang->translation("Giro") ?></option>
                                    <option value="nuchk"><?= $lang->translation("Nomina") ?></option>
                                    <option value="nuchk"><?= $lang->translation("Banco") ?></option>
                                    <option value="rec"><?= $lang->translation("Recibo") ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>
                                <input name="num" style="width: 90px" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br />
                    <button name='bus' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Buscar') ?>
                    </button>
                    <br /><br />

                </div>
            </form>
            <?php
            if (isset($_POST['bus']) and $_POST['num'] ?? 0 > 0) {
                $camp = $_POST['cn'] ?? '';
                $busca = $_POST['num'] ?? 0;
                $rest3 = DB::table('pagos')->where([
                    [$camp, $busca]
                ])->orderBy('codigo')->get();

            ?>
                <table align="center" cellpadding="2" cellspacing="0" style="width: 77%">
                    <tr>
                        <td class="style5"><strong><?= $lang->translation("Cuenta") ?></strong></td>
                        <td class="style5"><strong><?= $lang->translation("Recibos") ?></strong></td>
                        <td class="style5"><strong><?= $lang->translation("Descripción") ?></strong></td>
                        <td class="style5"><strong><?= $lang->translation("Cantidad") ?></strong></td>
                        <td class="style5"><strong><?= $lang->translation("Fecha") ?></strong></td>
                    </tr>
                    <?php foreach ($rest3 as $row3) { ?>
                        <tr>
                            <td><?= $row3->id; ?></td>
                            <td><?= $row3->rec; ?></td>
                            <td><?= $row3->desc1; ?></td>
                            <td><?= $row3->pago; ?></td>
                            <td><?= $row3->fecha_p; ?> </td>
                        </tr>
                        <tr>
                            <td><?= $row3->fecha_r; ?></td>
                            <td class="style8" colspan="4"><?= $row3->razon; ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <br /><br />
                <br /><br />

            <?php
            }
            ?>
        </div>
    </div>
    </div>
</body>

</html>