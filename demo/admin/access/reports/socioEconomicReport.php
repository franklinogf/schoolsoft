<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher();
$lang = new Lang([
    ['Informe socioeconómico', 'Socioeconomic report'],
    ['Repartidas', 'Distributed'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Lista', 'List'],
    ['Resumen bajo nivel', 'Under level summary'],
    ['Resumen sobre nivel', 'Over level summary'],
    ['Lista de estudiantes', 'Students list'],
    ['Totales', 'Totals'],
]);
$students = new Student();
$allStudents = $students->all();
$school = new School();
$grades = $school->allGrades();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Informe socioeconómico');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Informe socioeconómico') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/') ?>" target="socioEconomicReport" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <!-- <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation("Repartidas") ?>:</label>
                        </div>
                        <input class="form-control" type="text" name="distributed">
                    </div>    -->
                    <div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text'><?= $lang->translation("Opción") ?></label>
                            </div>
                            <select class="form-control w-100" id="option" name="option" required>
                                <option value="1"><?= $lang->translation('Lista') ?></option>
                                <option value="2"><?= $lang->translation('Resumen bajo nivel') ?></option>
                                <option value="5"><?= $lang->translation('Resumen sobre nivel') ?></option>
                                <option value="3"><?= $lang->translation('Totales') ?></option>
                                <option value="6"><?= $lang->translation('Tabla') ?></option>
                                <!-- <option value="4"><?= $lang->translation('Lista de estudiantes') ?></option> -->
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary mt-4" type="submit"><?= $lang->translation("Continuar") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>