<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Programa de clases', 'Class Program'],
    ['Reporte de Notas', 'Grade Report'],
    ['Código del Curso: ', 'Course Code: '],
    ['Valor menor < que: ', 'Value less than: '],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Todos', 'All'],
    ['Semestre 2', 'Semester 2'],
    ['1 por pagina', '1 per page'],
    ['2 por pagina', '2 per page'],
    ['3 por pagina', '3 per page'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Firmas', 'Signature'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Notas para ver:', 'Notes to see:'],
    ['Maestro', 'Maestro'],
    ['Padre/encargado', 'Parent/Guardian'],
    ['Registradora', 'Registrar'],
    ['Promedio final', 'Final average'],
    ['CURSOS A MEJORAR', 'COURSES TO IMPROVE'],
    ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],
    

    
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
    $title = $lang->translation('Programa de clases');
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
            <?= $lang->translation('Programa de clases') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="Deficiencia" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/ProgramaClases.php') ?>">
                <div class="mx-auto" style="max-width: 500px;">
                    <?php if (Session::get('Deficiencia')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('Deficiencia', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grade" name="grade" class="form-control" required>
                                <option value='all'>
                                    <?= $lang->translation('Todos') ?>
                                </option>

                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>                    <div class="input-group mb-3">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Opción') ?>
                            </label>
                         </div>
                        <select id="pagina" name="pagina" class="form-control">
                            <option value="1">
                                <?= $lang->translation('1 por pagina') ?>
                            </option>
                            <option value="2">
                                <?= $lang->translation('2 por pagina') ?>
                            </option>
                            <option value="3">
                                <?= $lang->translation('3 por pagina') ?>
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