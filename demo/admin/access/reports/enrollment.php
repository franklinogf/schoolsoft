<?php
require_once '../../../app.php';

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
    ['Matrícula', 'Enrollment'],
    ['Hoja de matrícula', 'Enrollment'],
    ['Hoja', 'Sheet'],
    ['Con fecha de hoy', 'With today date'],
    ['estudiante', 'student'],
    ['Grado', 'Grade'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Por estudiante', 'By student'],
    ['Por grado', 'By grade'],
    ['Estudiantes', 'Students'],
    ['Todos los grados', 'All grades'],
    ['Este año', 'This year'],
    ['Próximo año', 'Next year'],
    ['Con cuotas', 'With fees'],
]);
$students = new Student();
$allStudents = $students->all();
$school = new School();
$grades = $school->allGrades(false);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Hoja de matrícula');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Hoja de matrícula') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/') ?>" target="enrollment" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="sheet"><?= $lang->translation("Matrícula") ?>:</label>
                        </div>
                        <select class="form-control" name="sheet" id="sheet">
                            <option value="1"><?= $lang->translation("Hoja") ?> - 1</option>
                            <option value="2" disabled><?= $lang->translation("Hoja") ?> - 2</option>
                            <option value="3"><?= $lang->translation("Hoja") ?> - 3</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mb-1">
                        <div class="btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" name="todayDate" value="si"> <?= $lang->translation("Con fecha de hoy") ?>
                            </label>
                        </div>

                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info active">
                                <input type="radio" name="enrollmentYear" value="thisYear" checked> <?= $lang->translation("Este año") ?>
                            </label>
                            <label class="btn btn-outline-info">
                                <input type="radio" name="enrollmentYear" value="nextYear"> <?= $lang->translation("Próximo año") ?>
                            </label>
                        </div>
                    </div>
                    <div class="btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-info">
                            <input type="checkbox" name="fees" value="si" checked> <?= $lang->translation("Con cuotas") ?>
                        </label>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="option" id="option" class="form-control">
                            <option value="student"><?= $lang->translation("Por estudiante") ?></option>
                            <option value="grade"><?= $lang->translation("Por grado") ?></option>
                        </select>
                    </div>
                    <div id="student">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="student"><?= $lang->translation("Estudiantes") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                                <?php foreach ($allStudents as $student) : ?>
                                    <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div id="grade" class="hidden">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="option"><?= $lang->translation("Grado") ?></label>
                            </div>
                            <select name="grade" class="form-control">
                                <option value=""><?= $lang->translation("Todos los grados") ?></option>
                                <?php foreach ($grades as $grade) : ?>
                                    <option value="<?= $grade ?>"><?= $grade ?></option>
                                <?php endforeach ?>
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
    Route::selectPicker('js');
    ?>
</body>

</html>