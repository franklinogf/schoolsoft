<?php

use App\Models\Admin;
use Classes\Route;
use Classes\Session;
use Classes\Services\SccsCompiler;


include '../../../app.php';

Session::is_logged();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $admin = Admin::primaryAdmin();

    $theme = $_POST['theme'] ?? null;
    $defaultTheme = config('theme');

    if (empty($theme)) {
        Session::set('theme', __('El tema no puede estar vacío'));
        Route::redirect('information/');
    }
    foreach ($defaultTheme['booleans'] as $key => $value) {
        $theme['booleans'][$key] = isset($theme['booleans'][$key]) ? true : false;
    }

    $compiler = new SccsCompiler;
    $compiled = $compiler->compile($theme);

    if ($compiled) {
        $admin->update([
            'theme' => json_encode($theme),
        ]);
        Session::set('theme', __('Tema actualizado correctamente'));
    } else {
        Session::set('theme', __('Error al actualizar tema'));
    }
    Route::redirect('/information');
} else {
    Route::error();
}
