<?php

use Classes\Controllers\School;
use Classes\Route;


echo '<script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>';

echo '<link rel="icon" href="' . School::logo() . '" />';


Route::css("/css/main-bootstrap.css");
Route::css("/css/main.css", true);


$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__cssFile = str_replace('.php', '', $__file) . '.css';

$__path = '/foro/profesor/css/' . $__cssFile;

if (Route::file_exists($__path)) {

    Route::css($__path);
}
