<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Lista de rango', 'Rank List'],
    ['Reporte de Notas', 'Grade Report'],
    ['Código del Curso: ', 'Course Code: '],
    ['Valor menor < que: ', 'Value less than: '],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Si', 'Yes'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Notas para ver:', 'Notes to see:'],
    ['Selección:', 'Selection:'],
    ['Promedio final', 'Final average'],
    ['Con Lineas:', 'With Lines:'],
    ['Curso', 'Course'],
    
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
    $title = $lang->translation('Lista de rango');
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
            <?= $lang->translation('Lista de rango') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/ListaRango.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('ListaFracasados', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Código del Curso: ') ?>
                            </label>
                         </div>
                            <input name="curso" type="text">

                    </div>
                    <div class="input-group mb-3">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección:') ?>
                            </label>
                        </div>
                        <select id="nota" name="nota" class="form-control" required>
                               <option value='nota1'><?= $lang->translation('Trimestre 1') ?></option>
                               <option value='nota2'><?= $lang->translation('Trimestre 2') ?></option>
                               <option value='nota3'><?= $lang->translation('Trimestre 3') ?></option>
                               <option value='nota4'><?= $lang->translation('Trimestre 4') ?></option>
                               <option value='sem1'><?= $lang->translation('Semestre 1') ?></option>
                               <option value='sem2'><?= $lang->translation('Semestre 2') ?></option>
                               <option value='final'><?= $lang->translation('Promedio final') ?></option>
                        </select>

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Con Lineas:') ?>
                            </label>
                        </div>
                        <select id="conlinia" name="conlinia" class="form-control">
                            <option value='1'>
                                <?= $lang->translation('Si') ?>
                            </option>
                            <option value='0'>No</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                    </div>

                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                    <div class="input-group mb-3">
                    </div>
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