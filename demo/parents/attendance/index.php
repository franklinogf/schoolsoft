<?php
require_once __DIR__ . '/../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$parents = Family::find(Session::id());

$colegio = Admin::primaryAdmin();

$year = $colegio->year;


$attendances = collect();
if (isset($_POST['student'])) {
    [$ss, $year] = explode(',', $_POST['student']);
    $attendances = Manager::table('asispp')->where([
        ['ss', $ss],
        ['year', $year]
    ])->orderByDesc('fecha')->get();
}
$li2=0;
$li1=0;

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
                    <?php foreach ($parents->kids as $kid): ?>
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
                        <th><?= __("Curso") ?></th>
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
                                <td class="text-center"><?= $attendance->curso ?></td>
                                <td><?php
        if ($attendance->codigo == 1) {
            $cod = 'Ausencia-situación en el hogar';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 2) {
            $cod = 'Ausencia-determinación del hogar(viaje)';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 3) {
            $cod = 'Ausencia-actividad con padres(open house)';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 4) {
            $cod = 'Ausencia-enfermedad';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 5) {
            $cod = 'Ausencia-cita';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 6) {
            $cod = 'Ausencia-actividad educativa del colegio';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 7) {
            $cod = 'Ausencia-sin excusa del hogar';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 15) {
            $cod = 'Ausencia-determinación de la familia';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 16) {
            $cod = 'Ausencia-problema de transportación';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 17) {
            $cod = 'Ausencia-protocolo salud';
            $li2 = $li2 + 1;
        }
        if ($attendance->codigo == 8) {
            $cod = 'Tardanza-sin excusa del hogar';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 9) {
            $cod = 'Tardanza-situación en el hogar';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 10) {
            $cod = 'Tardanza-problema en la transportación';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 11) {
            $cod = 'Tardanza-enfermedad';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 12) {
            $cod = 'Tardanza-cita';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 13) {
            $cod = 'Ausente protocolo COVID-19';
            $li1 = $li1 + 1;
        }
        if ($attendance->codigo == 14) {
            $cod = 'Fue recogido antes de la salida';
        }
        if ($attendance->codigo == 18) {
            $cod = 'Fue recogido antes de la salida - enfermedad';
        }
        if ($attendance->codigo == 19) {
            $cod = 'Fue recogido antes de la salida - personal';
        }
        if ($attendance->codigo == 20) {
            $cod = 'Actividad escolar - torneo';
        }
        if ($attendance->codigo == 21) {
            $cod = 'Fue recogido antes de la salida - cita';
        }
        if ($attendance->codigo == 22) {
            $cod = 'Suspensión';
        }
                                 ?><?= utf8_encode($cod)?>
                                 </td>
                            </tr>
                        <?php
//                            $codes[0] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'A' ? 1 : 0;
//                            $codes[1] += Util::$attendanceCodes[$attendance->codigo]['type'] === 'T' ? 1 : 0;
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
                            <td><?= $li2 ?></td>
                            <td><?= $li1 ?></td>
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