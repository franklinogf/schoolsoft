<?php
require_once '../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$parents = Family::find(Session::id());

$colegio = Admin::primaryAdmin()->first();

$year = $colegio->year;

$students = Student::byId(Session::id())->get();
$attendances = collect();
if (isset($_POST['student'])) {
    [$ss, $year] = explode(',', $_POST['student']);
    $attendances = Manager::table('asispp')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderByDesc('fecha')->get();
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head> <?php
        $title = __('Asistencias');
        Route::includeFile('/parents/includes/layouts/header.php');
        ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?> <div class="container mt-3">
        <h1 class="text-center my-2"><?= __("Informe de asistencia") ?></h1>
        <form method="post">
            <div class="form-row">
                <label class="font-weight-bold col-12" for="student"><?= __("Estudiantes") ?></label>
                <select name="student" id="student" class="form-control col-12 col-lg-6">
                    <?php foreach ($students as $kid): ?>
                        <option <?= isset($_POST['student']) && $_POST['student'] === "$kid->ss,$kid->year" ? 'selected=""' : '' ?> value="<?= "$kid->ss,$kid->year" ?>"><?= "$kid->nombre $kid->apellidos -> $kid->grado [$kid->year]" ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <button id="attendanceBtn" type="submit" class="btn btn-primary mt-3"><?= __("Asistencias") ?></button>
        </form>

        <div class="mt-5">
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th></th>
                        <th><?= __("Fecha") ?></th>
                        <th><?= __("Descripción") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($attendances) > 0):
                        $codes = [0, 0];
                    ?>
                        <?php foreach ($attendances as $index => $attendance): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="text-center"><?= $attendance->fecha ?></td>
                                <td><?= Util::$attendanceCodes[$attendance->codigo]['description'][__LANG] ?></td>
                            </tr>
                        <?php
                            $codes[0] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'A' ? 1 : 0;
                            $codes[1] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'T' ? 1 : 0;
                        endforeach ?> <?php else: ?>
                        <tr>
                            <td class="text-center" colspan="3"><?= __("No tiene información") ?></td>
                        </tr>
                    <?php endif ?>
                </tbody>

            </table>
            <?php if (count($attendances) > 0): ?>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light text-center">
                        <tr>
                            <th><?= __("Ausencias") ?></th>
                            <th><?= __("Tardanzas") ?></th>
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