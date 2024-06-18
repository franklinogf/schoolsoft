<?php
require_once '../../../app.php';

use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Lang;

Session::is_logged();
$lang = new Lang([
    ["Códigos Departamento", "Department codes"],
    ['Guardar', 'Save'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
    ['Editar', 'Edit'],
    ['Eliminar', 'Delete'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list']
]);
$codes = DB::table('departamentos')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Códigos Departamento');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Códigos Departamento') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 25rem;">
            <div class="row">
                <div class="input-group mb-3 col-6">
                    <div class="input-group-prepend">
                        <span class="input-group-text">ID</span>
                    </div>
                    <input type="text" class="form-control" id="id">
                </div>

                <div class="input-group mb-3 col-6">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= $lang->translation("Código") ?></span>
                    </div>
                    <input type="text" class="form-control" id="code">
                </div>

                <div class="input-group mb-3 col-12">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><?= $lang->translation("Descripción") ?></span>
                    </div>
                    <input type="text" class="form-control" id="description">
                </div>
            </div>
            <small id="alertMsg" class="text-danger invisible"><?= $lang->translation("Debe de llenar todos los campos") ?></small>
            <button id="submitBtn" data-option='add' class="btn btn-primary btn-block"><?= $lang->translation("Guardar") ?></button>
        </div>

        <h2 class="text-center my-5 <?php sizeof($codes) > 0 ? '' : 'invisible' ?>"><?= $lang->translation("Lista de codigos") ?></h2>
        <div id="codesList" class="row row-cols-1 row-cols-md-4">
            <?php if (sizeof($codes) > 0) : ?>
                <?php foreach ($codes as $code) : ?>
                    <div id="<?= $code->codigo ?>" class="col mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <p class="card-text float-left id"><?= $code->id ?></p>
                                <p class="card-text float-right"><span class="badge badge-info code"><?= $code->codigo ?></span></p>
                            </div>
                            <div class="card-body">
                                <p class="card-text description"><?= $code->descripcion ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <button class="btn btn-primary edit" data-code=<?= $code->codigo ?>><?= $lang->translation("Editar") ?></button>
                                <button class="btn btn-danger del" data-code=<?= $code->codigo ?>><?= $lang->translation("Eliminar") ?></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>


    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>