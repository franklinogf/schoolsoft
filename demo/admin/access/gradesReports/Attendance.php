<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\DataBase\DB;

Session::is_logged();


$lang = new Lang([
    ['Asistencia', 'Attendance'],
    ['Hasta', 'Up to'],
    ['Desde', 'From'],
    ['Grado', 'Grade'],
    ['Estudiante', 'Student'],
    ['Prueba cortas', 'Quiz'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Selección', 'Selection'],
    ['Todos', 'Alls'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas', 'Grades'],
    ['Final', 'Final'],
    ['Trabajos diarios', 'Homework'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Lista', 'List'],
    ['Selección:', 'Selection:'],
    ['Resumen', 'Summary'],
    ['Con Lineas:', 'With Lines:'],
    ['Curso', 'Course'],
    

    
]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$year = $school->info('year2');

$teachers = DB::table('year')->where([
    ['codigobaja', 0],
    ['year', $year]
])->orderBy('apellidos')->get();



$grades = $school->allGrades();

$re = $school->info('tar');
$in1='';$in2='';$in3='';$in4='';$in5='';$in6=''; $in7='';$in8='';
$in9='';$in10='';$in11='';$in12='';$in13='';$in14='';$in15='';$in16='';$in17='';$in18='';$in19='';$in20='';
if ($re=='1'){$in1='selected';}
if ($re=='2'){$in2='selected';}
if ($re=='3'){$in3='selected';}

//$mensaj = DB::table('codigos')->orderBy('codigo')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
function activarTrimestre() {
var dis = document.RegistroNotas.seasis.value;
if (dis == '1')
   {
   document.RegistroNotas.grado.disabled=false;
   document.RegistroNotas.lista.disabled=false;
   }
 else
   {
   document.RegistroNotas.grado.disabled=true;
   document.RegistroNotas.lista.disabled=true;
   }
if (dis == '2')
   {
   document.RegistroNotas.teacher.disabled=false;
   }
 else
   {
   document.RegistroNotas.teacher.disabled=true;
   }

}
</script> 

<head>
    <?php
    $title = $lang->translation('Asistencia');
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
            <?= $lang->translation('Asistencia') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="RegistroNotas" name="RegistroNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/Attendance.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('RegistroNotas1')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Opción') ?>
                            </label>
                        </div>
                        <select id="seasis" name="seasis" class="form-control" required onclick="return activarTrimestre(); return true">
                               <option value=''><?= $lang->translation('Selección') ?></option>
                               <option value='1'><?= $lang->translation('Grado') ?></option>
                               <option value='2'><?= $lang->translation('Estudiante') ?></option>
                        </select>
                    </div>


                    <div id="student">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="teacher"><?= $lang->translation("Desde") ?></label>
                            </div>
                            <input type="date" class="form-control" name="quater1_start" id="quater1_start" value="<?= date('Y-m-d') ?>">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="teacher"><?= $lang->translation("Hasta") ?></label>
                            </div>
                            <input type="date" class="form-control" name="quater1_start" id="quater1_start" value="<?= date('Y-m-d') ?>">
                        </div>
                    
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="teacher"><?= $lang->translation("Estudiante") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="teacher" data-live-search="true" disabled required>
                                <option value=""><?= $lang->translation("Selección") ?></option>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher->ss ?>"><?= "$teacher->apellidos $teacher->nombre ($teacher->id)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="teacher"><?= $lang->translation("Grado") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="grado" data-live-search="true" disabled required>
                                <option value=""><?= $lang->translation("Selección") ?></option>
                                <option value="todos"><?= $lang->translation("Todos") ?></option>
                                <?php foreach ($grades as $grade) : ?>
                                    <option value="<?= $grade ?>"><?= $grade ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>




                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección:') ?>
                            </label>
                        </div>
                        <select id="lista" name="lista" class="form-control" disabled required>
                               <option value='1'><?= $lang->translation('Lista') ?></option>
                               <option value='2'><?= $lang->translation('Resumen') ?></option>
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
<script language="JavaScript">
var dis = document.TarjetaNotas.seasis.value;
if (dis == '2')
   {
//   document.TarjetaNotas.grado.disabled=false;
   }
 else
   {
//   document.TarjetaNotas.grado.disabled=true;
   }

</script> 

</html>