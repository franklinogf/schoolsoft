<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Asistencia perfecta', 'Perfect assistance'],
    ['Reporte de Notas', 'Grade Report'],
    ['Código del Curso: ', 'Course Code: '],
    ['Valor menor < que: ', 'Value less than: '],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Selección', 'Selection'],
    ['Semestre 2', 'Semester 2'],
    ['Por fechas', 'By dates'],
    ['Por cantidad', 'By quantity'],
    ['Peso', 'Values'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Promedio', 'Average'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['1 por pagina', '1 per page'],
    ['2 por pagina', '2 per page'],
    ['3 por pagina', '3 per page'],
    ['Maestro', 'Maestro'],
    ['Promedio final', 'Final average'],
    ['todos', 'alls'],
    ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],



]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();

$re = $school->info('tar');
$in1 = '';
$in2 = '';
$in3 = '';
$in4 = '';
$in5 = '';
$in6 = '';
$in7 = '';
$in8 = '';
$in9 = '';
$in10 = '';
$in11 = '';
$in12 = '';
$in13 = '';
$in14 = '';
$in15 = '';
$in16 = '';
$in17 = '';
$in18 = '';
$in19 = '';
$in20 = '';
if ($re == '1') {
    $in1 = 'selected';
}
if ($re == '2') {
    $in2 = 'selected';
}
if ($re == '3') {
    $in3 = 'selected';
}

$mensaj = DB::table('codigos')->orderBy('codigo')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Asistencia perfecta');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style type="text/css">
        .style1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Asistencia perfecta') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="Deficiencia" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/AsistenciaPerfecta.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('Deficiencia')) : ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('ListadePromedios', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
                            <option value=''>
                                <?= $lang->translation('Selección') ?>
                            </option>
                            <option value='todos'>
                                <?= $lang->translation('todos') ?>
                            </option>

                            <?php foreach ($grades as $grade) : ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Promedio') ?>
                            </label>
                        </div>
                        <select id="asis" name="asis" class="form-control">
                            <option value="1">
                                <?= $lang->translation('Por fechas') ?>
                            </option>
                            <option value="2">
                                <?= $lang->translation('Por cantidad') ?>
                            </option>
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