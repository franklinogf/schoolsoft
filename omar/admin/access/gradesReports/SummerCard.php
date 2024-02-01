<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Boleta de verano', 'Summer card'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
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
    ['Mensaje', 'Message'],
    ['Comentario', 'Comment'],
    ['Selección', 'Selection'],
    ['Notas', 'Grades'],
    ['Trabajo libreta', 'Daily Homework'],
    ['Ninguno', 'None'],
    ['Todos', 'All'],
    
    
    
]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();

$year = $school->info('year2');

//$mensaj = DB::table('codigos')->orderBy('codigo')->get();

//$estudiantes = DB::table('padres')->where('year', $year)->orderBy('apellidos')->get();

$estudiantes = DB::table('padres')->select("distinct nombre, apellidos, ss")->where([
          ['year', $year],
          ['verano', '!=', ''],
        ])->orderBy('apellidos')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<head>
    <?php
    $title = $lang->translation('Boleta de verano');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Boleta de verano') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/SummerCard.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('SummerCard')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('SummerCard', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Boleta de verano') ?>
                            </label>
                        </div>
                        <select id="tarjeta" name="tarjeta" class="form-control" onclick="return activarTrimestre(); return true">
                            <option value='1' <?= $in1 ?> >Tarjeta 1</option>
                            <option value='2' <?= $in2 ?> >Tarjeta 2</option>
                            <option value='3' <?= $in3 ?> >Tarjeta 3</option>
                        </select>
                        
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        </div>

                        <select id="estu" name="estu" class="form-control">
                            <option value='N'>
                                <?= $lang->translation('Ninguno') ?>
                            </option>
                            <option value='T'>
                                <?= $lang->translation('Todos') ?>
                            </option>
                            <?php foreach ($estudiantes as $estudiante): ?>
                                <option value='<?= $estudiante->ss ?>'>
                                    <?= $estudiante->apellidos.' '.$estudiante->nombre?>
                                </option>
                            <?php endforeach ?>
                        </select>




                    </div>
                    <div class="input-group mb-3">
                    </div>


                    <div class="input-group mb-3">
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