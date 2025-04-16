<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$school = new School();
$date = date("Y-m-d");
$documents = DB::table('T_ing')
    ->whereRaw("fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='M' or fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='T'")->get();

$lang = new Lang([
    ["Lista de documentos", "Documents list"],
    ['Lista de documentos para descargar', 'Documents list for download'],
    ['Grado inicial:', 'Initial grade:'],
    ['Grado final:', 'Final grade:'],
    ['Fecha inicial:', 'Initial date:'],
    ['Fecha final:', 'Final date:'],
    ['Descargar', 'Download'],
    ["No hay documentos para descargar", "No documents for download"],
    ["Historial de descargas", "Download history"]

]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Lita de documentos");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <header class="jumbotron text-center">
        <div class="container">
            <h1><?= $school->info('colegio') ?></h1>
            <img class="img-fluid" src="<?= school_logo() ?>" alt="School Logo" width='<?= school_config('app.logo.size.home') ?>'>
        </div>
    </header>
    <section class="container">
        <h1 class="text-center"><?= $lang->translation("Lista de documentos para descargar") ?></h1>

        <?php if ($documents) : ?>
            <div class="row row-cols-2 mt-5">
                <?php foreach ($documents as $document) : ?>
                    <div class="col mb-4">
                        <div class="card border-primary">
                            <h5 class="card-header"><?= $document->titulo ?></h5>
                            <div class="card-body">
                                <p class="card-text"><?= utf8_decode($document->descripcion) ?></p>
                                <p class="card-text d-flex justify-content-around text-info">
                                    <span><?= $lang->translation("Grado inicial:") ?> <?= $document->grado_desde ?></span>
                                    <span><?= $lang->translation("Grado final:") ?> <?= $document->grado_hasta ?></span>
                                </p>
                                </p>
                                <a data-id="<?= $document->id ?>" href="<?= Route::url("/admin/ing/files/$document->archivo") ?>" class="btn btn-sm btn-primary btn-block download" title="Descargar archivo"><?= $lang->translation("Descargar") ?></a>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <small class="text-muted"><?= $lang->translation("Fecha inicial:") ?> <?= $document->fecha_desde ?></small>
                                <small class="text-muted"><?= $lang->translation("Fecha final:") ?> <?= $document->fecha_hasta ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <h2 class="text-center text-warning"><?= $lang->translation("No hay documentos para descargar") ?></h2>
        <?php endif ?>
        <div class="text-center">
            <a href="<?= Route::url('/regiweb/options/documents/history.php') ?>" class="btn btn-secondary mx-auto my-5"><?= $lang->translation("Historial de descargas") ?></a>
        </div>
    </section>
    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>