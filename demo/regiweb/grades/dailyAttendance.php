<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$lang = new Lang([
    ["Informe de asistencia diarias","Daily attendance report"],
    ["Seleccione las fechas","Select the dates"],
    ["Desde", "From"],
    ["Hasta", "To"],
    ["Salón hogar", "Home grade"],
    ["Por estudiante", "By student"],
    ["Tipo", "Type"],
    ["Lista", "List"],
    ["Resumen", "Summary"],
    ["Estudiantes", "Students"]
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Informe de asistencia diarias');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mt-2"><?= $lang->translation("Informe de asistencia diarias") ?></h1>
        <form action="<?= Route::url('/regiweb/grades/pdf/pdfDailyAttendance.php') ?>" method="post" target="pdfDailyAttendance">
            <h3 class="text-center"><?= $lang->translation("Seleccione las fechas") ?></h3>
            <div class="mx-1 row">
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation("Desde") ?></label>
                        </div>
                        <input class="form-control" type="date" name="date1" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation("Hasta") ?></label>
                        </div>
                        <input class="form-control" type="date" name="date2" value="<?= Util::date() ?>" required>
                    </div>
                </div>
                <div class="col-12 offset-md-3 col-md-6 mb-3">
                    <select name="option" id="option" class="form-control">
                        <option value="home"><?= $lang->translation("Salón hogar") ?></option>
                        <option value="students"><?= $lang->translation("Por estudiante") ?></option>
                    </select>
                </div>
                <div class="col-12 offset-md-3 col-md-6" id="infoType">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation("Tipo") ?></label>
                        </div>
                        <select name="type"  class="form-control">
                            <option value="list"><?= $lang->translation("Lista") ?></option>
                            <option value="resum"><?= $lang->translation("Resumen") ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-12 offset-md-3 col-md-6 hidden" id="infoStudents">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= $lang->translation("Estudiantes") ?></label>
                        </div>
                        <select name="ss" class="form-control">
                            <?php foreach ($teacher->homeStudents() as $student) : ?>
                                <option value="<?= $student->ss ?>"><?= "$student->apellidos, $student->nombre" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <input class="btn btn-primary mx-auto d-block my-3" type="submit" value="<?= $lang->translation("Continuar") ?>">
        </form>

    </div>

<?php
Route::includeFile('/includes/layouts/scripts.php', true);
?>
</body>

</html>