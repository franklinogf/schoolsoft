<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
//use Classes\Controllers\Student;
//use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
//$teacher = new Teacher();
$lang = new Lang([
    ['No docentes', 'Not teachers'],
    ['Nuevos estudiantes', 'New students'],
    ['Diciembre', 'December'],
    ['Grado', 'Grade'],
    ['Grados separados', 'Separted grades'],
    ['estudiante', 'student'],
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],
    ['Todos los estudiantes', 'All students'],
    ['Por grado', 'By grade'],
    ['Estudiante', 'Student'],
    ['Todos los departamentos', 'All the departments'],
    ['Lista', 'List'],
    ['Resumen', 'Summary'],
]);
$school = new School();
$year = $school->year();

$depar = DB::table('departamentos')->orderBy('descripcion')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('No docentes');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('No docentes') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/notTeachers.php') ?>" target="notTeachers" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <div id="grade">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text' for="option"><?= $lang->translation("Opción") ?></label>
                            </div>
                            <select name="grade" class="form-control">
                                <option value=""><?= $lang->translation("Todos los departamentos") ?></option>
                                <?php foreach ($depar as $depars) : ?>
                                    <option value="<?= $depars->id ?>"><?= $depars->descripcion ?></option>
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