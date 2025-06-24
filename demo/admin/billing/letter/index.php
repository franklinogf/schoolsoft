<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Carta de cobro", "Collection letter"],
    ['Carta', 'Letter'],
    ['Papel orientación', 'Paper orientation'],
    ['Mes', 'Month'],
    ['Todos', 'All'],
    ['Con Cantidad', 'With Quantity'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Informe pdf', 'PDF report'],
    ['Enviar por E-mail', 'Send by E-mail'],
    ['Deudores', 'Debtors'],
    ['Descripción', 'Description'],
    ['Hoja Legal', 'Legal Sheet'],
    ['Hoja carta', 'Letter Sheet'],
    ['Por Cuenta', 'By Account'],
    ['Por Grado', 'By Grade'],
    ['Selección de código', 'Code selection'],
    ['Opciones', 'Options'],
    ['En Orden', 'In order'],
    ['Fecha', 'Date'],
    ['Cuentas', 'Accounts'],
    ['Estudiantes', 'Students'],
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
    ['Primer aviso de cobro', 'First collection notice'],
    ['Segundo aviso de cobro', 'Second collection notice'],
    ['Carta de suspensión', 'Suspension Letter'],
    ['Carta general', 'General Letter'],
    ['Carta de cobro general A', 'General Collection Letter A'],
    ['', ''],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Selección de Meses', 'Month Selection'],
    ['Borrar todos los Cargos', 'Eliminate all costs'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);
$school = new School(Session::id());
$year = $school->info('year2');

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    $title = $lang->translation('Carta de cobro');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Cartas</title>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Carta de cobro') ?></h1>
    <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
        <div class="div">
            <form method="post" action="carta.php" target="_blank">
                <table align="center" cellspacing="2" cellpadding="0" border="0">
                    <thead>
                        <tr>
                            <th colspan="2"><?= $lang->translation('Opciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="tipo">
                                    <option value="pdf"><?= $lang->translation('Informe pdf') ?></option>
                                    <option value="email"><?= $lang->translation('Enviar por E-mail') ?></option>
                                </select>
                            </td>
                            <td>
                                <select name="opcion">
                                    <option value="todos"><?= $lang->translation('Todos') ?></option>
                                    <option value="deudores"><?= $lang->translation('Deudores') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?= $lang->translation('Mes') ?></th>
                            <th><?= $lang->translation('Carta') ?></th>
                        </tr>
                        <tr>
                            <td>
                                <select name="mes">
                                    <option value="1"><?= $lang->translation('Enero') ?></option>
                                    <option value="2"><?= $lang->translation('Febrero') ?></option>
                                    <option value="3"><?= $lang->translation('Marzo') ?></option>
                                    <option value="4"><?= $lang->translation('Abril') ?></option>
                                    <option value="5"><?= $lang->translation('Mayo') ?></option>
                                    <option value="6"><?= $lang->translation('Junio') ?></option>
                                    <option value="7"><?= $lang->translation('Julio') ?></option>
                                    <option value="8"><?= $lang->translation('Agosto') ?></option>
                                    <option value="9"><?= $lang->translation('Septiembre') ?></option>
                                    <option value="10"><?= $lang->translation('Octubre') ?></option>
                                    <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                    <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                </select>
                            </td>
                            <td>
                                <select id="carta" name="carta">
                                    <option value="1"><?= $lang->translation('Primer aviso de cobro') ?></option>
                                    <option value="2"><?= $lang->translation('Segundo aviso de cobro') ?></option>
                                    <option value="3"><?= $lang->translation('Carta de suspensión') ?></option>
                                    <option value="4"><?= $lang->translation('Carta general') ?></option>
                                    <option value="5"><?= $lang->translation('Carta de cobro general A') ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <strong>
                                    <center><br><br>
                                        <input class="btn btn-primary form-control" id='Aceptar' name="buscar" type="submit" value="<?= $lang->translation('Procesar') ?>" style="width: 129px;" />
                                    </center>
                                </strong><br />

                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>

        </div>
    </div>
</body>

</html>