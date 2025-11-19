<?php
require_once __DIR__ . '/../app.php';

use App\Models\Family;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$parents = Family::find(Session::id());
$lang = new Lang([
    ["Información", "Information"],
    ["Bienvenido", "Welcome"],
    ["Nombre:", "Name:"],
    ["ID:", "ID:"],
    ["Grupo:", "Group:"],
    ["Ultima Entrada:", "Last entry:"],
    ["IP:", "IP:"],
    ["Hora:", "Time:"],
]);
$date =  Util::date();
$ip = Util::getIp();

Manager::table("entradas")->insert([
    'id' => $parents->id,
    'usuario' => $parents->usuario,
    'fecha' => $date,
    'hora' =>  Util::time(),
    'ip' => $ip,
    'nombre' => $parents->madre,
    'apellidos' => $parents->padre
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
                <div class="col-6"><?= $ip ?></div>
                <div class="col-6 text-right">
                    <span class="badge badge-info"><?= $lang->translation("Hora:") ?></span>
                </div>
                <div class="col-6"><?= Util::time(true) ?></div>
            </div>
            <hr class="my-4" />
            <?php if ($parents->activo != 'Activo' && $parents->info('inactivo') == '1') : ?>
                <div class="alert alert-danger" role="alert">
                    <?= nl2br($parents->info('men_inac')) ?>
                </div>
            <?php else : ?>
                <a class="btn btn-primary btn-block mt-5 mx-auto" href="<?= Route::url('/parents/home.php') ?>"><?= $lang->translation("Continuar") ?></a>
            <?php endif ?>
        </div>
    </div>
    <?php
    $parents->ufecha = $date;
    $parents->save();
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>