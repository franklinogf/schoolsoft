<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Pantalla para borrar notas', 'Screen for deleting notes'],
    ['Reporte de Notas', 'Grade Report'],
    ['Borrar Semestre', 'Delete Semester'],
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
    ['Año', 'Year'],

]);
$school = new School(Session::id());
$years = DB::table('year')->select("DISTINCT year")->get();

if (isset($_POST['Submit1'])) {
    $year = $_POST['year'] ?? '';
    $sem = $_POST['sem'] ?? '';
    if ($sem == 'A') {
        $thisCourse2 = DB::table("acumulativa")->where([
            ['year', $year]
        ])->update([
            'sem1' => '',
        ]);
    }
    if ($sem == 'B') {
        $thisCourse2 = DB::table("acumulativa")->where([
            ['year', $year]
        ])->update([
            'sem2' => '',
        ]);
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php
    $title = $lang->translation('Pantalla para borrar notas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Rangos</title>
    <script language="Javascript" type="text/javascript">
        function confirmar(mensaje) {
            return confirm(mensaje);
        }

        function bosi() {
            var dis = document.transd.acp.value;

            if (acp.checked == 1) {
                document.transd.Submit1.disabled = false;
            } else {
                document.transd.Submit1.disabled = true;
            }
        }
    </script>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Pantalla para borrar notas') ?>
        </h1>
        <button onclick="history.back()" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></button>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form name="transd" id="transd" method="POST">
                <div class="mx-auto" style="max-width: 500px;"> <?php if (Session::get('createGrades')): ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Año') ?>
                            </label>
                            <select id="year" name="year" class="custom-select">
                                <?php foreach ($years as $year) : ?>
                                    <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Opción') ?>
                            </label>
                        </div>
                        <select id="sem" name="sem" class="form-control">
                            <option selected=""><?= $lang->translation('Selección') ?></option>
                            <option value="A">Sem-1</option>
                            <option value="B">Sem-2</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Borrar Semestre') ?>
                            </label>
                        </div>
                        <input id="acp" name="acp" type="checkbox" value="1" style="width: 21px; height: 24px" onclick="return bosi(); return true" />
                    </div>
                    <div class="input-group mb-3">
                    </div>
                    <div class="btn btn-primary mx-auto">
                        <input disabled="disabled" name="Submit1" type="submit" value="Borrar" style="width: 144px" onclick="return confirmar('&iquest;Está seguro que desea borrar el semestre?')" />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>