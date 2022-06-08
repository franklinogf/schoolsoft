<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\Lang;

Session::is_logged();
$teacher = new Teacher(Session::id());
$classes = $teacher->classes();


$lang= new Lang([
    ['Cursos', 'Grades'],
    ['Entrada de asistencias', 'Assists entry'],
    ['Informe de asistencias diarias', 'Daily attendance report'],
    ['Informe de asistencias', 'Attendance report'],
    ['Preescolar', 'Preschool'],
    ["Grado","Grade"],
    ["Pagina","Page"],
    ["Verano","Summer"],
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Cursos');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Cursos') ?></h1>
        <div class="jumbotron bg-secondary shadow-sm py-3">
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col mb-3">
                    <a href="attendance.php" class="btn btn-outline-light btn-block btn-lg"><?= $lang->translation('Entrada de asistencias') ?></a>
                </div>
                <div class="col mb-3">
                    <a href="dailyAttendance.php" class="btn btn-outline-light btn-block btn-lg <?= $teacher->grado === '' ? 'disabled' : '' ?>"><?= $lang->translation('Informe de asistencias diarias') ?></a>
                </div>
                <div class="col">
                    <a href="attendance_report.php" class="btn btn-outline-light btn-block btn-lg"><?= $lang->translation('Informe de asistencias') ?></a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= $lang->translation('Preescolar') ?></a>
                </div>
            </div>
        </div>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/regiweb/grades/enterGrades.php') ?>" method="post">
                <div class="mx-auto" style="width: 20rem;">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation('Grado') ?></label>
                        </div>
                        <select name="class" class="custom-select" id="class" required>
                            <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                            <?php foreach ($classes as $class) : ?>
                                <option data-verano=<?= $class->verano === '2' ? 'true' : 'false' ?> value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tri"><?= $lang->translation('Trimestre') ?></label>
                        </div>
                        <select class="custom-select" id="tri" required>
                            <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                            <!-- if decimals are active -->
                            <?php if ($teacher->info('cppd') === 'Si') : ?>
                                <option value="Trimestre-1"><?= $lang->translation("Trimestre") ?> 1</option>
                                <option value="Trimestre-3"><?= $lang->translation("Trimestre") ?> 3</option>
                                <option value="Verano" disabled><?= $lang->translation("Verano") ?></option>
                            <?php else : ?>
                                <option value="Trimestre-1"><?= $lang->translation("Trimestre") ?> 1</option>
                                <option value="Trimestre-2"><?= $lang->translation("Trimestre") ?> 2</option>
                                <option value="Trimestre-3"><?= $lang->translation("Trimestre") ?> 3</option>
                                <option value="Trimestre-4"><?= $lang->translation("Trimestre") ?> 4</option>
                                <option value="Verano" disabled><?= $lang->translation("Verano") ?></option>
                            <?php endif ?>
                        </select>
                        <input type="hidden" name="tri" id="hiddenTri">

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tra"><?= $lang->translation("Pagina") ?></label>
                        </div>
                        <select class="custom-select" id="tra" required>
                            <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                            <!-- if decimals are active -->
                            <?php if ($teacher->info('cppd') === 'Si') : ?>
                                <option value="Notas"><?= $lang->translation("Notas") ?></option>
                                <option value="V-Nota" disabled><?= $lang->translation("Notas de verano") ?></option>
                            <?php else : ?>
                                <option value="Notas"><?= $lang->translation("Notas") ?></option>
                                <option value="Pruebas-Cortas"><?= $lang->translation("Pruebas cortas") ?></option>
                                <option value="Trab-Diarios"><?= $lang->translation("Trabajos diarios") ?></option>
                                <?php if ($teacher->info('etd') === 'SI') : ?>
                                    <option value="Trab-Diarios2"><?= $lang->translation("Trabajos diarios") ?> 2</option>
                                <?php endif ?>
                                <option value="Trab-Libreta"><?= $lang->translation("Trabajos de libreta") ?></option>
                                <?php if ($teacher->info('etd') === 'SI') : ?>
                                    <option value="Trab-Libreta2"><?= $lang->translation("Trabajos de libreta") ?> 2</option>
                                <?php endif ?>
                                <option value="Cond-Asis"><?= $lang->translation("Conducta y asistencia") ?></option>
                                <option value="Ex-Final"><?= $lang->translation("Examen final") ?></option>
                                <option value="V-Nota" disabled><?= $lang->translation("Notas de verano") ?></option>
                            <?php endif ?>
                        </select>
                        <input type="hidden" name="tra" id="hiddenTra">
                    </div>
                    <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= $lang->translation("Continuar") ?>">
                </div>
            </form>

        </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>