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
    ["Estado de cuenta", "Statement"],
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
    ['El mes para aplicar', 'The month to apply'],
    ['Número de Cuenta', 'Account number'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
    ['Mensaje', 'Message'],
    ['Enviar los estados a las cuentas de los padres?', "Send statements to parent's accounts?"],
    ['Enviar los estados por E-Mail?', 'Send the states by E-Mail?'],
    ['Mes', 'Month'],
    ['Con Estudiantes', 'With Students'],
    ['Sin Estudiantes', 'Without Students'],
    ['Mensajes para el Estado de Cuenta', 'Account Statement Messages'],
    ['Idioma del Estado de Cuenta', 'Account Statement Language'],
    ['Inglés', 'English'],
    ['Mensajes en español', 'Messages in Spanish'],
    ['Mensajes en inglés', 'Messages in English'],
    ['Está seguro que desea eliminar el mensaje?', 'Are you sure you want to delete the message?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');

if (isset($_POST['bor'])) {
    DB::table('codigos')->where('codigo', $_REQUEST['num1'])->delete();
}

if (isset($_POST['gra'])) {

    if (empty($_POST['num1']) and !empty($_POST['tema']) or empty($_POST['num1']) and !empty($_POST['tema2'])) {
        DB::table('codigos')->insert([
            'tema' => $_POST['tema'],
            'tema2' => $_POST['tema2'],
            'idc' => '2',
        ]);
    } else {
        $thisCourse2 = DB::table("codigos")->where([
            ['codigo', $_POST['num1']],
            ['idc', '2']
        ])->update([
            'tema' => $_POST['tema'],
            'tema2' => $_POST['tema2'],
        ]);
    }
}

if (isset($_POST['bus'])) {
    $row1 = DB::table('codigos')->whereRaw("idc='2' and codigo='" . $_POST['num'] . "'")->orderBy('codigo')->first();
}

$resultado3 = DB::table('year')->whereRaw("year='$year' and activo=''")->orderBy('apellidos, nombre')->get();

$result = DB::table('codigos')->whereRaw("idc='2'")->orderBy('codigo')->get();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
    $title = $lang->translation('Estado de cuenta');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: x-large;
        }

        .style2 {
            background-color: #CCCCCC;
        }

        .style3 {
            text-align: center;
            background-color: #CCCCCC;
        }

        .style4 {
            text-align: center;
            background-color: #FFFFCC;
        }

        .style6 {
            text-align: center;
        }

        .style8 {
            font-weight: bold;
            text-align: center;
            background-color: #FFFFCC;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Estado de cuenta') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">
                <form name="algunNombre" action="pdf/estados_inf.php" method="post" target="_blank">

                    <div class="style6">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 45%">
                            <tr>
                                <td><strong><?= $lang->translation('Selección') ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="ctas" type="text" placeholder="<?= $lang->translation('Número de Cuenta') ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="nombre" style="width: 353px">
                                        <option value="1"><?= $lang->translation('Todos') ?></option>
                                        <?php foreach ($resultado3 as $row3): ?>

                                            <option value="<?= $row3->id ?>"><?= $row3->apellidos . ', ' . $row3->nombre . ', ' . $row3->id ?></option>

                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><select name="deuda" style="width: 121px">
                                        <option value="1"><?= $lang->translation('Todos') ?></option>
                                        <option value="2"><?= $lang->translation('Deudores') ?></option>
                                        <option value="3"><?= $lang->translation('Atrasados') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><select name="conest" style="width: 193px">
                                        <option value="1"><?= $lang->translation('Con Estudiantes') ?></option>
                                        <option value="2"><?= $lang->translation('Sin Estudiantes') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Mensaje') ?></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="num2" style="width: 99px">
                                        <option><?= $lang->translation('Selección') ?></option>
                                        <?php foreach ($result as $row): ?>
                                            <option><?= $row->codigo ?></option>
                                        <?php endforeach ?>

                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('El mes para aplicar') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="mes" style="width: 97px">
                                        <option value="0"><?= $lang->translation('Mes') ?></option>
                                        <option value="01"><?= $lang->translation('Enero') ?></option>
                                        <option value="02"><?= $lang->translation('Febrero') ?></option>
                                        <option value="03"><?= $lang->translation('Marzo') ?></option>
                                        <option value="04"><?= $lang->translation('Abril') ?></option>
                                        <option value="05"><?= $lang->translation('Mayo') ?></option>
                                        <option value="06"><?= $lang->translation('Junio') ?></option>
                                        <option value="07"><?= $lang->translation('Julio') ?></option>
                                        <option value="08"><?= $lang->translation('Agosto') ?></option>
                                        <option value="09"><?= $lang->translation('Septiembre') ?></option>
                                        <option value="10"><?= $lang->translation('Octubre') ?></option>
                                        <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                        <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Idioma del Estado de Cuenta') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="idi" style="width: 104px">
                                        <option><?= $lang->translation('Español') ?></option>
                                        <option><?= $lang->translation('Inglés') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Enviar los estados a las cuentas de los padres?') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="envia" style="width: 57px">
                                        <option value="No">No</option>
                                        <option value="Si"><?= $lang->translation('Si') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td><strong><?= $lang->translation('Enviar los estados por E-Mail?') ?></strong></td>
                            </tr>
                            <tr>
                                <td><select name="enviae" style="width: 57px">
                                        <option value="No">No</option>
                                        <option value="Si"><?= $lang->translation('Si') ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        &nbsp;<br />
                        <strong>
                            <input class="btn btn-primary" name="Submit1" style="width: 140px;" type="submit" value="<?= $lang->translation('Procesar') ?>" />

                        </strong>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensajes para el Estado de Cuenta') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">
            </div>
            <div class="div">

                <form method="post" action="Statement.php">

                    <?php
                    $result = DB::table('codigos')->whereRaw("idc='2'")->orderBy('codigo')->get();
                    ?>
                    <input type=hidden name=num1 value='<?= $row1->codigo ?? '' ?>' />
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 50%">
                        <tr>
                            <td>
                                <strong><?= $lang->translation('Mensajes en español') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <textarea name="tema" style="width: 600px; height: 100px"><?= $row1->tema ?? ''; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?= $lang->translation('Mensaje en inglés') ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 23px">
                                <textarea name="tema2" style="width: 600px; height: 100px"><?= $row1->tema2 ?? ''; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 23px"></td>
                        </tr>
                        <tr>
                            <td>
                                <strong>
                                    <input name="gra" id="gra" type="submit" value="<?= $lang->translation('Grabar') ?>" style="width: 100px" class="btn btn-primary form-control" /></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <strong>
                                    <input name="bor" type="submit" value="<?= $lang->translation('Borrar') ?>" onclick="return confirmar('<?= $lang->translation('Está seguro que desea eliminar el mensaje?') ?>')" style="width: 100px;" class="btn btn-danger" /></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <select name="num" style="width: 87px">
                                    <?php foreach ($result as $row): ?>
                                        <option><?= $row->codigo ?></option>
                                    <?php endforeach ?>

                                </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <strong>
                                    <input name="bus" type="submit" value="<?= $lang->translation('Buscar') ?>" style="width: 100px;" class="btn btn-primary" /></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br />
                </form>
            </div>
        </div>
    </div>

    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <script>
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
    </script>
</body>

</html>