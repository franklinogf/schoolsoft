<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$teacher = new Teacher();
$lang = new Lang([
    ['Lista de direcciones maestros', 'Teacher address List'],
    ['Nuevos estudiantes', 'New students'],
    ['Dirección Residencial', 'Residential address'],
    ['Dirección Postal', 'Mailing address'],
    ['Grados separados', 'Separted grades'],
    ['estudiante', 'student'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Todos los estudiantes', 'All students'],
    ['Por grado', 'By grade'],
    ['Estudiante', 'Student'],
    ['Todos los grados', 'All grades'],
    ['Lista', 'List'],
    ['Resumen', 'Summary'],
]);
$students = new Student();
$allStudents = $students->all();
$school = new School();
$grades = $school->allGrades();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Lista de direcciones maestros');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Lista de direcciones maestros') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/addressProfesor.php') ?>" target="addressProfesor" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="prof" id="option" class="form-control">
                            <option value="R"><?= $lang->translation("Dirección Residencial") ?></option>
                            <option value="P"><?= $lang->translation("Dirección Postal") ?></option>
                        </select>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary mt-4" type="submit"><?= $lang->translation("Continuar") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
</body>

</html>