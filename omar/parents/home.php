<?php
require_once '../app.php';

use Classes\Route;
use Classes\Session;

Session::is_logged();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Inicio";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3  px-0">
        <h1 class="display-4 mt-5">Conectate desde cualquier parte del Mundo.</h1>
        <img class="img-fluid mx-auto d-block mt-5 mt-lg-4 w-25" src="/images/globe.gif" />
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    // Route::js('/react-components/Clock.js', true);
    ?>

</body>

</html>