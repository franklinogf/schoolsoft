<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use App\Models\School;

Session::is_logged();
$school = School::find(Session::id());

$user = $school->usuario;
$date = Util::date();
$ip = Util::getIp();

DB::table("entradas")->insert([
    'id' => $school->id,
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
    $title = __("Información");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <div class="container-lg mt-lg-3  px-0">
        <div class="text-center">
            <img class="img-fluid my-4" width="400px" src="<?= __DEFAULT_LOGO_SCHOOLSOFT ?>" />
        </div>
        <div class="jumbotron pt-4 shadow">
            <h1 class="text-center"><?= __("Bienvenido") ?></h1>
            <hr class="my-4" />
            <div class="row">
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Nombre") ?></span>
                </div>
                <div class="col-6"><?= $user ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Grupo") ?></span>
                </div>
                <div class="col-6"><?= $school->info("grupo") ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Ultima entrada") ?></span>
                </div>
                <div class="col-6"><?= $school->info("ufecha") ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("IP") ?></span>
                </div>
                <div class="col-6"><?= $ip ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Hora") ?></span>
                </div>
                <div class="col-6"><?= Util::time(true) ?></div>
            </div>
            <hr class="my-4" />
            <a class="btn btn-primary btn-block mt-5 mx-auto" href="<?= Route::url('/admin/home.php') ?>"><?= __("Continuar") ?></a>
        </div>
    </div>
    <?php

    $school->update([
        'ufecha' => $date,
    ]);

    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>