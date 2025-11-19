<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;

Session::is_logged();
$teacher = new Teacher();
$lang = new Lang([
    ['Informe de documentos', 'Document report'],
    ['Repartidas', 'Distributed'],    
    ['Atrás', 'Go back'],
    ['Opción', 'Option'],    
    ['Lista', 'List'],
    ['Resumen bajo nivel', 'Under level summary'],
    ['Resumen sobre nivel', 'Over level summary'],
    ['Lista de estudiantes', 'Students list'],
    ['Totales', 'Totals'],
]);
$students = new Student();
$allStudents = $students->all();
$school = new School(Session::id());

$year = $school->info('year2');
$students = DB::table('docu_entregados')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Informe de documentos');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Informe de documentos') ?></h1>
        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/admin/access/reports/pdf/DocumentReport.php') ?>" target="DocumentReport.php" method="POST">
                <div class="mx-auto" style="width: 25rem;">
                    <!-- <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <label class='input-group-text'><?= $lang->translation("Repartidas") ?>:</label>
                        </div>
                        <input class="form-control" type="text" name="distributed">
                    </div>    -->
                    <div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label class='input-group-text'><?= $lang->translation("Opción") ?></label>
                            </div>
                            <select class="form-control w-100" id="option" name="option" required>
                           <?php  foreach ($students as $docu) { 
                               echo "<option value='".$docu->codigo."'>".$docu->desc1."</option>";
                               } ?>
                               <!-- <option value="4"><?= $lang->translation('Lista de estudiantes') ?></option> -->
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
    ?>
</body>

</html>