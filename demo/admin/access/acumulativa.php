<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Transcripción de crédito', 'Credit transcription'],
    ['Reporte de Notas', 'Grade Report'],
    ['Idioma', 'Language'],
    ['Grado', 'Grade'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Transcripción', 'Transcription'],
    ['Transferir', 'Transfer'],
    ['Añadir/Editar', 'Add/Edit'],
    ['Clasificación', 'Ranking'],
    ['Lista Rango 4.00', 'Ranking List 4.00'],
    ['Lista Rango %', 'Ranking List %'],
    ['Año', 'Year'],
    ['No ha completado', 'has not completed'],
    ['Promedio', 'Average'],
    ['Letra', 'Letter'],
    ['Número', 'Number'],
    ['Créditos en Progreso', 'Credits in Progress'],
    ['Formato', 'Format'],
    ['Fecha graduación', 'Graduation date'],
    ['Estudiante', 'Student'],
    ['Atrás', 'Go back'],
    ['Mensaje', 'Message'],
    ['Comentario', 'Comment'],
    ['Selección', 'Selection'],
    ['Borrar', 'Delete'],



]);
$school = new School(Session::id());
$grades = $school->allGrades();
$years = DB::table('year')->select("DISTINCT year")->get();

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
$in21 = '';
$in22 = '';
$in23 = '';
$in24 = '';
$in25 = '';
$in26 = '';
$in27 = '';
$in28 = '';
$in29 = '';
$in30 = '';
$in31 = '';
$in32 = '';
$in33 = '';
if ($re == '1') {
    $in1 = 'selected';
}
if ($re == '2') {
    $in2 = 'selected';
}
if ($re == '3') {
    $in3 = 'selected';
}

$students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")->orderBy('apellidos')->get();


//$mensaj = DB::table('codigos')->orderBy('codigo')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    function activarOpcion() {
        var dis = document.TarjetaNotas.opcion.value;
        if (dis == '2') {
            document.TarjetaNotas.grados.disabled = false;
            document.TarjetaNotas.Year.disabled = false;
            document.TarjetaNotas.fg.disabled = false;
            document.TarjetaNotas.estu.disabled = true;
        } else {
            document.TarjetaNotas.grados.disabled = true;
            document.TarjetaNotas.Year.disabled = true;
            document.TarjetaNotas.fg.disabled = true;
            document.TarjetaNotas.estu.disabled = false;
        }

    }

    function activarFgra() {
        var dis = document.TarjetaNotas.fg.value;

        if (fg.checked == 1) {
            document.TarjetaNotas.fdg.disabled = false;
        } else {
            document.TarjetaNotas.fdg.disabled = true;
        }

    }

    function activarVariables() {
        var now = new Date();
        var time = now.getTime();
        time += 1800 * 1000;
        now.setTime(time);

        var miVariablea = document.TarjetaNotas.gradorank.value;
        document.cookie = 'variable1=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.tarjeta.value;
        document.cookie = 'variable2=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.tnot.value;
        document.cookie = 'variable3=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.grado.value;
        document.cookie = 'variable4=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.idioma.value;
        document.cookie = 'variable5=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.opcion.value;
        document.cookie = 'variable6=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.grados.value;
        document.cookie = 'variable7=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.Year.value;
        document.cookie = 'variable8=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.estu.value;
        document.cookie = 'variable9=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        //var miVariablea = document.TarjetaNotas.cp.value;
        var dis = document.TarjetaNotas.cp.value;
        document.cookie = 'variable10=' + cp.checked + '; expires=' + now.toGMTString() + '; path=/';

        var dis = document.TarjetaNotas.fg.value;
        document.cookie = 'variable11=' + fg.checked + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.fdg.value;
        document.cookie = 'variable12=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.memsa1.value;
        document.cookie = 'variable13=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var miVariablea = document.TarjetaNotas.memsa2.value;
        document.cookie = 'variable14=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';

        var dis = document.TarjetaNotas.nhc.value;
        document.cookie = 'variable15=' + nhc.checked + '; expires=' + now.toGMTString() + '; path=/';



    }
</script>

<head>
    <?php
    $title = $lang->translation('Transcripción de crédito');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Transcripción de crédito') ?>
        </h1>
        <button onclick="history.back()" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></button>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="gradesReports/<?= Route::url('/admin/access/gradesReports/pdf/TarjetaOpciones.php') ?>">
                <div class="mx-auto" style="max-width: 600px;">
                    <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Formato') ?>
                            </label>
                        </div>
                        <select id="tarjeta" name="tarjeta" class="form-control" onclick="return activarTrimestre(); return true">
                            <option value='1' <?= $in1 ?>>Tarjeta 1</option>
                            <option value='2' <?= $in2 ?>>Tarjeta 2</option>
                            <option value='9' <?= $in9 ?>>Tarjeta 9</option>
                            <option value='14' <?= $in14 ?>>Tarjeta 14</option>
                            <option value='32' <?= $in3 ?>>Tarjeta 32</option>
                            <option value='33' <?= $in3 ?>>Tarjeta 33</option>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Promedio') ?>
                            </label>
                        </div>
                        <select id="tnot" name="tnot" class="form-control">
                            <option value="A"><?= $lang->translation('Letra') ?></option>
                            <option value="B"><?= $lang->translation('Número') ?></option>
                            <option value="C">L-M</option>
                        </select>


                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grado" name="grado" class="form-control">
                            <option value="A">01-04</option>
                            <option value="B">05-08</option>
                            <option value="C" selected="">09-12</option>
                            <option value="D">01-08</option>
                        </select>


                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Idioma') ?>
                            </label>
                        </div>
                        <select id="idioma" name="idioma" class="form-control" required>
                            <option value='A'>Español</option>
                            <option value='B'>English</option>
                        </select>



                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Opción') ?>
                            </label>
                        </div>
                        <select id="opcion" name="opcion" class="form-control" required onclick="return activarOpcion(); return true">
                            <option value='1'><?= $lang->translation('Estudiante') ?></option>
                            <option value='2'><?= $lang->translation('Grado') ?></option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        </div>
                        <select id="estu" name="estu" class="custom-select" required>
                            <?php foreach ($students as $student) : ?>
                                <option value="<?= $student->ss ?>"><?= $student->apellidos . ' ' . $student->nombre ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="grados" name="grados" class="form-control" required disabled="disabled">
                            <?php foreach ($grades as $grade): ?>
                                <option value='<?= $grade ?>'>
                                    <?= $grade ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation('Año') ?></label>
                        </div>
                        <select id="Year" name="Year" class="custom-select" required disabled="disabled">
                            <?php foreach ($years as $year) : ?>
                                <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                            <?php endforeach ?>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Fecha graduación') ?>
                            </label>
                        </div>
                        <input id="fg" name="fg" type="checkbox" disabled="disabled" style="height: 30px; width: 30px" value="Si" onclick="return activarFgra(); return true" />
                        <input id="fdg" name="fdg" type="date" size="25" disabled="disabled">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Créditos en Progreso') ?>
                            </label>
                        </div>
                        <input id="cp" name="cp" type="checkbox" style="height: 30px; width: 30px" value="Si" />

                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('No ha completado') ?>
                            </label>
                        </div>
                        <input id="nhc" name="nhc" type="checkbox" style="height: 30px; width: 30px" value="Si" />



                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Comentario') ?>
                            </label>
                        </div>


                    </div>
                    <div class="input-group mb-3">
                        <input id="memsa1" name="memsa1" type="text" size="80" maxlength="100">
                    </div>
                    <div class="input-group mb-3">
                        <input id="memsa2" name="memsa2" type="text" size="80" maxlength="100">
                    </div>

                    <div class="input-group mb-3 col-12 mt-2">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Grado') ?>
                            </label>
                        </div>
                        <select id="gradorank" name="gradorank" class="form-control">
                            <option value="A">01-08</option>
                            <option value="B" selected="">09-12</option>
                            <option value="C">09-11</option>
                            <option value="D">09-10</option>
                        </select>

                        <a class="btn btn-primary mx-auto" onclick="return activarVariables(); return true" href="pdf/List_rango.php" target="_blank">
                            <?= $lang->translation('Lista Rango %') ?>
                        </a>
                        <div>
                            <label class="input-group mb-3 col-8 mt-2">
                                <?= '  ' ?>
                            </label>
                        </div>
                        <a class="btn btn-primary mx-auto" onclick="return activarVariables(); return true" href="pdf/acumula_rango.php" target="_blank">
                            <?= $lang->translation('Lista Rango 4.00') ?>
                        </a>
                        <div>
                            <label class="input-group mb-3 col-8 mt-2">
                                <?= '  ' ?>
                            </label>
                        </div>

                        <a class="btn btn-primary mx-auto" onclick="return activarVariables(); return true" href="acumula_rango.php">
                            <?= $lang->translation('Clasificación') ?>
                        </a>


                    </div>


                    <div class="input-group mb-3">

                        <a class="btn btn-primary d-block mx-auto" href="Transfer.php">
                            <?= $lang->translation('Transferir') ?>
                        </a>
                        <a class="btn btn-primary mx-auto" href="acumula_borrar.php">
                            <?= $lang->translation('Borrar') ?>
                        </a>
                        <a class="btn btn-primary mx-auto" href="add_edit.php">
                            <?= $lang->translation('Añadir/Editar') ?>
                        </a>
                        <a class="btn btn-primary mx-auto" onclick="return activarVariables(); return true" href="pdf/acumula_tarjeta.php" target="_blank">
                            <?= $lang->translation('Transcripción') ?>
                        </a>

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
    if (dis == '2') {
        document.TarjetaNotas.tri.disabled = false;
    } else {
        document.TarjetaNotas.tri.disabled = true;
    }
</script>

</html>