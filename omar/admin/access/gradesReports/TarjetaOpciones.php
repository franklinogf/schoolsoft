<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Informe de calificaciones', 'Report Card'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
]);
$school = new School(Session::id());
$grades = DB::table('materias')->where('year', $school->info('year'))->orderBy('grado')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

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
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Informe de calificaciones') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form method="POST" action="<?= Route::url('/admin/access/includes/createGrades.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')) : ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i> <?= Session::get('createGrades', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade"><?= $lang->translation('Reporte de Notas') ?></label>
                        </div>
                        <select id="tarjeta" name="tarjeta" class="form-control" required>
                               <option value='1'>Tarleta 1</option>
                               <option value='2'>Tarleta 2</option>
                               <option value='3'>Tarleta 3</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade"><?= $lang->translation('Idioma') ?></label>
                        </div>
                        <select id="idioma" name="idioma" class="form-control" required>
                               <option value='1'>Español</option>
                               <option value='2'>English</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade"><?= $lang->translation('Grado') ?></label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
                            <?php foreach ($grades as $grade) : ?>
                                <option value='<?= $grade->grado ?>'><?= $grade->grado ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option"><?= $lang->translation('Opci&#65533;n') ?></label>
                        </div>
                        <select id="option" name="option" class="form-control" required>
                            <option value="1">Todo</option>
                            <option value="2">Cursos</option>
                            <option value="3">Asistencias</option>
                        </select>
                    </div>
                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto"><?= $lang->translation('Continuar') ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
?>

</body>

</html>