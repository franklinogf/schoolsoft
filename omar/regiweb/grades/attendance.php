<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Lang;
use Classes\Util;

Session::is_logged();

$teacher = new Teacher(Session::id());
$attendanceOption = $teacher->info('asist');
$lang = new Lang([
    ['Entrada de asistencias', 'Assists entry'],
    ['Asistencia de los estudiantes', 'Student assistance'],
    ["Fecha para la asistencia", "Date for assistance"],
    ["Lista de estudiantes", "Students list"],
    ["Estudiantes", "Students"],
    ["Codigo de asistencia", "Attendance code"]
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Asistencia de los estudiantes');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mt-2"><?= $lang->translation("Entrada de asistencias") ?></h1>
        <!-- Required info -->
        <input type="hidden" id="attendanceOption" value="<?= $attendanceOption ?>">
        <div class="d-flex justify-content-center">
            <div class="form-group mt-5">
                <label for="date"><?= $lang->translation("Fecha para la asistencia") ?></label>
                <input class="form-control" type="date" id="date" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <?php if (__SCHOOL_ACRONYM !== 'cbtm') : ?>
            <?php if ($attendanceOption === "3") : ?>
                <div id="classButtons" class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($teacher->classes() as $class) : ?>
                        <button class="btn btn-outline-primary mr-2 mt-1 flex-grow-1" data-class="<?= $class->curso ?>" data-toggle="tooltip" data-placement="bottom" title="<?= $class->desc1 ?>"><?= $class->curso ?></button>
                    <?php endforeach ?>
                </div>
            <?php elseif ($attendanceOption === "2") :
                $grades = DB::table('year')
                    ->select('DISTINCT grado')
                    ->where('year', $teacher->info('year'))
                    ->orderBy('grado')->get();
            ?>
                <div id="gradesButtons" class="d-flex flex-wrap justify-content-center">
                    <?php foreach ($grades as $grade) : ?>
                        <button class="btn btn-outline-primary mr-2 mt-1 flex-grow-1" data-grade="<?= $grade->grado ?>"><?= $grade->grado ?></button>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        <?php else :
            if ($teacher->grado === '02-01') {
                $grades = ['02-01', 'K5'];
            } else if ($teacher->grado === '06-01') {
                $grades = ['06-01', '07-01', '08-01', '09-01', '10-01', '11-01', '12-01'];
            } else {
                $grades = [$teacher->grado];
            }

        ?>

            <div id="gradesButtons" class="d-flex flex-wrap justify-content-center <?= sizeof($grades) === 1 ? 'invisible' : '' ?>">
                <?php foreach ($grades as $grade) : ?>
                    <button class="btn btn-outline-primary mr-2 mt-1 flex-grow-1 <?= sizeof($grades) === 1 ? 'active' : '' ?>" data-grade="<?= $grade ?>"><?= $grade ?></button>
                <?php endforeach ?>
            </div>

        <?php endif ?>

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

    <script type="text/javascript">
        const attendanceCodes = <?= json_encode(Util::$attendanceCodes) ?>
    </script>
    <?php

    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>