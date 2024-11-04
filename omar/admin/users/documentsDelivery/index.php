<?php
require_once '../../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$students = new Student();
$lang = new Lang([
    ['Entrega de documentos', 'Documents delivery'],
    ['Estudiante', 'Student'],
    ['Buscar', 'Search'],
]);
$year = $students->info('year');
$documents = DB::table('docu_entregados')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Entrega de documentos");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center"><?= $lang->translation("Entrega de documentos") ?></h1>
        <div class="row">
            <div class="col-12">
                <form method="POST">
                    <select class="form-control selectpicker w-100" name="student" data-live-search="true" required>
                        <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                        <?php foreach ($students->All() as $student): ?>
                            <option <?= isset($_REQUEST['student']) && $_REQUEST['student'] == $student->ss ? 'selected=""' : '' ?> value="<?= $student->ss ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar") ?></button>
                </form>

            </div>
        </div>
        <?php if (isset($_POST['student'])): ?>
            <form id="documents" method="POST">
                <input type="hidden" name="studentSS" value="<?= $_POST['student'] ?>">
                <div class="row row-cols-2 row-cols-md-4 mt-3">
                    <?php foreach ($documents as $index => $document):

                        $docu = DB::table("docu_estudiantes")->where([
                            ['ss', $_POST['student']],
                            ['codigo', $document->codigo]
                        ])->first();

                        ?>
                        <div class="col mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $document->desc1 ?></h5>
                                </div>
                                <div class="card-footer">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="doesntApply<?= $index ?>" id="doesntApply<?= $index ?>" <?= $docu && $docu->nap === 'Si' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="doesntApply<?= $index ?>">No aplica</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="delivered<?= $index ?>" id="delivered<?= $index ?>" <?= $docu && $docu->entrego === 'Si' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="delivered<?= $index ?>">Entregado</label>
                                    </div>
                                    <br>
                                    <label for="date<?= $index ?>">Fecha</label>
                                    <input type="date" class="form-control" name="date<?= $index ?>" id="date<?= $index ?>" value="<?= $docu ? $docu->fecha : '' ?>">
                                    <label for="expirationDate<?= $index ?>">Fecha de expiración</label>
                                    <input type="date" class="form-control" name="expirationDate<?= $index ?>" id="expirationDate<?= $index ?>" value="<?= $docu ? $docu->fesp : '' ?>">

                                </div>

                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="text-center">
                    <input id="save" class="btn btn-primary btn-lg" type="submit" value="Guardar">
                </div>

            </form>
        </div>
    <?php endif ?>

    </div>


    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');

    ?>

</body>

</html>