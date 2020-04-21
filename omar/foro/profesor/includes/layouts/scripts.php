<?php

use Classes\Route;
?>
<script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<?php

$__file = basename($_SERVER['SCRIPT_FILENAME']);

$__jsFile = str_replace('.php', '', $__file) . '.js';
$__path = '/foro/profesor/js/' . $__jsFile;


if (Route::file_exists($__path)) : ?>

   <?php Route::js($__path) ?>

<?php endif ?>