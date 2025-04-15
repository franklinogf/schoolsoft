<?php
require_once '../../../app.php';

use App\Enums\Status;
use App\Models\Admin;
use Classes\Route;
use Classes\Session;


Session::is_logged();
$admins = Admin::all();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = __("Usuarios administradores");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body class='pb-5'>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= __("Usuarios administradores") ?></h1>

        <?php if (Session::get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Session::get('error', true) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>


        <?php if (Session::get('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Session::get('success', true) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <a href="<?= Route::url("/admin/users/administration/create.php") ?>" class="btn btn-primary btn-sm my-2"><?= __("Añadir") ?></a>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th><?= __("Usuario") ?></th>
                        <th><?= __("Nombre") ?></th>
                        <th><?= __("Activo") ?></th>
                        <th style="width: 0;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td scope="row"><?= $admin->usuario ?></td>
                            <td><?= $admin->director ?></td>
                            <td class="text-center">
                                <span class="badge badge-pill <?= $admin->activo === Status::ACTIVE->value ? 'badge-primary' : 'badge-secondary' ?>"><?= Status::tryFrom($admin->activo)?->label() ?></span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <form class="mr-2" method="POST" action="<?= Route::url('/admin/users/administration/includes/active.php') ?>">
                                        <input type="hidden" name="id" value="<?= $admin->id ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary"><?= $admin->activo === Status::ACTIVE->value ? __('Desactivar') : __('Activar') ?></button>
                                    </form>
                                    <a href="<?= Route::url("/admin/users/administration/edit.php?id=$admin->id") ?>" class="btn btn-primary btn-sm mr-2"><?= __("Editar") ?></a>
                                    <form method="POST" action="<?= Route::url("/admin/users/administration/includes/delete.php") ?>">
                                        <input type="hidden" name="id" value="<?= $admin->id ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><?= __("Eliminar") ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>


    <?php Route::includeFile('/includes/layouts/scripts.php', true) ?>

</body>

</html>