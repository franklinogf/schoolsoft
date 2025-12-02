<?php

require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$teacherId = $_POST['teacherId'] ?? null;

$plans = null;
$teacher = null;


if ($teacherId) {
    $teacher = Teacher::with('englishPlans')->find($teacherId);
    if (!$teacher) {
        die('Profesor no encontrado');
    }
    $plans = $teacher->englishPlans;
}

$teachers = Teacher::all();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Plan de inglés");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Plan de inglés") ?></h1>
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
                <h2 class="text-center my-4"><?= __("Planes de inglés para") . " $teacher->apellidos $teacher->nombre" ?></h2>
                <?php if ($plans && $plans->count() > 0) : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?= __("Grado") ?></th>
                                    <th><?= __("Tema") ?></th>
                                    <th><?= __('Imprimir') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plans as $plan) : ?>
                                    <tr>
                                        <td><?= $plan->grade ?></td>
                                        <td><?= $plan->subject ?></td>
                                        <td><a target="_blank" href="<?= Route::url("/admin/access/plans/englishplan/pdf.php?id={$plan->{$plan->getKeyName()}}") ?>" class="btn btn-sm btn-primary"><?= __("Imprimir") ?></ata>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-center"><?= __("No se encontraron planes de inglés para este profesor.") ?></p>
                <?php endif; ?>

            <?php endif; ?>




        </div>

        <?php
        Route::includeFile('/includes/layouts/scripts.php', true);
        Route::selectPicker('js');
        ?>
</body>

</html>