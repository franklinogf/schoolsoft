<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Inf. de Deficiencia', 'Deficiency Report'],
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
    $title = $lang->translation('Inf. de Deficiencia');
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
            <?= $lang->translation('Inf. de Deficiencia') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="Deficiencia" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/Deficiencia.php') ?>">
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
                                <?= $lang->translation('Opción') ?>
                            </label>
                         </div>
                        <select id="titulo" name="titulo" class="form-control">
                            <option>
                                <?= $lang->translation('INFORME DE DEFICIENCIA') ?>
                            </option>
                            <option>
                                <?= $lang->translation('CURSOS A MEJORAR') ?>
                            </option>
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
                                <?= $lang->translation('Valor menor < que: ') ?>
                            </label>
                        </div>
                            <input name="valor" type="text">


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
                    </div>
                    <div class="style1">
                        <div ><b>
                              <?= $lang->translation('Notas para ver:') ?>
                        </b>
							<div></div>
						</div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 1') ?>
                            </label>
                        </div>
                        <input id="tri1b" name="tri1b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 2') ?>
                            </label>
                        </div>
                        <input id="tri2" name="tri2b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 1') ?>
                            </label>
                        </div>
                        <input id="sem1" name="sem1b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 3') ?>
                            </label>
                        </div>
                        <input id="tri3" name="tri3b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 4') ?>
                            </label>
                        </div>
                        <input id="tri4" name="tri4b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 2') ?>
                            </label>
                        </div>
                        <input id="sem2" name="sem2b" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Promedio final') ?>
                            </label>
                        </div>
                        <input id="prof" name="profb" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>

                    <div class="style1">
                        <div ><b>
                              <?= $lang->translation('Firmas') ?>
                        </b>
							<div></div>
						</div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Maestro') ?>
                            </label>
                        </div>
                        <input id="firm1" name="firm1" type="checkbox" style="height: 30px; width: 30px" value="Si" />

                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Padre/encargado') ?>
                            </label>
                        </div>
                        <input id="firm2" name="firm2" type="checkbox" style="height: 30px; width: 30px" value="Si" />

                    </div>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Registradora') ?>
                            </label>
                        </div>
                        <input id="firm3" name="firm3" type="checkbox" style="height: 30px; width: 30px" value="Si" />

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