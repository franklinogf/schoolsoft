<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
Session::is_logged();
$school = new School();
$donwloads = DB::table('T_historial_descargas')
    ->where([
        ['id', Session::id()],
        ['year', $school->info('year')]
    ])->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Historial de descargas";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <section class="container mt-4">
        <h1 class="text-center">Historial de descargas</h1>

        <?php if ($donwloads) : ?>
            <div class="row row-cols-2 mt-5">
                <?php foreach ($donwloads as $donwload) : ?>
                    <div class="col mb-4">
                        <div class="card border-primary">
                            <h5 class="card-header"><?= $donwload->titulo ?></h5>
                            <div class="card-body">
                                <p class="card-text"><?= $donwload->ip ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <small class="text-muted">Fecha: <?= $donwload->fecha ?></small>
                                <small class="text-muted">Hora: <?= $donwload->hora ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <h2 class="text-center text-warning">No ha descargado ningun documento</h2>
        <?php endif ?>
        <div class="text-center">
            <a href="<?= Route::url('/parents/options/documents/') ?>" class="btn btn-secondary mx-auto my-5">Volver atrás</a>
        </div>
    </section>
    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>