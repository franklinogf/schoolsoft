<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Lang;
use Classes\Util;

$school = new School();
$lang = new Lang([
    ['Entrada de asistencias', 'Assists entry'],
    ['Asistencia de los estudiantes', 'Student assistance'],
    ["Fecha para la asistencia", "Date for assistance"],
    ["Lista de estudiantes", "Students list"],
    ["Estudiantes", "Students"],
    ["Codigo de asistencia", "Attendance code"]
]);
$grades = DB::table('year')
    ->select('DISTINCT grado')
    ->where('year', $school->info('year2'))
    ->orderBy('grado')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Asistencia de los estudiantes');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mt-2"><?= $lang->translation("Entrada de asistencias") ?></h1>
        <div class="d-flex justify-content-center">
            <div class="form-group mt-5">
                <label for="date"><?= $lang->translation("Fecha para la asistencia") ?></label>
                <input class="form-control" type="date" id="date" value="<?= date('Y-m-d') ?>">
            </div>
        </div>

    </div>
    <div class="container-fluid">
        <div id="gradesButtons" class="d-flex flex-wrap justify-content-center">
            <?php foreach ($grades as $grade) : ?>
                <button class="btn btn-outline-primary mr-2 mt-1 flex-grow-1" data-grade="<?= $grade->grado ?>"><?= $grade->grado ?></button>
            <?php endforeach ?>
        </div>
    </div>
    <div class="container-lg">
        <div id="studentsList" class="table-responsive mt-5 invisible">
            <table class="table table-striped table-sm">
                <caption><?= $lang->translation("Lista de estudiantes") ?></caption>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" class="text-center"><?= $lang->translation("Estudiantes") ?></th>
                        <th scope="col" class="text-center"><?= $lang->translation("Codigo de asistencia") ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script type="text/javascript">
        const attendanceCodes = <?= json_encode(Util::$attendanceCodes) ?>
    </script>
</body>

</html>