<?php
require '../../../../app.php';

use App\Models\Admin;
use Carbon\Carbon;
use Classes\Route;
use Classes\Session;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primaryAdmin = Admin::primaryAdmin();
    Admin::create([
        'director' => $_POST['director'],
        'correo' => $_POST['correo'],
        'telefono' => $_POST['telefono'],
        'usuario' => $_POST['usuario'],
        'clave' => $_POST['clave'],
        'activo' => $_POST['activo'],
        'idioma' => $_POST['idioma'],
        'grupo' => 'Administrador',
        'year2' => $primaryAdmin->year,
        'ufecha' => Carbon::now()->format('Y-m-d'),
    ]);

    Session::set('success', __("El usuario ha sido creado"));
    Route::redirect('/users/administration/index.php');
} else {
    Session::set('error', __("Método no permitido"));
    Route::redirect('/users/administration/index.php');
}
