<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$school = new School(Session::id());
$lang = new Lang([
    ["Información", "Information"],
    ["Bienvenido", "Welcome"],
    ["Nombre:", "Name:"],
    ["Grupo:", "Group:"],
    ["Ultima Entrada:", "Last entry:"],
    ["IP:", "IP:"],
    ["Hora:", "Time:"],
]);

$user = $school->info("usuario");
$date = Util::date();
$ip = Util::getIp();

DB::table("entradas")->insert([
    'id' => $school->info('id'),
    'usuario' => $user,
    'fecha' => $date,
    'hora' => Util::time(),
    'ip' => $ip,
    'nombre' => '',
    'apellidos' => '',
    'control' => '',
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Información");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <div class="container-lg mt-lg-3  px-0">
        <div class="text-center">
            <img class="img-fluid my-4" width="400px" src="<?= __DEFAULT_LOGO_SCHOOLSOFT ?>" />
        </div>
        <div class="jumbotron pt-4 shadow">
            <h1 class="text-center"><?= $lang->translation("Bienvenido") ?></h1>
            <hr class="my-4" />
            <div class="row">
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Nombre:") ?></span>
                </div>
                <div class="col-6"><?= $user ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Grupo:") ?></span>
                </div>
                <div class="col-6"><?= $school->info("grupo") ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Ultima entrada:") ?></span>
                </div>
                <div class="col-6"><?= $school->info("ufecha") ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("IP:") ?></span>
                </div>
                <div class="col-6"><?= $ip ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Hora:") ?></span>
                </div>
                <div class="col-6"><?= Util::time(true) ?></div>
            </div>
            <hr class="my-4" />
            <a class="btn btn-primary btn-block mt-5 mx-auto" href="<?= Route::url('/admin/home.php') ?>"><?= $lang->translation("Continuar") ?></a>
        </div>
    </div>
    <?php
    $school->set('ufecha', $date);
    $school->save();
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>