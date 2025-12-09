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



]);
$school = new School(Session::id());
$re = $school->info('tar');
$year = $school->info('year2');

$grades = DB::table('year')->select("DISTINCT grado")->where([
        ['codigobaja', 0],
        ['year', $year],
        ['grado', '!=', ''],
   ])->orderBy('grado')->get();
$grupo = 'grado';

if ($re == '4')
   {
   $grades = $school->allGrades('alias');
   $grades = DB::table('year')->select("DISTINCT alias, year")->where([
        ['codigobaja', 0],
        ['year', $year],
        ['alias', '!=', ''],
   ])->orderBy('alias')->get();
   $grupo = 'alias';

   $students = DB::table('year')->where([
        ['codigobaja', 0],
        ['year', $year],
   ])->orderBy('apellidos')->get();

   }



$in1 = '';
$in2 = '';
$in3 = '';
$in4 = '';
$in4b = '';
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
$in22 = '';
$in26 = '';
$in31 = '';
$in43 = '';
if ($re == '1') {
    $in1 = 'selected';
}
if ($re == '2') {
    $in2 = 'selected';
}
if ($re == '3') {
    $in3 = 'selected';
}
if ($re == '4') {
    $in4 = 'selected';
}
if ($re == '5') {
    $in5 = 'selected';
}
if ($re == '7') {
    $in7 = 'selected';
}
if ($re == '8') {
    $in8 = 'selected';
}
if ($re == '13') {
    $in13 = 'selected';
}
if ($re == '14') {
    $in14 = 'selected';
}
if ($re == '17') {
    $in17 = 'selected';
}
if ($re == '18') {
    $in18 = 'selected';
}
if ($re == '22') {
    $in22 = 'selected';
}
if ($re == '26') {
    $in26 = 'selected';
}
if ($re == '31') {
    $in31 = 'selected';
}
if ($re == '43') {
    $in43 = 'selected';
}

$mensaj = DB::table('codigos')->orderBy('codigo')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    function activarTrimestre() {
        var dis = document.TarjetaNotas.tarjeta.value;
        if (dis == '2' || dis == '1b' || dis == '4' || dis == '1c') {
            document.TarjetaNotas.tri.disabled = false;
        } else {
            document.TarjetaNotas.tri.disabled = true;
        }

    }
</script>

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
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Informe de calificaciones') ?>
        </h1>
        <a href="<?= Route::url('/admin/access/gradesReports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/access/gradesReports/pdf/TarjetaOpciones.php') ?>">
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
                            <option value='1c' <?= $in1 ?>>Tarjeta cdls</option>
                            <option value='1' <?= $in1 ?>>Tarjeta 1</option>
                            <option value='1b' <?= $in4b ?>>Tarjeta 1b</option>
                            <option value='2' <?= $in2 ?>>Tarjeta 2</option>
                            <option value='3' <?= $in3 ?>>Tarjeta 3</option>
                            <option value='4' <?= $in4 ?>>Tarjeta 4</option>
                            <option value='5' <?= $in5 ?>>Tarjeta 5</option>
                            <option value='7' <?= $in7 ?>>Tarjeta 7</option>
                            <option value='8' <?= $in8 ?>>Tarjeta 8</option>
                            <option value='13' <?= $in13 ?>>Tarjeta 13</option>
                            <option value='14' <?= $in14 ?>>Tarjeta 14</option>
                            <option value='17' <?= $in17 ?>>Tarjeta 17</option>
                            <option value='18' <?= $in18 ?>>Tarjeta 18</option>
                            <option value='22' <?= $in22 ?>>Tarjeta 22</option>
                            <option value='26' <?= $in26 ?>>Tarjeta 26</option>
                            <option value='31' <?= $in31 ?>>Tarjeta 31</option>
                            <option value='43' <?= $in43 ?>>Tarjeta 43</option>
                        </select>
                        <select id="tri" name="tri" class="form-control" disabled="disable">
                            <option value='1'><?= $lang->translation('Trimestre 1') ?></option>
                            <option value='2'><?= $lang->translation('Trimestre 2') ?></option>
                            <option value='3'><?= $lang->translation('Trimestre 3') ?></option>
                            <option value='4'><?= $lang->translation('Trimestre 4') ?></option>
                            <option value='5'><?= $lang->translation('Semestre 1') ?></option>
                            <option value='6'><?= $lang->translation('Semestre 2') ?></option>
                            <option value='7'><?= $lang->translation('Q-1') ?></option>
                            <option value='8'><?= $lang->translation('Q-2') ?></option>
                            <option value='9'><?= $lang->translation('Q-3') ?></option>
                            <option value='10'><?= $lang->translation('Q-4') ?></option>
                            <option value='11'><?= $lang->translation('Q-5') ?></option>
                            <option value='12'><?= $lang->translation('Q-6') ?></option>
                            <option value='13'><?= $lang->translation('Q-7') ?></option>
                            <option value='14'><?= $lang->translation('Q-8') ?></option>
                            <option value='15'><?= $lang->translation('Final') ?></option>
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
                                <option value='<?= $grupo ?>'><?= $grupo ?></option>
                            <?php foreach ($grades as $grade){ ?>
                                <option value='<?= $grade->{"$grupo"} ?>'>
                                    <?= $grade->{"$grupo"} ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if ($grupo == 'alias') { ?>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="estu">
                                <?= $lang->translation('Estudiantes') ?>
                            </label>
                        </div>
                        <select id="estu" name="estu" class="form-control">
                                <option value=''>Selección</option>
                            <?php foreach ($students as $student){ ?>
                                <option value='<?= $student->ss ?>'>
                                    <?= $student->apellidos.' '.$student->nombre ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 1') ?>
                            </label>
                        </div>
                        <input id="tri1" name="tri1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 2') ?>
                            </label>
                        </div>
                        <input id="tri2" name="tri2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 1') ?>
                            </label>
                        </div>
                        <input id="sem1" name="sem1" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 3') ?>
                            </label>
                        </div>
                        <input id="tri3" name="tri3" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Trimestre 4') ?>
                            </label>
                        </div>
                        <input id="tri4" name="tri4" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Semestre 2') ?>
                            </label>
                        </div>
                        <input id="sem2" name="sem2" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con promedio final') ?>
                            </label>
                        </div>
                        <input id="prof" name="prof" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con Créditos') ?>
                            </label>
                        </div>
                        <input id="cr" name="cr" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Con firma') ?>
                            </label>
                        </div>
                        <input id="fir" name="fir" type="checkbox" style="height: 30px; width: 30px" value="Si" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Mensaje') ?>
                            </label>
                        </div>
                        <select id="grade" name="mensaje" class="form-control">
                            <option><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($mensaj as $mes) { ?>
                                <option value='<?= $mes->codigo ?>'>
                                    <?= $mes->codigo ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Comentario') ?>
                            </label>
                        </div>
                        <select id="comentario" name="comentario" class="form-control">
                            <option><?= $lang->translation('Selección') ?></option>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
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
<script language="JavaScript">
    var dis = document.TarjetaNotas.tarjeta.value;
    if (dis == '2' || dis == '1b' || dis == '4') {
        document.TarjetaNotas.tri.disabled = false;
    } else {
        document.TarjetaNotas.tri.disabled = true;
    }

</script>

</html>