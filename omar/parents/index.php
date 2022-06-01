<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Parents;

Session::is_logged();
$parents = new Parents(Session::id());
$lang = new Lang([
    ["Información","Information"],
    ["Bienvenido", "Welcome"],
    ["Nombre:", "Name:"],
    ["ID:", "ID:"],
    ["Grupo:", "Group:"],
    ["Ultima Entrada:", "Last entry:"],
    ["IP:", "IP:"],
    ["Hora:", "Time:"],
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Información");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <div class="container mt-3">
        <div class="text-center">
            <img class="img-fluid my-4" width="400px" src="<?= __DEFAULT_LOGO_REGIWEB ?>" />
        </div>
        <div class="jumbotron pt-4 shadow">
            <h1 class="text-center"><?= $lang->translation("Bienvenido") ?></h1>
            <hr class="my-4" />
            <div class="row">
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Nombre:") ?></span>
                </div>
                <div class="col-6"><?= "$parents->madre, $parents->padre" ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("ID:") ?></span>
                </div>
                <div class="col-6"><?= $parents->id ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Grupo:") ?></span>
                </div>
                <div class="col-6"><?= $parents->grupo ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Ultima entrada:") ?></span>
                </div>
                <div class="col-6"><?= $parents->ufecha ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Ip:") ?></span>
                </div>
                <div class="col-6"><?= Server::get('REMOTE_ADDR') ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Hora:") ?></span>
                </div>
                <div class="col-6"><?= Util::time(true) ?></div>
            </div>
            <hr class="my-4" />
            <a class="btn btn-primary btn-block mt-5 mx-auto" href="<?= Route::url('/parents/home.php') ?>"><?= $lang->translation("Continuar") ?></a>
        </div>
    </div>
    <?php
    $parents->ufecha = Util::date();
    $parents->save();
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>