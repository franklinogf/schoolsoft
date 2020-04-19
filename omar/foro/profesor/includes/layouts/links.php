<?php 
use Classes\Route;

Route::css("/css/main-bootstrap.css");

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__cssFile = str_replace('.php','',$__file) . '.css';

$__path ='/foro/profesor/css/'.$__cssFile;

if (Route::file_exists($__path)) {
   
    Route::css($__path);
}


