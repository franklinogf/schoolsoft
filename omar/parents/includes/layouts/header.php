<?php

use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;

global $DataTable;
global $title;
$lang = new Lang([
    ["Padres", "Parents"],
]);
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<title><?= $lang->translation("Padres") ?> <?= $title ? "- {$title}" : '' ?></title>
<link rel="icon" href="<?= School::logo() ?>" />
<?php

if ($DataTable) Route::includeFile('/includes/datatable-css.php', true);

echo Route::bootstrapCSS();
Route::css("/css/main.css", true);

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__cssFile = str_replace('.php', '', $__file) . '.css';

$__path = '/regiweb/css/' . $__cssFile;

if (Route::file_exists($__path)) {

    Route::css($__path);
}
