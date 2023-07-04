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
    ['Asistencia diaria', 'Daily Attendance'],
    ['Desde', 'From'],
    ['Hasta', 'To'],
    ['Grado', 'Grade'],
    ['Grados separados', 'Separted grades'],
    ['estudiante', 'student'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Por estudiante', 'By student'],
    ['Por grado', 'By grade'],
    ['Estudiante', 'Student'],
    ['Todos los grados', 'All grades'],
    ['Lista', 'List'],
    ['Resumen', 'Summary'],
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
    $title = $lang->translation('Asistencia diaria');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Asistencia diaria') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/dailyAttendance.php') ?>" target="dailyAttendance" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="from"><?= $lang->translation("Desde") ?>:</label>
                        </div>
                        <input id="from" class="form-control" type="date" name="from" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="to"><?= $lang->translation("Hasta") ?>:</label>
                        </div>
                        <input id="to" class="form-control" type="date" name="to" value="<?= Util::date() ?>" required>
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
                                <label class='input-group-text' for="student"><?= $lang->translation("Estudiante") ?></label>
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
                        <div class="d-flex justify-content-between">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-info">
                                    <input type="checkbox" checked name="separatedGrade" value="si"> <?= $lang->translation("Grados separados") ?>
                                </label>
                            </div>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-info active">
                                    <input type="radio" name="type" value="list" checked> <?= $lang->translation("Lista") ?>
                                </label>
                                <label class="btn btn-outline-info">
                                    <input type="radio" name="type" value="summary"> <?= $lang->translation("Resumen") ?>
                                </label>
                            </div>                            
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