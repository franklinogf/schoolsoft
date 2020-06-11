<?php
require_once '../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Información";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <div class="container-lg mt-lg-3  px-0">
        <div class="text-center">
            <img class="img-fluid my-4" width="400px" src="<?= Route::url('/images/logo-regiweb.gif', false, true) ?>" />
        </div>
        <div class="jumbotron pt-4 shadow">
            <h1 class="text-center">Bienvenido al sistema</h1>
            <hr class="my-4" />
            <div class="row">
                <div class="col-6 text-right">
                    <span class="badge badge-info">Nombre:</span>
                </div>
                <div class="col-6"><?= $teacher->fullName() ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info">ID:</span>
                </div>
                <div class="col-6"><?= $teacher->id ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info">Grupo:</span>
                </div>
                <div class="col-6"><?= $teacher->grupo ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info">Ultima entrada:</span>
                </div>
                <div class="col-6"><?= $teacher->ufecha ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info">Ip:</span>
                </div>
                <div class="col-6"><?= Server::get('REMOTE_ADDR') ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info">Hora:</span>
                </div>
                <div class="col-6"><?= Util::time(true) ?></div>
            </div>
            <hr class="my-4" />
            <button class="btn btn-primary btn-block mt-5 mx-auto">Continuar</button>
        </div>
    </div>
    <?php
    $teacher->ufecha = Util::date();
    $teacher->save();
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::js('/react-components/Clock.js', true);
    ?>

</body>

</html>