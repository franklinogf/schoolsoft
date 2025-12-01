<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use App\Models\WeeklyPlan;
use App\Models\WeeklyPlan2;
use App\Models\WeeklyPlan3;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacherId = $_POST['teacherId'] ?? null;
$planNumber = $_GET['plan'] ?? null;

if (!$planNumber) {
    die('No ha seleccionado ningún plan.');
}

if (!in_array($planNumber, ['1', '2', '3'])) {
    die('Plan no válido.');
}

$plans = null;
$teacher = null;


if ($teacherId) {
    $withPlan = match ($planNumber) {
        '1' => 'weeklyPlans',
        '2' => 'weeklyPlans2',
        '3' => 'weeklyPlans3',
        default => null,
    };
    $teacher = Teacher::with([$withPlan])->find($teacherId);
    if (!$teacher) {
        die('Profesor no encontrado');
    }
    $plans = $teacher->{$withPlan};
}

$teachers = Teacher::all();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Plan semanal :plan", ['plan' => $planNumber]);
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Plan semanal :plan", ['plan' => $planNumber]) ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form method="POST">
                <div class="mx-auto" style="width: 20rem;">
                    <select name="teacherId" class="selectpicker w-100 mb-2" data-live-search="true" required>
                        <option value=""><?= __("Seleccionar") ?></option>
                        <?php foreach ($teachers as $teacherRow) : ?>
                            <option <?= isset($_POST['teacherId']) && $_POST['teacherId'] == $teacherRow->id ? 'selected' : '' ?> value="<?= $teacherRow->id ?>"><?= "$teacherRow->apellidos $teacherRow->nombre ($teacherRow->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-block"><?= __("Seleccionar") ?></button>
                    </div>
                </div>
            </form>


            <!-- list of plans to print -->
            <?php if ($teacher) : ?>
                <h2 class="text-center my-4"><?= __("Planes de trabajo para") . " $teacher->apellidos $teacher->nombre" ?></h2>
                <?php if ($plans && $plans->count() > 0) : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <?php if ($planNumber !== '3'): ?>
                                        <th><?= __("Tema") ?></th>
                                        <th><?= __("Unidad") ?></th>
                                    <?php else: ?>
                                        <th><?= __("Curso") ?></th>
                                        <th><?= __("Semana") ?></th>
                                    <?php endif; ?>
                                    <th><?= __('Imprimir') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plans as $plan) : ?>
                                    <tr>
                                        <?php if ($planNumber !== '3'): ?>
                                            <td><?= $plan->tema ?></td>
                                            <td><?= $plan->unidad ?></td>
                                        <?php else: ?>
                                            <td><?= $plan->curso ?></td>
                                            <td><?= $plan->getFormattedWeek() ?></td>
                                        <?php endif; ?>
                                        <td><a target="_blank" href="<?= Route::url("/admin/access/plans/weeklyplans/pdf.php?plan={$planNumber}&id={$plan->{$plan->getKeyName()}}") ?>" class="btn btn-sm btn-primary"><?= __("Imprimir") ?></ata>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-center"><?= __("No se encontraron planes de trabajo para este profesor.") ?></p>
                <?php endif; ?>

            <?php endif; ?>




        </div>

        <?php
        Route::includeFile('/includes/layouts/scripts.php', true);
        Route::selectPicker('js');
        ?>
</body>

</html>