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
    ['Registro de notas', 'Record of notes'],
    ['profesor', 'teacher'],
    ['Por profesor', 'By teacher'],
    ['Reporte de Notas', 'Grade Report'],
    ['Paginas:', 'Pages:'],
    ['Prueba cortas', 'Quiz'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas', 'Grades'],
    ['Trabajo libreta', 'Daily Homework'],
    ['Trabajos diarios', 'Homework'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Verano', 'Summer'],
    ['Selección:', 'Selection:'],
    ['Final', 'Final'],
    ['Con Lineas:', 'With Lines:'],
    ['Curso', 'Course'],
    

    
]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();



//$grades = $school->allGrades();

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
<script language="JavaScript">
function activarTrimestre() {
var dis = document.TarjetaNotas.tarjeta.value;
if (dis == '2')
   {
   document.TarjetaNotas.tri.disabled=false;
   }
 else
   {
   document.TarjetaNotas.tri.disabled=true;
   }

}
</script> 

<head>
    <?php
    $title = $lang->translation('Registro de notas');
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
            <?= $lang->translation('Registro de notas') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="RegistroNotas" name="RegistroNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/RegistroNotas.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('RegistroNotas1')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>

                    <div id="student">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="teacher"><?= $lang->translation("Profesor") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="teacher" data-live-search="true" required>
                                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('profesor') ?></option>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher->id ?>"><?= "$teacher->apellidos $teacher->nombre ($teacher->id)" ?></option>
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
                        <select id="nota" name="nota" class="form-control" required>
                               <option value='1'><?= $lang->translation('Trimestre 1') ?></option>
                               <option value='2'><?= $lang->translation('Trimestre 2') ?></option>
                               <option value='3'><?= $lang->translation('Trimestre 3') ?></option>
                               <option value='4'><?= $lang->translation('Trimestre 4') ?></option>
                               <option value='sem1'><?= $lang->translation('Semestre 1') ?></option>
                               <option value='sem2'><?= $lang->translation('Semestre 2') ?></option>
                               <option value='final'><?= $lang->translation('Promedio final') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                    </div>
                    <div class="style1">
                        <div ><b>
                              <?= $lang->translation('Paginas:') ?>
                        </b>
							<div></div>
						</div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Notas') ?>
                            </label>
                        </div>
                        <input id="tri1b" name="tri1b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Prueba cortas') ?>
                            </label>
                        </div>
                        <input id="tri2" name="tri2b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trabajo libreta') ?>
                            </label>
                        </div>
                        <input id="sem1" name="tri3b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trabajos diarios') ?>
                            </label>
                        </div>
                        <input id="tri3" name="tri4b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Final') ?>
                            </label>
                        </div>
                        <input id="tri4" name="tri5b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Verano') ?>
                            </label>
                        </div>
                        <input id="sem2" name="tri6b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
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
var dis = document.TarjetaNotas.tarjeta.value;
if (dis == '2')
   {
   document.TarjetaNotas.tri.disabled=false;
   }
 else
   {
   document.TarjetaNotas.tri.disabled=true;
   }

</script> 

</html>