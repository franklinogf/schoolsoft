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
    ["Pasar balances", "Pass balances"],
    ['Pantalla para Pasar los Balances', 'Balance Transfer Screen'],
    ['Código', 'Code'],
    ['a', 'to'],
    ['Transferir', 'Transfer'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Por Selección', 'By Selection'],
    ['Descripci&oacute;n', 'Description'],
    ['Pasar Todo', 'Pass Everything'],
    ['Por Selección', 'By Selection'],
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
    ['Estu. Nuevo', 'New Student'],
    ['Todos', 'All'],
    ['Selecci&oacute;n', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Cambiar estado', 'Change Status'],
    ['Guardar cambios', 'Save Changes'],
    ['E', 'I'],
    ['Est&aacute;s seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
]);

$school = new School(Session::id());
$year = $school->info('year2');
list($yr1, $yr2) = explode("-", $year);
$yr3 = $yr2 + 1;
$year2 = $yr2 . '-' . $yr3;

if (isset($_POST['pasa'])) {
    $row5 = DB::table('presupuesto')->where([
        ['codigo', $_POST['pasat'] ?? ''],
        ['year', $year2]
    ])->orderBy('codigo')->first();
    $codi = $row5->codigo ?? '';

    if ($_POST['pasar'] == 'A' and $_POST['pasat'] == $codi) {
        $resultad2 = DB::table('year')->select("DISTINCT id, ss, grado, nombre, apellidos")->where([
            ['year', $year]
        ])->orderBy('id')->get();
        foreach ($resultad2 as $row2) {
            $deuda = 0;
            $pago = 0;
            $resultad3 = DB::table('pagos')->where([
                ['id', $row2->id],
                ['ss', $row2->ss],
                ['year', $year]
            ])->orderBy('id')->get();
            foreach ($resultad3 as $row3) {
                $deuda = $deuda + $row3->deuda;
                $pago = $pago + $row3->pago;
            }
            $total = 0;
            $total = $deuda - $pago;

            $re1 = DB::table('pagos')->whereRaw("id=$row2->id and ss='$row2->ss' and year='$year2' and codigo=" . $_POST['pasat'] . "")->orderBy('id')->first();

            $can = $re1->ss ?? '';
            if ($total > 0 and $can == '') {
                DB::table('pagos')->insert([
                    'id' => $row2->id,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'desc1' => $row5->descripcion,
                    'fecha_d' => $_POST['fec1'],
                    'year' => $year2,
                    'codigo' => $_POST['pasat'],
                    'ss' => $row2->ss,
                    'grado' => $row2->grado,
                    'deuda' => $total,
                ]);
            }
        }
    }
    if ($_POST['pasar'] == 'B') {
        $resultad2 = DB::table('year')->select("DISTINCT id, ss, grado, nombre, apellidos")->where([
            ['year', $year]
        ])->orderBy('id')->get();
        foreach ($resultad2 as $row2) {
            $deuda = 0;
            $pago = 0;
            $row5 = DB::table('presupuesto')->where([
                ['codigo', $_POST['ida1']],
                ['year', $year]
            ])->orderBy('codigo')->first();

            $resultad3 = DB::table('pagos')->where([
                ['codigo', $_POST['ida1']],
                ['id', $row2->id],
                ['ss', $row2->ss],
                ['year', $year]
            ])->orderBy('id')->get();

            foreach ($resultad3 as $row3) {
                $deuda = $deuda + $row3->deuda;
                $pago = $pago + $row3->pago;
            }
            $total = 0;
            $total = $deuda - $pago;

            $re1 = DB::table('pagos')->whereRaw("id=$row2->id and ss='$row2->ss' and year='$year2' and codigo=" . $_POST['idb1'] . "")->orderBy('id')->first();

            $can = $re1->ss ?? '';
            if ($total > 0 and $row5->codigo ?? 0 > 0 and $can == '') {
                DB::table('pagos')->insert([
                    'id' => $row2->id,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'desc1' => $row5->descripcion,
                    'fecha_d' => $_POST['fec2'],
                    'year' => $year2,
                    'codigo' => $_POST['idb1'],
                    'ss' => $row2->ss,
                    'grado' => $row2->grado,
                    'deuda' => $total,
                ]);
            }

            $deuda = 0;
            $pago = 0;
            $row5 = DB::table('presupuesto')->where([
                ['codigo', $_POST['ida2']],
                ['year', $year]
            ])->orderBy('codigo')->first();
            $resultad3 = DB::table('pagos')->where([
                ['id', $row2->id],
                ['ss', $row2->ss],
                ['year', $year]
            ])->orderBy('id')->get();
            foreach ($resultad3 as $row3) {
                $deuda = $deuda + $row3->deuda;
                $pago = $pago + $row3->pago;
            }
            $total = 0;
            $total = $deuda - $pago;
            if (!empty($_POST['idb2'])) {
                $re1 = DB::table('pagos')->whereRaw("id=$row2->id and ss='$row2->ss' and year='$year2' and codigo=" . $_POST['idb2'] . "")->orderBy('id')->first();
            }

            $can = $re1->ss ?? '';
            if ($total > 0 and $row5->codigo ?? 0 > 0 and $can == '') {
                DB::table('pagos')->insert([
                    'id' => $row2->id,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'desc1' => $row5->descripcion,
                    'fecha_d' => $_POST['fec2'],
                    'year' => $year2,
                    'codigo' => $_POST['idb2'],
                    'ss' => $row2->ss,
                    'grado' => $row2->grado,
                    'deuda' => $total,
                ]);
            }

            $deuda = 0;
            $pago = 0;
            $row5 = DB::table('presupuesto')->where([
                ['codigo', $_POST['ida3']],
                ['year', $year]
            ])->orderBy('codigo')->first();
            $resultad3 = DB::table('pagos')->where([
                ['codigo', $_POST['ida3']],
                ['id', $row2->id],
                ['ss', $row2->ss],
                ['year', $year]
            ])->orderBy('id')->get();

            foreach ($resultad3 as $row3) {
                $deuda = $deuda + $row3->deuda;
                $pago = $pago + $row3->pago;
            }
            $total = 0;
            $total = $deuda - $pago;
            if (!empty($_POST['idb3'])) {
                $re1 = DB::table('pagos')->whereRaw("id=$row2->id and ss='$row2->ss' and year='$year2' and codigo=" . $_POST['idb3'] . "")->orderBy('id')->first();
            }

            $can = $re1->ss ?? '';
            if ($total > 0 and $row5->codigo ?? 0 > 0 and $can == '') {
                DB::table('pagos')->insert([
                    'id' => $row2->id,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'desc1' => $row5->descripcion,
                    'fecha_d' => $_POST['fec2'],
                    'year' => $year2,
                    'codigo' => $_POST['idb3'],
                    'ss' => $row2->ss,
                    'grado' => $row2->grado,
                    'deuda' => $total,
                ]);
            }

            $deuda = 0;
            $pago = 0;
            $row5 = DB::table('presupuesto')->where([
                ['codigo', $_POST['ida4']],
                ['year', $year]
            ])->orderBy('codigo')->first();
            $resultad3 = DB::table('pagos')->where([
                ['codigo', $_POST['ida4']],
                ['id', $row2->id],
                ['ss', $row2->ss],
                ['year', $year]
            ])->orderBy('id')->get();
            foreach ($resultad3 as $row3) {
                $deuda = $deuda + $row3->deuda;
                $pago = $pago + $row3->pago;
            }
            $total = 0;
            $total = $deuda - $pago;
            if (!empty($_POST['idb4'])) {
                $re1 = DB::table('pagos')->whereRaw("id=$row2->id and ss='$row2->ss' and year='$year2' and codigo=" . $_POST['idb4'] . "")->orderBy('id')->first();
            }

            $can = $re1->ss ?? '';
            if ($total > 0 and $row5->codigo ?? 0 > 0 and $can == '') {
                DB::table('pagos')->insert([
                    'id' => $row2->id,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'desc1' => $row5->descripcion,
                    'fecha_d' => $_POST['fec2'],
                    'year' => $year2,
                    'codigo' => $_POST['idb4'],
                    'ss' => $row2->ss,
                    'grado' => $row2->grado,
                    'deuda' => $total,
                ]);
            }
        }
    }
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

    <title>Untitled 1</title>
    <script language="JavaScript">
        document.oncontextmenu = function() {
            return false
        }

        function confirmar(mensaje) {
            return confirm(mensaje);
        }

        function cambiaPalabra() {
            var dis = document.algunNombre.pasar.value;
            if (dis == 'B') {
                document.algunNombre.ida1.disabled = false;
                document.algunNombre.ida2.disabled = false;
                document.algunNombre.ida3.disabled = false;
                document.algunNombre.ida4.disabled = false;
                document.algunNombre.idb1.disabled = false;
                document.algunNombre.idb2.disabled = false;
                document.algunNombre.idb3.disabled = false;
                document.algunNombre.idb4.disabled = false;
                document.algunNombre.pasat.disabled = true;
                document.algunNombre.fec1.disabled = true;
                document.algunNombre.fec2.disabled = false;
                document.getElementById('cal-button-1').disabled = true;
                document.getElementById('cal-button-2').disabled = false;
            } else {
                document.algunNombre.ida1.disabled = true;
                document.algunNombre.ida2.disabled = true;
                document.algunNombre.ida3.disabled = true;
                document.algunNombre.ida4.disabled = true;
                document.algunNombre.idb1.disabled = true;
                document.algunNombre.idb2.disabled = true;
                document.algunNombre.idb3.disabled = true;
                document.algunNombre.idb4.disabled = true;
                document.algunNombre.fec1.disabled = false;
                document.algunNombre.fec2.disabled = true;
                document.algunNombre.pasat.disabled = false;
                document.getElementById('cal-button-2').disabled = true;
                document.getElementById('cal-button-1').disabled = false;
            }
        }
    </script>
    <style type="text/css">
        input:required:invalid,
        input:focus:invalid {
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeVJREFUeNqkU01oE1EQ/mazSTdRmqSxLVSJVKU9RYoHD8WfHr16kh5EFA8eSy6hXrwUPBSKZ6E9V1CU4tGf0DZWDEQrGkhprRDbCvlpavan3ezu+LLSUnADLZnHwHvzmJlvvpkhZkY7IqFNaTuAfPhhP/8Uo87SGSaDsP27hgYM/lUpy6lHdqsAtM+BPfvqKp3ufYKwcgmWCug6oKmrrG3PoaqngWjdd/922hOBs5C/jJA6x7AiUt8VYVUAVQXXShfIqCYRMZO8/N1N+B8H1sOUwivpSUSVCJ2MAjtVwBAIdv+AQkHQqbOgc+fBvorjyQENDcch16/BtkQdAlC4E6jrYHGgGU18Io3gmhzJuwub6/fQJYNi/YBpCifhbDaAPXFvCBVxXbvfbNGFeN8DkjogWAd8DljV3KRutcEAeHMN/HXZ4p9bhncJHCyhNx52R0Kv/XNuQvYBnM+CP7xddXL5KaJw0TMAF8qjnMvegeK/SLHubhpKDKIrJDlvXoMX3y9xcSMZyBQ+tpyk5hzsa2Ns7LGdfWdbL6fZvHn92d7dgROH/730YBLtiZmEdGPkFnhX4kxmjVe2xgPfCtrRd6GHRtEh9zsL8xVe+pwSzj+OtwvletZZ/wLeKD71L+ZeHHWZ/gowABkp7AwwnEjFAAAAAElFTkSuQmCC);
            background-position: right top;
            background-repeat: no-repeat;
            -moz-box-shadow: none;
        }

        input:required:valid {
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAepJREFUeNrEk79PFEEUx9/uDDd7v/AAQQnEQokmJCRGwc7/QeM/YGVxsZJQYI/EhCChICYmUJigNBSGzobQaI5SaYRw6imne0d2D/bYmZ3dGd+YQKEHYiyc5GUyb3Y+77vfeWNpreFfhvXfAWAAJtbKi7dff1rWK9vPHx3mThP2Iaipk5EzTg8Qmru38H7izmkFHAF4WH1R52654PR0Oamzj2dKxYt/Bbg1OPZuY3d9aU82VGem/5LtnJscLxWzfzRxaWNqWJP0XUadIbSzu5DuvUJpzq7sfYBKsP1GJeLB+PWpt8cCXm4+2+zLXx4guKiLXWA2Nc5ChOuacMEPv20FkT+dIawyenVi5VcAbcigWzXLeNiDRCdwId0LFm5IUMBIBgrp8wOEsFlfeCGm23/zoBZWn9a4C314A1nCoM1OAVccuGyCkPs/P+pIdVIOkG9pIh6YlyqCrwhRKD3GygK9PUBImIQQxRi4b2O+JcCLg8+e8NZiLVEygwCrWpYF0jQJziYU/ho2TUuCPTn8hHcQNuZy1/94sAMOzQHDeqaij7Cd8Dt8CatGhX3iWxgtFW/m29pnUjR7TSQcRCIAVW1FSr6KAVYdi+5Pj8yunviYHq7f72po3Y9dbi7CxzDO1+duzCXH9cEPAQYAhJELY/AqBtwAAAAASUVORK5CYII=);
            background-position: right top;
            background-repeat: no-repeat;
        }

        .style1 {
            font-size: xx-small;
        }

        .style2 {
            font-size: large;
            text-align: center;
        }

        .style3 {
            text-align: center;
        }

        .style5 {
            background-color: #CCCCCC;
        }

        .style6 {
            background-color: #FFFFCC;
            text-align: center;
        }

        .style7 {
            background-color: #CCCCCC;
            text-align: center;
        }
    </style>
    <?php
    $title = $lang->translation('Pasar balances');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Pantalla para Pasar los Balances') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">


                <form name="algunNombre" method="post">
                    <div class="style3">
                        <p class="style1">&nbsp;</p>
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 50%">
                            <tr>
                                <td class="style7"><strong><?= $lang->translation("Opciones") ?></strong></td>
                            </tr>
                            <tr>
                                <td class="gris">
                                    <select name="pasar" style="width: 102px" onclick="return cambiaPalabra(); return true">
                                        <option value="A"><?= $lang->translation("Pasar Todo") ?></option>
                                        <option value="B"><?= $lang->translation("Por Selección") ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="style7">
                                    <strong><?= $lang->translation("Todos") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td color="gris">
                                    <input maxlength="7" name="pasat" size="7" type="text" required />&nbsp;&nbsp;
                                    <input type='date' id='cal-field-1' name='fec1' size='10' maxlength='10' required />
                                </td>
                            </tr>
                            <tr>
                                <td class="style7">
                                    <strong><?= $lang->translation("Por Selección") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input maxlength="7" name="ida1" size="7" type="text" disabled="" required />&nbsp;
                                    <?= $lang->translation("a") ?>&nbsp;
                                    <input maxlength="7" name="idb1" size="7" type="text" disabled="" required />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input maxlength="7" name="ida2" size="7" type="text" disabled="" />&nbsp; <?= $lang->translation("a") ?>&nbsp;
                                    <input maxlength="7" name="idb2" size="7" type="text" disabled="" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input maxlength="7" name="ida3" size="7" type="text" disabled="" />&nbsp; <?= $lang->translation("a") ?>&nbsp;
                                    <input maxlength="7" name="idb3" size="7" type="text" disabled="" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input maxlength="7" name="ida4" size="7" type="text" disabled="" />&nbsp; <?= $lang->translation("a") ?>&nbsp;
                                    <input maxlength="7" name="idb4" size="7" type="text" disabled="" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type='date' id='cal-field-2' name='fec2' size='10' maxlength='10' disabled="" required />
                                </td>
                            </tr>
                            <tr>
                                <td class="style5">&nbsp;</td>
                            </tr>
                        </table>
                        <p class="style1">&nbsp;</p>
                        <strong>
                            <input class="btn btn-primary" name="pasa" style="width: 130px" type="submit" value="<?= $lang->translation('Transferir') ?>" />
                        </strong>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>