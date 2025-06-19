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
    ['Lista de cumple Años', 'Birthday List'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Grado', 'Grade'],
    ['Grados separados', 'Separted grades'],
    ['estudiante', 'student'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Todos los meses', 'Every month'],
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
    $title = $lang->translation('Lista de cumple Años');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Lista de cumple Años') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/birthbayList.php') ?>" target="birthbayList" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                        </div>
                        <select name="mes" id="option" class="form-control">
                            <option value="00"><?= $lang->translation("Todos los meses") ?></option>
                            <option value="01"><?= $lang->translation("Enero") ?></option>
                            <option value="02"><?= $lang->translation("Febrero") ?></option>
                            <option value="03"><?= $lang->translation("Marzo") ?></option>
                            <option value="04"><?= $lang->translation("Abril") ?></option>
                            <option value="05"><?= $lang->translation("Mayo") ?></option>
                            <option value="06"><?= $lang->translation("Junio") ?></option>
                            <option value="07"><?= $lang->translation("Julio") ?></option>
                            <option value="08"><?= $lang->translation("Agosto") ?></option>
                            <option value="09"><?= $lang->translation("Septiembre") ?></option>
                            <option value="10"><?= $lang->translation("Octubre") ?></option>
                            <option value="11"><?= $lang->translation("Noviembre") ?></option>
                            <option value="12"><?= $lang->translation("Diciembre") ?></option>
                        </select>
                    </div>
                    <div id="grade">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="option"><?= $lang->translation("Grado") ?></label>
                            </div>
                            <select name="grade" class="form-control">
                                <option value=""><?= $lang->translation("Todos los grados") ?></option>
                                <?php foreach ($grades as $grade) : ?>
                                    <option value="<?= $grade ?>"><?= $grade ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-info">
                                    <input type="checkbox" checked id="separatedGrade" value="si"> <?= $lang->translation("Grados separados") ?>
                                </label>
                            </div>
                        </div>
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