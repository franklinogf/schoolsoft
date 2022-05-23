<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
Session::is_logged();
$school = new School();
$date = date("Y-m-d");
$documents = DB::table('T_ing')
    ->whereRaw("fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='P' or fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='T'")->get()

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Lita de documentos";
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    
    <section class="container">
        <h1 class="text-center">Lista de documentos para descargar</h1>

        <?php if ($documents) : ?>
            <div class="row row-cols-2 mt-5">
                <?php foreach ($documents as $document) : ?>
                    <div class="col mb-4">
                        <div class="card border-primary">
                            <h5 class="card-header"><?= $document->titulo ?></h5>
                            <div class="card-body">
                                <p class="card-text"><?= utf8_decode($document->descripcion) ?></p>
                                <p class="card-text d-flex justify-content-around text-info">
                                    <span>Grado inicial: <?= $document->grado_desde ?></span>
                                    <span>Grado final: <?= $document->grado_hasta ?></span>
                                </p>
                                <a data-id="<?= $document->id ?>" href="<?= "admin/ing/files/$document->archivo" ?>" class="btn btn-sm btn-primary btn-block download" title="Descargar archivo">Descargar</a>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <small class="text-muted">Fecha inicial: <?= $document->fecha_desde ?></small>
                                <small class="text-muted">Fecha final: <?= $document->fecha_hasta ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <h2 class="text-center text-warning">No hay documentos para descargar</h2>
        <?php endif ?>
        <div class="text-center">
            <a href="<?= Route::url('/parents/options/documents/history.php') ?>" class="btn btn-secondary mx-auto my-5">Historial de descargas</a>
        </div>
    </section>
    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>