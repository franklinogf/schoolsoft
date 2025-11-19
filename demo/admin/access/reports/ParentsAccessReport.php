<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher();
$lang = new Lang([
    ['Informe de acceso de los profesores', 'Teacher Access Report'],
    ['Desde', 'From'],
    ['Hasta', 'To'],
    ['Grado', 'Grade'],
    ['Grados separados', 'Separted grades'],
    ['maestro', 'Teacher'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Por estudiante', 'By student'],
    ['Por grado', 'By grade'],
    ['Estudiante', 'Student'],
    ['Selección', 'Selection'],
    ['Lista', 'List'],
    ['Resumen', 'Summary'],
]);
$school = new School(Session::id());

$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Informe de acceso de los padres');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Informe de acceso de los maestros') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/TeacherAccessReport.php') ?>" target="ParentsAccessReport" target="_blank" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="from"><?= $lang->translation("Desde") ?>:</label>
                        </div>
                        <input id="from" class="form-control" type="date" name="from" required>
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text' for="to"><?= $lang->translation("Hasta") ?>:</label>
                        </div>
                        <input id="to" class="form-control" type="date" name="to" value="<?= Util::date() ?>" required>
                    </div>
                    <div id="student">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="student"><?= $lang->translation("Maestros") ?></label>
                            </div>
                            <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('maestro') ?></option>
                                <option value="Todos"><?= $lang->translation("Todos") ?></option>
                                <?php foreach ($teachers as $student) : ?>
                                    <option value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                                <?php endforeach ?>
                            </select>
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