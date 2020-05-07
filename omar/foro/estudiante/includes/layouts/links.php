<?php
global $DataTable;

use Classes\Controllers\School;
use Classes\Route;

if ($DataTable) Route::includeFile('/includes/datatable-css.php', true);

echo '<link rel="icon" href="' . School::logo() . '" />';

Route::css("/css/main-bootstrap.css");
Route::css("/css/main.css", true);


$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__cssFile = str_replace('.php', '', $__file) . '.css';

$__path = '/foro/estudiante/css/' . $__cssFile;

if (Route::file_exists($__path)) {

    Route::css($__path);
}
