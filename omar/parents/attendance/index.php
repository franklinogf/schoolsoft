<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Util;

Session::is_logged();
$parents = new Parents(Session::id());
$students = DB::table('year')->where([
    ['id', $parents->id]
])->get();
if (isset($_POST['student'])) {
    list($ss, $year) = split(',', $_POST['student']);
    $attendances = DB::table('asispp')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderBy('fecha DESC')->get();
}
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Asistencia";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center my-2">Informe de asistencia</h1>
        <form target="_self" method="POST">
            <div class="form-row">
                <label class="font-weight-bold col-12" for="student">Estudiantes</label>
                <select name="student" id="student" class="form-control col-12 col-lg-6">
                    <?php foreach ($students as $kid) : ?>
                        <option <?= isset($_POST['student']) && $_POST['student'] === "$kid->ss,$kid->year" ? 'selected=""' : '' ?> value="<?= "$kid->ss,$kid->year" ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado [$kid->year]" ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button id="attendanceBtn" type="submit" class="btn btn-primary mt-3">Asistencias</button>
        </form>

        <div class="mt-5">
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th></th>
                        <th>Fecha</th>
                        <th>Descripción</th>
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
                                <td><?= Util::$attendanceCodes[$attendance->codigo]['description'] ?></td>
                            </tr>
                        <?php $count++;
                            $codes[0] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'A' ? 1 : 0;
                            $codes[1] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'T' ? 1 : 0;
                        endforeach ?>
                    <?php else : ?>
                        <tr>
                            <td class="text-center" colspan="3">No tiene información</td>
                        </tr>
                    <?php endif ?>
                </tbody>

            </table>
            <?php if (sizeof($attendances) > 0) : ?>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Ausencias</th>
                            <th>Tardanzas</th>
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