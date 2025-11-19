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
    ["Lista de no deudores", "List of non-debtors"],
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
    ['Grados', 'Grades'],
    ['Procesar', 'Process'],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Selección de código', 'Code selection'],
    ['Semestre-1', 'Semester-1'],
    ['Semestre-2', 'Semester-2'],
    ['Todo el año', 'All year round'],
    ['Opción', 'Option'],
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
    $title = $lang->translation('Lista de no deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de no deudores') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <div class="div">

            </div>
            <div class="div">


                <form action="pdf/inf_pagados.php" method="post" target="_blank">
                    <div class="style4">
                        <table align="center" cellpadding="2" cellspacing="0" style="width: 35%">
                            <tr>
                                <td class="style3">
                                    <strong><?= $lang->translation("Selección de código") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="style6">
                                    <select name="desc" required style="width: 250px">
                                        <option value=""><?= $lang->translation("Selección") ?></option>
                                        <option value="Todos"><?= $lang->translation("Todos") ?></option>
                                        <?php foreach ($resultado2 as $row2): ?>
                                            <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="style3"><strong><?= $lang->translation("Opción") ?></strong></td>
                            </tr>
                            <tr>
                                <td class="style6"><select name="saldo">
                                        <option value="1"><?= $lang->translation("Semestre-1") ?></option>
                                        <option value="2"><?= $lang->translation("Semestre-2") ?></option>
                                        <option value="3"><?= $lang->translation("Todo el año") ?></option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td class="style6"><select name="lg">
                                        <option value="1"><?= $lang->translation("Lista") ?></option>
                                        <option value="2"><?= $lang->translation("Grados") ?></option>
                                    </select></td>
                            </tr>
                        </table>
                        <br />
                        <br />
                        <strong>
                            <input name="Submit1" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                        </strong><br />
                        <br />
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>