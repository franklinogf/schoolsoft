<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;

Session::is_logged();
$parents = new Parents(Session::id());
$students = DB::table('year')->where([
    ['id', $parents->id]
])->get();
if (isset($_POST['student'])) {
    list($ss, $year) = explode(',', $_POST['student']);
    $attendances = DB::table('asispp')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderBy('fecha DESC')->get();
}
$lang = new Lang([
    ["Informe de asistencia", "Attendance report"],
    ["Estudiantes", "Students"],
    ["Asistencias", "Attendance"],
    ["Fecha", "Date"],
    ["Descripción", "Description"],
    ["No tiene información", "Has no information"],
    ["Ausencias", "Absence"],
    ["Tardanzas", "Tardy"]

]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Asistencias');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2"><?= $lang->translation("Informe de asistencia") ?></h1>
        <form target="_self" method="POST">
            <div class="form-row">
                <label class="font-weight-bold col-12" for="student"><?= $lang->translation("Estudiantes") ?></label>
                <select name="student" id="student" class="form-control col-12 col-lg-6">
                    <?php foreach ($students as $kid) : ?>
                        <option <?= isset($_POST['student']) && $_POST['student'] === "$kid->ss,$kid->year" ? 'selected=""' : '' ?> value="<?= "$kid->ss,$kid->year" ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado [$kid->year]" ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button id="attendanceBtn" type="submit" class="btn btn-primary mt-3"><?= $lang->translation("Asistencias") ?></button>
        </form>

        <div class="mt-5">
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th></th>
                        <th><?= $lang->translation("Fecha") ?></th>
                        <th><?= $lang->translation("Descripción") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (sizeof($attendances) > 0) :
                        $count = 1;
                        $codes = [0, 0];
                    ?>
                        <?php foreach ($attendances as $attendance) : ?>
                            <tr>
                                <td class="text-center"><?= $count ?></td>
                                <td class="text-center"><?= $attendance->fecha ?></td>
                                <td><?= Util::$attendanceCodes[$attendance->codigo]['description'][__LANG] ?></td>
                            </tr>
                        <?php $count++;
                            $codes[0] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'A' ? 1 : 0;
                            $codes[1] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'T' ? 1 : 0;
                        endforeach ?>
                    <?php else : ?>
                        <tr>
                            <td class="text-center" colspan="3"><?= $lang->translation("No tiene información") ?></td>
                        </tr>
                    <?php endif ?>
                </tbody>

            </table>
            <?php if (sizeof($attendances) > 0) : ?>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light text-center">
                        <tr>
                            <th><?= $lang->translation("Ausencias") ?></th>
                            <th><?= $lang->translation("Tardanzas") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td><?= $codes[0] ?></td>
                            <td><?= $codes[1] ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif ?>

        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>