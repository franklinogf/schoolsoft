<?php

use App\Models\Admin;
use Classes\Route;
use Classes\Session;
use Classes\Services\SccsCompiler;

include '../../../app.php';

Session::is_logged();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $admin = Admin::primaryAdmin()->first();

    $colors = $theme['colors'];
    $booleans = $theme['booleans'];

    $compiler = new SccsCompiler;
    $compiler->useDefault();

    $admin->update([
        'theme' => null,
    ]);

    Session::set('theme', __('Tema actualizado correctamente'));

    Route::redirect('/information');
} else {
    Route::error();
}
