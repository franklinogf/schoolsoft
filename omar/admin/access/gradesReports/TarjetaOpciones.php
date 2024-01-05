<<<<<<< HEAD
﻿<?php
=======
<?php
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

<<<<<<< HEAD

=======
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
$lang = new Lang([
    ['Informe de calificaciones', 'Report Card'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
<<<<<<< HEAD
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Con promedio final', 'With final average'],
    ['Con Créditos', 'With Credits'],
    ['Con firma', 'With signature'],
    ['Atrás', 'Go back'],
    ['', ''],
    ['', ''],



]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();
=======
]);
$school = new School(Session::id());
$grades = DB::table('materias')->where('year', $school->info('year'))->orderBy('grado')->get();
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<<<<<<< HEAD
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    function activarTrimestre() {
        var dis = document.TarjetaNotas.tarjeta.value;
        if (dis == '2') {
            document.TarjetaNotas.tri.disabled = false;
        } else {
            document.TarjetaNotas.tri.disabled = true;
        }

    }
</script>
=======
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8

<head>
    <?php
    $title = $lang->translation('Informe de calificaciones');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Informe de calificaciones') ?>
        </h1>
<<<<<<< HEAD
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" action="<?= Route::url('/admin/access/gradesReports/pdf/TarjetaOpciones.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')) : ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
=======
        <div class="container bg-white shadow-lg py-3 rounded">
            <form method="POST" action="<?= Route::url('/admin/access/includes/createGrades.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('createGrades', true) ?>
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Reporte de Notas') ?>
                            </label>
                        </div>
<<<<<<< HEAD
                        <select id="tarjeta" name="tarjeta" class="form-control" onclick="return activarTrimestre(); return true">
=======
                        <select id="tarjeta" name="tarjeta" class="form-control" required>
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
                            <option value='1'>Tarleta 1</option>
                            <option value='2'>Tarleta 2</option>
                            <option value='3'>Tarleta 3</option>
                        </select>
<<<<<<< HEAD
                        <select id="tri" name="tri" class="form-control" disabled="disable">
                            <option value='1'>Trimestre 1</option>
                            <option value='2'>Trimestre 2</option>
                            <option value='3'>Trimestre 3</option>
                            <option value='4'>Trimestre 4</option>
                        </select>

=======
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Idioma') ?>
                            </label>
                        </div>
                        <select id="idioma" name="idioma" class="form-control" required>
                            <option value='1'>Español</option>
                            <option value='2'>English</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
<<<<<<< HEAD
                            <?php foreach ($grades as $grade) : ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
=======
                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade->grado ?>'>
                                    <?= $grade->grado ?>
>>>>>>> e50fbc8cc90fee561551f6c92aff8f843a87cba8
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
<<<<<<< HEAD
                                <?= $lang->translation('Trimestre 1') ?>
                            </label>
                        </div>
                        <input id="tri1" name="tri1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 2') ?>
                            </label>
                        </div>
                        <input id="tri2" name="tri2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 1') ?>
                            </label>
                        </div>
                        <input id="sem1" name="sem1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 3') ?>
                            </label>
                        </div>
                        <input id="tri3" name="tri3" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 4') ?>
                            </label>
                        </div>
                        <input id="tri4" name="tri4" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 2') ?>
                            </label>
                        </div>
                        <input id="sem2" name="sem2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>




                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con promedio final') ?>
                            </label>
                        </div>
                        <input id="prof" name="prof" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con Créditos') ?>
                            </label>
                        </div>
                        <input id="cr" name="cr" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con firma') ?>
                            </label>
                        </div>
                        <input id="fir" name="fir" type="checkbox" style="height: 30px; width: 30px" value="Si" />


                    </div>


                                <?= $lang->translation('Opci&#65533;n') ?>
                            </label>
                        </div>
                        <select id="option" name="option" class="form-control" required>
                            <option value="1">Todo</option>
                            <option value="2">Cursos</option>
                            <option value="3">Asistencias</option>
                        </select>
                    </div>
                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>