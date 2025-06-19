<?php
require_once '../../../app.php';

use App\Enums\LanguageCode;
use App\Enums\Status;

use Classes\Route;
use Classes\Session;

Session::is_logged();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Añadir usuario administrador");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center mb-2"><?= __("Añadir usuario administrador") ?></h1>

        <a href="<?= Route::url('/admin/users/administration/index.php') ?>" class="btn btn-secondary mb-3"><?= __("Volver") ?></a>

        <?php if (Session::get('error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= Session::get('error', true) ?>
            </div>
        <?php endif; ?>
        <?php if (Session::get('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= Session::get('success', true) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= Route::url('/admin/users/administration/includes/store.php') ?>">

            <div class="form-group">
                <label for="director"><?= __("Nombre") ?></label>
                <input type="text" class="form-control" id="director" name="director" required>
            </div>
            <div class="form-group">
                <label for="correo"><?= __("Email") ?></label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="telefono"><?= __("Teléfono") ?></label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="usuario"><?= __("Nombre de usuario") ?></label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="clave"><?= __("Contraseña") ?></label>
                    <input type="text" class="form-control" id="clave" name="clave" required>
                </div>
            </div>


            <div class="row">
                <div class="form-group col-md-6">
                    <label for="activo"><?= __("Activo") ?></label>
                    <select class="custom-select" id="activo" name="activo" required>
                        <?php foreach (Status::cases() as $status): ?>
                            <option value="<?= $status->value ?>"><?= $status->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="idioma"><?= __("Idioma") ?></label>
                    <select class="custom-select" id="idioma" name="idioma" required>
                        <?php foreach (LanguageCode::cases() as $lang): ?>
                            <option value="<?= $lang->value ?>"><?= $lang->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?= __("Guardar") ?></button>
        </form>

    </div>


    <?php Route::includeFile('/includes/layouts/scripts.php', true) ?>

</body>

</html>