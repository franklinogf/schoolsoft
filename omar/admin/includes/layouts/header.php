<?php
use Classes\Route;
use Classes\Controllers\School;

global $DataTable;
global $title;
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title>Admin <?= $title ? "- {$title}":'' ?></title>
<link rel="icon" href="<?= School::logo() ?>" />
<?php

if ($DataTable) Route::includeFile('/includes/datatable-css.php', true);

Route::css("/css/main-bootstrap.css");
Route::css("/css/main.css", true);

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__cssFile = str_replace('.php', '', $__file) . '.css';
$root = str_replace(__ROOT_SCHOOL, '', str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']));
$__path = dirname($root) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $__cssFile;
// $__path = '/regiweb/css/' . $__cssFile;

if (Route::file_exists($__path)) {

    Route::css($__path);
}
