<?php
global $jqUI;
global $DataTable;

use Classes\Route;
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

<?php if ($jqUI) Route::jqUI(); ?>
<?php if ($DataTable) Route::includeFile('/includes/datatable-js.php', true); ?>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php
Route::js('/js/app.js');

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__jsFile = str_replace('.php', '', $__file) . '.js';
$__path = '/foro/estudiante/js/' . $__jsFile;


if (Route::file_exists($__path)) {

   Route::js($__path);
}
