<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$admins = DB::table('colegio')->select('usuario')->get();

/* ------------------------------- Transaltion ------------------------------ */
$lang = new Lang([
    ['Enviar mensaje de texto', 'Send SMS'],
    ['Estudiantes', 'Students'],
    ['Cursos', 'Classes'],
    ['Mensaje individual', 'One person message'],
    ["Celular", "Cellphone"],
    ["Compañia del celular", "Cellphone company"],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Enviar mensaje de texto");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation("Enviar mensaje de texto") ?></h1>
        <div class="jumbotron bg-secondary shadow-sm py-3">
            <div class="row row-cols-1 row-cols-md-3">
                <div class="col mb-3 mb-md-0">
                    <button data-id="students" class="btn btn-outline-light btn-block btn-lg options"><?= $lang->translation("Estudiantes"); ?></button>
                </div>
                <div class="col mb-3 mb-md-0">
                    <button data-id="classes" class="btn btn-outline-light btn-block btn-lg options"><?= $lang->translation("Cursos"); ?></button>
                </div>
                <div class="col">
                    <button data-id="individual" class="btn btn-outline-light btn-block btn-lg options"><?= $lang->translation("Mensaje individual"); ?></button>
                </div>
            </div>
        </div>
        <?php if (Session::get('smsSent')) : ?>
            <div class="alert alert-info" role="alert">
                <?= Session::get('smsSent', true) ?>
            </div>
        <?php endif ?>
        <div id="value" class="container bg-white shadow-lg py-3 rounded hidden">
            <form id="form" action="<?= Route::url('/regiweb/options/sms/form.php') ?>" method="post">

                <div id="students" class="mb-3 option hidden">
                    <?php

                    $__tableData = $teacher->getAllStudents();
                    $__tableDataCheckbox = true;
                    $__tableDataName = 'students';
                    $__dataPk = 'ss';
                    Route::includeFile('/includes/layouts/table.php', true)
                    ?>
                </div>

                <div id="classes" class="mb-3 option hidden">
                    <?php
                    $__tableData = $teacher->classes();
                    $__tableDataCheckbox = true;
                    $__tableDataName = 'classes';
                    $__tableDataInfo = [
                        [
                            'title' => ["es" => "Curso", 'en' => "Class"],
                            'values' => ['curso']
                        ],
                        [
                            'title' => ["es" => "Descripción", 'en' => "Description"],
                            'values' => ['desc1']
                        ]
                    ];
                    $__dataPk = 'curso';
                    Route::includeFile('/includes/layouts/table.php', true)
                    ?>
                </div>
                <div id='individual' class="mb-3 option hidden">
                    <div class="input-group mb-3 justNumbers">
                        <div class="input-group-prepend">
                            <label class="input-group-text"><?= $lang->translation("Celular") ?></label>
                        </div>
                        <input type="text" class="form-control onlyNumbers" id="phoneNumber" name="phoneNumber">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text"><?= $lang->translation("Compañia del celular") ?></label>
                        </div>
                        <select name="phoneCompany" class="custom-select" required>
                            <option value="" selected><?= $lang->translation("Seleccionar"); ?></option>
                            <?php foreach (Util::phoneCompanies() as $company) : ?>
                                <option value="<?= $company ?>"><?= "$company" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

        </div>
        </form>

    </div>




    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>