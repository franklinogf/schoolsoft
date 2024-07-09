<?php
global $jqUI;
global $DataTable;
global $jqMask;

use Classes\Route;
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php if ($jqUI) Route::jqUI(); ?>
<?php if ($DataTable) Route::includeFile('/includes/datatable-js.php', true); ?>
<?php if ($jqMask) : ?>
  <script src="/js/jquery.mask.min.js"></script>
<?php endif ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script type="text/javascript">
  const __LANG = '<?= __LANG ?>';
  const __SCHOOL_ACRONYM = '<?= __SCHOOL_ACRONYM ?>';
</script>
<?php
Route::js('/js/variables.js');
Route::js('/js/app.js', true);

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__jsFile = str_replace('.php', '', $__file) . '.js';
// $__path = __SUB_ROOT_URL.'/js/' . $__jsFile;

$root = str_replace(__ROOT_SCHOOL, '', str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['SCRIPT_FILENAME']));
$__path = dirname($root) . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $__jsFile;
if (Route::file_exists($__path)) {
  Route::js($__path);
}
