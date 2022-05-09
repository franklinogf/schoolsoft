<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$_info = [
    "Notas" => 'Notas',
    "Pruebas-Cortas" => 'Pruebas cortas',
    "Trab-Diarios" => 'Trabajos diarios',
    "Trab-Diarios2" => 'Trabajos diarios 2',
    "Trab-Libreta" => 'Trabajos de libreta',
    "Trab-Libreta2" => 'Trabajos de libreta 2',
];
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Listado de 100";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3">Opciones para el Listado de 100</h1>
        <div class="bg-white shadow-lg p-3 rounded">
            <form action="<?= Route::url('/regiweb/options/reports/pdf/100.php') ?>" method="POST" target="_blank">
                <h5 class="my-4 text-center">Paginas</h5>
                <div class="row">
                    <?php foreach ($_info as $index => $info) : ?>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label for="<?= "page$index" ?>"><?= $info ?></label>
                            <input type="checkbox" name="pages[]" id="<?= "page$index" ?>" class="custom-input align-middle" value="<?= $index ?>">
                        </div>
                    <?php endforeach ?>
                </div>
                <h5 class="my-4 text-center">Trimestres</h5>
                <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++) : ?>
                        <div class="col-6 col-md-3">
                            <label for="<?= "tri$i" ?>">Trimestre <?= $i ?></label>
                            <input type="checkbox" name="trimesters[]" id="<?= "tri$i" ?>" class="custom-input align-middle" value="<?= "Trimestre-$i" ?>">
                        </div>
                    <?php endfor ?>
                </div>
                <div class="text-center">
                    <hr>
                    <input type="submit" value="Continuar" class="btn btn-primary mt-3">
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>