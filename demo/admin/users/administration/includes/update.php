<?php
require '../../../../app.php';

use App\Models\Admin;
use Classes\Route;
use Classes\Session;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = Admin::find($_POST['id']);
    $admin->update([
        'director' => $_POST['director'],
        'correo' => $_POST['correo'],
        'telefono' => $_POST['telefono'],
        'usuario' => $_POST['usuario'],
        'clave' => $_POST['clave'],
        'activo' => $_POST['activo'],
        'idioma' => $_POST['idioma'],
    ]);
    Session::set('success', __("El usuario ha sido actualizado"));
    Route::redirect('/users/administration/index.php');
} else {
    Session::set('error', __("Método no permitido"));
    Route::redirect('/users/administration/index.php');
}
