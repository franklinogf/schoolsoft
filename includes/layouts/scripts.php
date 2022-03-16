<?php
global $jqUI;
global $DataTable;

use Classes\Route;
?>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>

<?php if ($jqUI) Route::jqUI(); ?>
<?php if ($DataTable) Route::includeFile('/includes/datatable-js.php', true); ?>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php
Route::js('/js/app.js', true);

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__jsFile = str_replace('.php', '', $__file) . '.js';
// $__path = __SUB_ROOT_URL.'/js/' . $__jsFile;

$root = str_replace(__ROOT_SCHOOL, '', str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']));
$__path = dirname($root) . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $__jsFile;
if (Route::file_exists($__path)) {
   Route::js($__path);
}
