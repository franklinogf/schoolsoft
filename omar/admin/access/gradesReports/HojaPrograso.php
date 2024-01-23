<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Hoja de progreso', 'Progress Sheet'],
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
    ['Trabajos diarios', 'Homework'],
    ['Prueba cortas', 'Quiz'],
    
    
    
]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();

$re = $school->info('tar');
$in1='';$in2='';$in3='';$in4='';$in5='';$in6=''; $in7='';$in8='';
$in9='';$in10='';$in11='';$in12='';$in13='';$in14='';$in15='';$in16='';$in17='';$in18='';$in19='';$in20='';
if ($re=='1'){$in1='selected';}
if ($re=='2'){$in2='selected';}
if ($re=='3'){$in3='selected';}

$mensaj = DB::table('codigos')->orderBy('codigo')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<head>
    <?php
    $title = $lang->translation('Hoja de progreso');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Hoja de progreso') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/HojaOpciones.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Reporte de Notas') ?>
                            </label>
                        </div>
                        <select id="tarjeta" name="tarjeta" class="form-control" onclick="return activarTrimestre(); return true">
                            <option value='1' <?= $in1 ?> >Tarjeta 1</option>
                            <option value='2' <?= $in2 ?> >Tarjeta 2</option>
                            <option value='3' <?= $in3 ?> >Tarjeta 3</option>
                        </select>
                        <select id="tri" name="tri" class="form-control">
                            <option value='1'><?= $lang->translation('Trimestre 1') ?></option>
                            <option value='2'><?= $lang->translation('Trimestre 2') ?></option>
                            <option value='3'><?= $lang->translation('Trimestre 3') ?></option>
                            <option value='4'><?= $lang->translation('Trimestre 4') ?></option>
                        </select>
                        
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
                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Sheet') ?>
                            </label>
                        </div>
                        <select id="hoja" name="hoja" class="form-control" onclick="return activarTrimestre(); return true">
                            <option value='1'>
                                 <?= $lang->translation('Notas') ?>
                            </option>
                            <option value='2'>
                                 <?= $lang->translation('Trabajos diarios') ?>
                            </option>
                            <option value='3'>
                                 <?= $lang->translation('Prueba cortas') ?>
                            </option>
                            <option value='4'>
                                 <?= $lang->translation('Trabajo libreta') ?>
                            </option>
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