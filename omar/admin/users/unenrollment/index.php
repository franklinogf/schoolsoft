<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$lang = new Lang([
    ["Dar de baja", "Unenrollment"],
    ["estudiante", "student"],
    ['Guardar', 'Save'],
    ['Buscar', 'Search'],
    ["Código de baja", "Unenrollment code"],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ["Sin baja", "No unenrollment"],
]);
$codes = DB::table('codigo_bajas')->get();
$students = new Student();
$allStudents = $students->All(null, true);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Dar de baja');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Dar de baja') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 25rem;">
            <form method="POST">
                <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                    <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                    <?php foreach ($allStudents as $student) : ?>
                        <option <?= isset($_POST['student']) && $_POST['student'] == $student->ss ? 'selected=""' : '' ?> value="<?= $student->ss ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                    <?php endforeach ?>
                </select>
                <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
            </form>
            <?php if ($_POST['student']) :
                $student = new Student($_POST['student']);
            ?>

                <div class="form-group">
                    <label for="unerollmentCode">Código de baja</label>
                    <select id="unerollmentCode" class="form-control">
                        <option value=""><?= $lang->translation("Sin baja") ?></option>
                        <?php foreach ($codes as $code) : ?>
                            <option <?= $student->codigobaja === $code->codigo ? 'selected' : '' ?> value="<?= $code->codigo ?>"><?= $code->descripcion ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="unerollmentDate">Fecha de baja</label>
                    <input type="date" id="unerollmentDate" class="form-control" value="<?= $student->fecha_baja ?>" <?= $student->codigobaja === '0' ? 'disabled' : '' ?>>
                </div>
                <input type="hidden" id="studentSS" value="<?= $student->ss ?>">
                <button class="btn btn-primary btn-sm btn-block mt-2" id="save"><?= $lang->translation("Guardar") ?></button>


            <?php endif ?>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>

</body>

</html>