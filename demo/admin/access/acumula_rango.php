<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Opciones de clasificación', 'Ranking Options'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado ', 'Grade '],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Ambos grados', 'Both grade'],
    ['Año:', 'Year:'],
    ['Selección de Cursos', 'Course Selection'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Con promedio final', 'With final average'],
    ['Con Créditos', 'With Credits'],
    ['Con S.S.', 'With S.S.'],
    ['Atrás', 'Go back'],
    ['Mensaje', 'Message'],
    ['Comentario', 'Comment'],
    ['Selección', 'Selection'],
    
    
    
]);
$school = new School(Session::id());
//$grades = DB::table('materias')->where('year', $school->info('year2'))->orderBy('grado')->get();
$grades = $school->allGrades();
$years = DB::table('year')->select("DISTINCT year")->get();


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
    $title = $lang->translation('Opciones de clasificación');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Opciones de clasificación') ?>
        </h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/pdf/acumula_rango_inf.php') ?>">
                <div class="mx-auto" style="max-width: 550px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class">
                                <?= $lang->translation('Año:') ?>
                            </label>
                        </div>
                        <select name="Year" class="form-control">
                            <?php foreach ($years as $year) : ?>
                                <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado ') ?>
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
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Ambos grados') ?>
                            </label>
                        </div>
                        <input id="gra" name="gra" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con S.S.') ?>
                            </label>
                        </div>
                        <input id="ss" name="ss" type="checkbox" style="height: 30px; width: 30px" value="1" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            01</label>
                        </div>
                        <input id="gr1" name="gr1" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            02</label>
                        </div>
                        <input id="gr2" name="gr2" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            03</label>
                        </div>
                        <input id="gr3" name="gr3" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            04</label>
                        </div>
                        <input id="gr4" name="gr4" type="checkbox" style="height: 30px; width: 30px" value="1" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            05</label>
                        </div>
                        <input id="gr5" name="gr5" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            06</label>
                        </div>
                        <input id="gr6" name="gr6" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            07</label>
                        </div>
                        <input id="gr7" name="gr7" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            08</label>
                        </div>
                        <input id="gr8" name="gr8" type="checkbox" style="height: 30px; width: 30px" value="1" />

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            09</label>
                        </div>
                        <input id="gr9" name="gr9" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            10</label>
                        </div>
                        <input id="gr10" name="gr10" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            11</label>
                        </div>
                        <input id="gr11" name="gr11" type="checkbox" style="height: 30px; width: 30px" value="1" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Grado ') ?>
                            12</label>
                        </div>
                        <input id="gr12" name="gr12" type="checkbox" style="height: 30px; width: 30px" value="1" />
                    </div>



                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Selección de Cursos') ?>
                            </label>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
                        <input maxlength="4" name="mat1" size="3" type="text" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
                        <input maxlength="4" name="mat1" size="3" type="text" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
                        <input maxlength="4" name="mat2" size="3" type="text" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
                        <input maxlength="4" name="mat3" size="3" type="text" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
                        <input maxlength="4" name="mat4" size="3" type="text" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('') ?>
                            </label>
                        </div>
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