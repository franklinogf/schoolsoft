<?php
require '../../../../app.php';

use App\Models\Admin;
use Classes\Route;
use Classes\Session;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = Admin::find($_POST['id']);
    $admin->delete();
    Session::set('success', __("El usuario ha sido eliminado"));
    Route::redirect('/users/administration/index.php');
} else {
    Session::set('error', __("Método no permitido"));
    Route::redirect('/users/administration/index.php');
}
