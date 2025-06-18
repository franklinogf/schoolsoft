<?php
require_once '../app.php';

use App\Models\Teacher;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$teacher = Teacher::find(Session::id());

$date =  Util::date();
$teacher->update(['ufecha' => $date]);
$ip = Util::getIp();

Manager::table("entradas")->insert([
    'id' => $teacher->id,
    'usuario' => $teacher->usuario,
    'fecha' => $date,
    'hora' =>  Util::time(),
    'ip' => $ip,
    'nombre' => $teacher->nombre,
    'apellidos' => $teacher->apellidos
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Información");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <div class="container-lg mt-lg-3  px-0">
        <div class="text-center">
            <img class="img-fluid my-4" width="400px" src="<?= asset('/images/logo-regiweb.gif') ?>" />
        </div>
        <div class="jumbotron pt-4 shadow">
            <h1 class="text-center"><?= __("Bienvenido") ?></h1>
            <hr class="my-4" />
            <div class="row">
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Nombre") ?></span>
                </div>
                <div class="col-6"><?= $teacher->full_name ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("ID") ?></span>
                </div>
                <div class="col-6"><?= $teacher->id ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Grupo") ?></span>
                </div>
                <div class="col-6"><?= $teacher->grupo ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= __("Ultima entrada") ?></span>
                </div>
                <div class="col-6"><?= $teacher->ufecha ?></div>
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
            <a class="btn btn-primary btn-block mt-5 mx-auto" href="<?= Route::url('/regiweb/home.php') ?>"><?= __("Continuar") ?></a>
        </div>
    </div>
    <?php

    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>