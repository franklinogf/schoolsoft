<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$students = new Student();
$teachers = new Teacher();
$lang = new Lang([
    ['Memos y demeritos', 'Memos and demerits'],
    ['estudiante', 'student'],
    ['Buscar', 'Search'],
    ['Eliminar', 'Delete'],
    ['Fecha', 'Date'],
    ['Agregar nuevo memo', 'Add new memo'],
    ['Buscar', 'Search'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['No matricula', 'No registration'],
    ['Profesor', 'Teacher'],
    ['Falta', 'Absence'],
    ['Falta Leve', 'Minor absence'],
    ['Falta Grave', 'Serious absence'],
    ['Titulo', 'Title'],
    ['Cantidad', 'Amount'],
    ['Hora', 'Time'],
    ['Comentario', 'Comment'],
    ['Imprimir memos de este estudiante', 'Print this student memos'],
]);
$year = $students->info('year');
DB::table("memos")->alter('ADD `mt` INT NOT NULL AUTO_INCREMENT AFTER `falta`, ADD `dpd` VARCHAR(50) NOT NULL AFTER `mt`, ADD PRIMARY KEY (`mt`);');
DB::table("memos_codes")->create("
id INT PRIMARY KEY AUTO_INCREMENT,
codigo varchar(150) not null,
nombre varchar(150) not null
");
DB::table("memos_codes")->alter("
ADD `valor` INT NULL AFTER `nombre`;
");

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Memos y demeritos");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Memos y demeritos") ?></h1>
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                        <?php foreach ($students->All() as $student) : ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->ss ? 'selected=""' : '' ?> value="<?= $student->ss ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
                </form>
            </div>

            <?php if (isset($_REQUEST['student'])) :
                $memos = DB::table('memos')->where([['ss', $_REQUEST['student']], ['year', $year]])->get();
            ?>
                <div class="col-6 mt-2">
                    <select class="form-control w-100" id="memo" name="memo" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' memo' ?></option>
                        <?php foreach ($memos as $memo) : ?>
                            <option <?= isset($_REQUEST['memo']) && $_REQUEST['memo'] == $memo->mt ? 'selected=""' : '' ?> value="<?= $memo->mt ?>"><?= "$memo->titulo" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button id="searchMemo" class="btn btn-primary btn-sm btn-block mt-2"><?= $lang->translation("Buscar") ?></button>
                </div>
                <div class="col-6 mt-2">
                    <button id="addMemo" class="btn btn-outline-primary btn-block"><?= $lang->translation("Agregar nuevo memo") ?></button>
                    <form action="<?= Route::url('/admin/users/memos/includes/pdfMemos.php') ?>" method="POST" target="pdfMemos">
                        <input type="hidden" id="student" name="student" value="<?= $_REQUEST['student'] ?>">
                        <button type="submit" class="btn btn-outline-secondary btn-block"><?= $lang->translation("Imprimir memos de este estudiante") ?> <i class='fas fa-print'></i></button>
                    </form>
                </div>
            <?php endif ?>
        </div>
        <?php if (isset($_REQUEST['student'])) :
            $memosCodes = DB::table('memos_codes')->get();
        ?>
            <div class="modal fade" id="addMemoModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addMemoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="addMemoForm" action="<?= Route::url('/admin/users/memos/includes/index.php') ?>" method="POST">
                        <input type="hidden" id="addMemoOption" name="option" value="save">
                        <input type="hidden" id="addMemoId" name="addMemoId">
                        <input type="hidden" id="addMemoStudentSs" name="addMemoStudentSs" value="<?= $_REQUEST['student'] ?>">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="date"><?= $lang->translation("Fecha") ?></label>
                                            <input id="date" name="date" class="form-control" type="date">
                                        </div>
                                        <div class="form-group">
                                            <label for="teacher"><?= $lang->translation("Profesor") ?></label>
                                            <select class="form-control" name="teacher" id="teacher">
                                                <?php foreach ($teachers->all() as $teacher) : ?>
                                                    <option value="<?= "$teacher->apellidos $teacher->nombre, $teacher->id" ?>"><?= "$teacher->apellidos, $teacher->nombre ($teacher->id)" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="title"><?= $lang->translation("Titulo") ?></label>
                                            <select class="form-control" name="title" id="title">
                                                <?php foreach ($memosCodes as $code) : ?>
                                                    <option value="<?= $code->codigo ?>"><?= $code->nombre ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="demerits"><?= $lang->translation("Cantidad") ?></label>
                                            <input class="form-control" type="number" name="demerits" id="demerits">
                                        </div>
                                        <div class="form-group">
                                            <label for="time"><?= $lang->translation("Hora") ?></label>
                                            <input class="form-control" type="time" name="time" id="time">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="noRegistritation" name="noRegistritation" type="checkbox" value="Si">
                                            <label for="noRegistritation" class="custom-control-label"><?= $lang->translation("No matricula") ?></label>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="absence" id="absence">
                                                <option value="Falta"><?= $lang->translation("Falta") ?></option>
                                                <option value="Falta Leve"><?= $lang->translation("Falta Leve") ?></option>
                                                <option value="Falta Grave"><?= $lang->translation("Falta Grave") ?></option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="comment"><?= $lang->translation("Comentario") ?></label>
                                            <textarea id="comment" name="comment" class="form-control h-100"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <small id="alertMsg" class="text-danger invisible"><?= $lang->translation("Debe de llenar todos los campos") ?></small>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" id="submitBtn" class="btn btn-primary">Guardar</button>
                                <button type="button" id="deleteBtn" class="btn btn-danger hidden">Eliminar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif ?>
    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>

</body>

</html>