<?php
require_once '../../../app.php';

use App\Enums\AdminPermission;
use App\Enums\LanguageCode;
use App\Enums\Status;
use App\Models\Admin;

use Classes\Route;
use Classes\Session;

Session::is_logged();

$adminId = $_GET['id'] ?? null;

if ($adminId) {
    $admin = Admin::find($adminId);
    if (!$admin) {
        Session::set('error', __("El usuario no existe"));
        Route::redirect('/users/administration/index.php');
    }
} else {
    Session::set('error', __("ID de usuario no proporcionado"));
    Route::redirect('/users/administration/index.php');
}

$permissionsGroups = collect(AdminPermission::cases())
    ->groupBy(fn($permission) => str($permission->name)->before('_'))
    ->toArray();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Editar usuario administrador");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center mb-2"><?= __("Editar usuario administrador") ?></h1>
        <h2 class="text-center mb-4"><?= $admin->director ?></h2>

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

        <form method="POST" action="<?= Route::url('/admin/users/administration/includes/update.php') ?>">
            <input type="hidden" name="id" value="<?= $admin->id ?>">

            <div class="form-group">
                <label for="director"><?= __("Nombre") ?></label>
                <input type="text" class="form-control" id="director" name="director" value="<?= $admin->director ?>" required>
            </div>
            <div class="form-group">
                <label for="correo"><?= __("Email") ?></label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?= $admin->correo ?>" required>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="telefono"><?= __("Teléfono") ?></label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= $admin->telefono ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="usuario"><?= __("Usuario") ?></label>
                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $admin->usuario ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="clave"><?= __("Contraseña") ?></label>
                    <input type="text" class="form-control" id="clave" name="clave" value="<?= $admin->clave ?>" required>
                </div>
            </div>


            <div class="row">
                <div class="form-group col-md-6">
                    <label for="activo"><?= __("Activo") ?></label>
                    <select class="custom-select" id="activo" name="activo" required>
                        <?php foreach (Status::cases() as $status): ?>
                            <option value="<?= $status->value ?>" <?= $admin->activo === $status->value ? 'selected' : '' ?>><?= $status->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="idioma"><?= __("Idioma") ?></label>
                    <select class="custom-select" id="idioma" name="idioma" required>
                        <?php foreach (LanguageCode::cases() as $lang): ?>
                            <option value="<?= $lang->value ?>" <?= $admin->idioma === $lang->value ? 'selected' : '' ?>><?= $lang->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?= __("Guardar") ?></button>
        </form>

        <form class="mt-5" method="POST" action="<?= Route::url('/admin/users/administration/includes/permission.php') ?>">
            <button type="submit" class="btn btn-primary mb-3 sticky-top" style="top:10px"><?= __("Guardar permisos") ?></button>
            <input type="hidden" name="id" value="<?= $admin->id ?>">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th><?= __("Permisos") ?></th>
                            <th class="text-center" style="width: 250px;">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="selectAllPermissions">
                                    <label class="custom-control-label" for="selectAllPermissions"><?= __("Seleccionar todo") ?></label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permissionsGroups as $group => $permissions): ?>
                            <tr>
                                <th scope="row" colspan="2">
                                    <?= $group ?>
                                </th>
                            </tr>
                            <?php foreach ($permissions as $permission): ?>
                                <tr>
                                    <td scope="row">
                                        <label class="d-flex flex-column custom-control-label" for="<?= $permission->value ?>">
                                            <span class="col-12"><?= $permission->label() ?></span>
                                            <small class="col-12"><?= $permission->name ?></small>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="custom-control custom-switch">
                                                <input <?= $admin->hasPermissionTo($permission->value) ? 'checked' : '' ?> name="permissions[]" value="<?= $permission->value ?>" type="checkbox" class="custom-control-input" id="<?= $permission->value ?>">
                                                <label class="custom-control-label" for="<?= $permission->value ?>"></label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>

    </div>


    <?php Route::includeFile('/includes/layouts/scripts.php', true) ?>
    <script>
        $(document).ready(function() {

            $("#selectAllPermissions").prop("checked", $("input[name='permissions[]']").length === $("input[name='permissions[]']:checked").length);
            $("input[name='permissions[]']").change(function() {
                $("#selectAllPermissions").prop("checked", $("input[name='permissions[]']").length === $("input[name='permissions[]']:checked").length);
            });

            $("#selectAllPermissions").change(function() {
                $("input[name='permissions[]']").prop("checked", this.checked);
            });
        });
    </script>

</body>

</html>