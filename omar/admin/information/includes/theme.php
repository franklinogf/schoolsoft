<?php

use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;
use Classes\Services\SccsCompiler;

include '../../../app.php';

Session::is_logged();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $theme = $_POST['theme'] ?? null;
    $defaultTheme = include __ROOT . '/config/theme.php';

    if (empty($theme)) {
        Session::set('theme', 'Please fill in all fields.');
        Route::redirect('information/');
    }
    foreach ($defaultTheme['booleans'] as $key => $value) {
        $theme['booleans'][$key] = isset($theme['booleans'][$key]) ? true : false;
    }

    $compiler = new SccsCompiler;
    $compiled = $compiler->compile($theme);

    if ($compiled) {
        DB::table('colegio')->where('usuario', 'administrador')->update([
            'theme' => json_encode($theme),
        ]);
        Session::set('theme', 'Theme updated successfully.');
    } else {
        Session::set('theme', 'Failed to update theme.');
    }
    Route::redirect('/information');
} else {
    Route::error();
}
