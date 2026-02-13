<?php
require_once __DIR__ . '/../../app.php';

use App\Enums\DecimalTrimesterEnum;
use App\Enums\GradePageEnum;
use App\Enums\QuincenalTrimesterEnum;
use App\Enums\TrimesterEnum;
use App\Models\Admin;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use App\Models\Teacher;

Session::is_logged();
$teacher = Teacher::find(Session::id());

$school = Admin::primaryAdmin();

$lang = new Lang([
    ['Cursos', 'Grades'],
    ['Entrada de asistencias', 'Assists entry'],
    ['Informe de asistencias diarias', 'Daily attendance report'],
    ['Informe de asistencias', 'Attendance report'],
    ['Preescolar', 'Preschool'],
    ["Grado", "Grade"],
    ["Pagina", "Page"],
    ["Verano", "Summer"],
]);

$pages = GradePageEnum::getPages();

$trimesters = match (true) {
    school_is('bs') => QuincenalTrimesterEnum::cases(),
    school_is('cdls') => collect(QuincenalTrimesterEnum::cases())->filter(fn($trimester) => $trimester !== QuincenalTrimesterEnum::FOURTH_S1  && $trimester !== QuincenalTrimesterEnum::FOURTH_S2)->toArray(),
    $school->cppd === 'Si' => DecimalTrimesterEnum::cases(),
    default => TrimesterEnum::cases(),
};
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Cursos');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Cursos') ?></h1>
        <div class="jumbotron bg-secondary shadow-sm py-3">
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col mb-3">
                    <a href="attendance.php" class="btn btn-outline-light btn-block btn-lg <?= $teacher->grado === '' ? 'disabled' : '' ?>"><?= $lang->translation('Entrada de asistencias') ?></a>
                </div>
                <div class="col mb-3">
                    <a href="dailyAttendance.php" class="btn btn-outline-light btn-block btn-lg <?= $teacher->grado === '' ? 'disabled' : '' ?>"><?= $lang->translation('Informe de asistencias diarias') ?></a>
                </div>
                <div class="col">
                    <a href="attendance_report.php" class="btn btn-outline-light btn-block btn-lg"><?= $lang->translation('Informe de asistencias') ?></a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= $lang->translation('Preescolar') ?></a>
                </div>
            </div>
        </div>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= school_is('cdls', 'bs') ? Route::url('/regiweb/grades/enterGrades2.php') : Route::url('/regiweb/grades/enterGrades.php') ?>" method="post">
                <div class="mx-auto" style="width: 20rem;">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation('Grado') ?></label>
                        </div>
                        <select name="class" class="custom-select" id="class" required>
                            <?php foreach ($teacher->subjects as $subject): ?>
                                <option data-verano="<?= $subject->verano === '2' ? 'true' : 'false' ?>" value="<?= $subject->curso ?>"><?= "$subject->curso - $subject->descripcion" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tri"><?= __('Trimestre') ?></label>
                        </div>
                        <select class="custom-select" id="tri" name="tri" required>
                            <?php foreach ($trimesters as $trimester): ?>
                                <option value="<?= $trimester->value ?>"><?= $trimester->getLabel() ?></option>
                            <?php endforeach ?>
                        </select>
                        <!-- <input type="hidden" name="tri" id="hiddenTri"> -->

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tra"><?= $lang->translation("Pagina") ?></label>
                        </div>
                        <select class="custom-select" id="tra" name='tra' required>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?= $page->value ?>"><?= $page->getLabel() ?></option>
                            <?php endforeach ?>
                        </select>
                        <!-- <input type="hidden" name="tra" id="hiddenTra"> -->
                    </div>
                    <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= __("Continuar") ?>">
                </div>
            </form>

        </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>