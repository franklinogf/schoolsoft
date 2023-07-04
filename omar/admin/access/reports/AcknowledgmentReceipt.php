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
    ['Acuse de recibo', 'Acknowledgment of receipt'],
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
    ['Tarjeta de notas', 'Note card'],
    ['Hoja de deficiencia', 'Deficiency sheet'],
    ['Hoja de progreso', 'Progress sheet'],
    ['Trimestre 1', 'Trimester 1'],
    ['Trimestre 2', 'Trimester 2'],
    ['Trimestre 3', 'Trimester 3'],
    ['Trimestre 4', 'Trimester 4'],

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
    $title = $lang->translation('Acuse de recibo');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Acuse de recibo') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/AcknowledgmentReceipt1.php') ?>" target="AcknowledgmentReceipt" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div id="student">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="student"><?= $lang->translation("Estudiante") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                                <?php foreach ($allStudents as $student) : ?>
                                    <option value="<?= $student->ss ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="hoja" id="option" class="form-control">
                            <option value="0"><?= $lang->translation("Tarjeta de notas") ?></option>
                            <option value="2"><?= $lang->translation("Hoja de deficiencia") ?></option>
                            <option value="3"><?= $lang->translation("Hoja de progreso") ?></option>
                        </select>
                    </div>
                   
                    <div class="text-center">
                        <button class="btn btn-primary mt-4" type="submit"><?= $lang->translation("Continuar") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/AcknowledgmentReceipt2.php') ?>" target="AcknowledgmentReceipt" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div id="grade">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="option"><?= $lang->translation("Grado") ?></label>
                            </div>
                            <select name="grade" class="form-control">
                                <?php foreach ($grades as $grade) : ?>
                                    <option value="<?= $grade ?>"><?= $grade ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="trimestre" id="option" class="form-control">
                            <option ><?= $lang->translation("Trimestre 1") ?></option>
                            <option ><?= $lang->translation("Trimestre 2") ?></option>
                            <option ><?= $lang->translation("Trimestre 3") ?></option>
                            <option ><?= $lang->translation("Trimestre 4") ?></option>
                        </select>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="hoja" id="option" class="form-control">
                            <option value="0"><?= $lang->translation("Tarjeta de notas") ?></option>
                            <option value="2"><?= $lang->translation("Hoja de deficiencia") ?></option>
                            <option value="3"><?= $lang->translation("Hoja de progreso") ?></option>
                        </select>
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