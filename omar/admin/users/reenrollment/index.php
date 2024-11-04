<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$students = new Student();
$year1 = $students->info('year');
$year2 = (($year1[0] . $year1[1]) + 1) . '-' . (($year1[3] . $year1[4]) + 1);
$fullYear1 = "20$year1[0]$year1[1]-20$year1[3]$year1[4]";
$fullYear2 = "20$year2[0]$year2[1]-20$year2[3]$year2[4]";

$grades = DB::table("year")->select('DISTINCT grado')->where('year', $year1)->orderBy('grado')->get();
$oldGrades = array_values(array_filter($grades, function ($grade) {
    [$g1] = explode('-', $grade->grado);

    return $g1 !== '12';
}));
$oldAllStudents = array_values(array_filter($students->all(), function ($student) use ($year2) {

    [$g1] = explode('-', $student->grado);
    $data = DB::table('year')->where([
        ['year', $year2],
        ['ss', $student->ss]
    ])->first();
    return $g1 !== '12' && !$data;
}));


$lang = new Lang([
    ["Re matricula", "Re enrollment"],
    ['Año escolar', 'School year'],
    ['Seleccione este para trabajar sin grados', 'Select this to work without grades'],
    ['Grado del que quiera enviar', 'Grade you want to send'],
    ['Se buscara en todos los años', 'It will look for in all years'],
    ['Todos los estudiantes', 'All the students'],
    ['Seleccionar todos', 'Select all'],
    ['Buscar', 'Search'],
    ['Pasar', 'Pass'],
    ['Siguiente año escolar', 'Next school year'],
    ['Seleccione el grado donde quiere recibir', 'Select the grade to receive'],
    ['Grado al que va a recibir', 'Grade you will receive'],
    ['Borrar seleccionados', 'Delete selected'],
    ['Borrar todos', 'Delete all'],
    ['Informes de estudiantes sin matricular', 'Student reports without enrollment'],
    ['En lista', 'List'],
    ['En resumen', 'Summary'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Re matricula");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-fluid px-md-5">
        <input type="hidden" name="year1" id="year1" value="<?= $year1 ?>">
        <input type="hidden" name="year2" id="year2" value="<?= $year2 ?>">
        <h1 class="text-center mb-5"><?= $lang->translation("Re matricula") ?></h1>
        <div class="row">
            <div class="col-12 col-lg-5">
                <p class="font-weight-bold"><?= $lang->translation("Año escolar") ?> <?= $fullYear1 ?></p>
                <select name="oldGrade" id="oldGrade" class="form-control">
                    <option value=""><?= $lang->translation("Seleccione este para trabajar sin grados") ?></option>
                    <?php foreach ($oldGrades as $grade): ?>
                        <option value="<?= $grade->grado ?>"><?= $grade->grado ?></option>
                    <?php endforeach ?>
                </select>
                <small class="text-muted"><?= $lang->translation("Grado del que quiera enviar") ?></small>
                <!-- Search by last name -->
                <div class="input-group input-group-sm mt-2">
                    <input id="studentSurnames" name="studentSurnames" type="text" class="form-control" placeholder="Student surnames" aria-label="Student surnames" aria-describedby="studentSurnamesBtn">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="studentSurnamesBtn" disabled><?= $lang->translation("Buscar") ?></button>
                    </div>
                </div>
                <small class="text-muted"><?= $lang->translation("Se buscara en todos los años") ?></small>
                <p id="oldStudentsTitle" class="mb-0"><?= $lang->translation("Todos los estudiantes") ?> <span id="oldStudentsAmount" class="badge badge-primary"><?= sizeof($oldAllStudents) ?></span></p>
                <div class="d-flex justify-content-end">
                    <button id="selectAll" class="btn btn-sm btn-secondary"><?= $lang->translation("Seleccionar todos") ?></button>
                </div>
                <select id="old" class="custom-select" multiple size="15">
                    <?php foreach ($oldAllStudents as $student): ?>
                        <option value="<?= $student->mt ?>"><?= "$student->apellidos, $student->nombre ($student->id) $student->grado" ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="col-12 col-lg-2 my-3 my-lg-0 d-flex justify-content-center flex-column">
                <button id="pass" class="btn btn-outline-primary align-self-center" data-type="regular"><?= $lang->translation("Pasar") ?> <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i></button>
                <select name="passGrade" id="passGrade" class="form-control align-self-center mt-2 invisible">
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade->grado ?>"><?= $grade->grado ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="col-12 col-lg-5">
                <p class="font-weight-bold"><?= $lang->translation("Siguiente año escolar") ?> <?= $fullYear2 ?></p>
                <select name="newGrade" id="newGrade" class="form-control" disabled>
                    <option value=""><?= $lang->translation("Seleccione el grado donde quiere recibir") ?></option>
                    <?php foreach ($grades as $grade): ?>
                        <option value="<?= $grade->grado ?>"><?= $grade->grado ?></option>
                    <?php endforeach ?>
                </select>
                <small class="text-muted mb-3"><?= $lang->translation("Grado al que va a recibir") ?></small>

                <p id="newStudentsTitle mt-3"><?= $lang->translation("Todos los estudiantes") ?> <span id="newStudentsAmount" class="badge badge-primary"><?= sizeof($students->all($year2)) ?></span></p>
                <input type="hidden" id="hiddenNewStudentsTitle" value="Todos los estudiantes <span class='badge badge-primary'><?= sizeof($students->all($year2)) ?></span>">
                <select id="new" class="custom-select" multiple size="15">
                    <?php foreach ($students->all($year2) as $student): ?>
                        <option value="<?= $student->mt ?>"><?= "$student->apellidos, $student->nombre ($student->id) $student->grado" ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="text-right mt-2">
                    <button id="delete" class="btn btn-warning"><?= $lang->translation("Borrar seleccionados") ?></button>
                    <button id="deleteAll" class="btn btn-danger"><?= $lang->translation("Borrar todos") ?></button>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <h3 class="text-center"><?= $lang->translation("Informes de estudiantes sin matricular") ?></h3>
            <div class="list-group list-group-horizontal shadow">
                <a href="<?= Route::url('/admin/users/reenrollment/pdf/pdfList.php') ?>" class="list-group-item list-group-item-action list-group-item-secondary" target="list">
                    <?= $lang->translation("En lista") ?>
                </a>
                <a href="<?= Route::url('/admin/users/reenrollment/pdf/pdfResume.php') ?>" class="list-group-item list-group-item-action list-group-item-secondary" target="resume">
                    <?= $lang->translation("En resumen") ?>
                </a>
            </div>
        </div>
    </div>

    <div id="modalAlert" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-danger">
                <div class="modal-body d-flex justify-content-between">
                    <p class="mb-0"></p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);

    ?>

</body>

</html>