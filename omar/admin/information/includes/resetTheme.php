<?php

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Services\SccsCompiler;

include '../../../app.php';

Session::is_logged();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $colors = $theme['colors'];
    $booleans = $theme['booleans'];

    $compiler = new SccsCompiler;
    $compiler->useDefault();

    DB::table('colegio')->where('usuario', 'administrador')->update([
        'theme' => null,
    ]);

    Session::set('theme', 'Theme updated successfully.');

    Route::redirect('/information');
} else {
    Route::error();
}
