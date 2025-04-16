<?php
require '../../../../app.php';

use App\Models\Admin;
use Classes\Route;
use Classes\Session;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin = Admin::find($_POST['id']);

    $admin->syncPermissions($_POST['permissions'] ?? []);

    Session::set('success', __("Los permisos del usuario han sido actualizados"));
    Route::redirect("/users/administration/edit.php?id={$admin->id}");
} else {
    Session::set('error', __("Método no permitido"));
    Route::redirect('/users/administration/index.php');
}
