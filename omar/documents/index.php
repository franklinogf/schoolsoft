<?php
require_once '../app.php';

use Classes\Route;
use Classes\DataBase\DB;
use Classes\Controllers\School;

$school = new School();
$date = date("Y-m-d");
$documents = DB::table('T_ing')
    ->whereRaw("fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='E' or fecha_desde <= '$date' and fecha_hasta >= '$date' and categoria='T'")->get()

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
<?php
    $title = "Lita de documentos";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <header class="jumbotron text-center">
        <div class="container">
            <h1><?= $school->info('colegio') ?></h1>
            <img class="img-fluid" src="<?= School::logo() ?>" alt="School Logo" width='<?= __HOME_LOGO_SIZE ?>'>
        </div>
    </header>
    <section class="container">
        <h2 class="text-center">Lista de documentos para descargar</h2>

        <?php if ($documents) : ?>
            <div class="row row-cols-2 mt-5">
                <?php foreach ($documents as $document) : ?>
                    <div class="col mb-4">
                        <div class="card border-primary">
                            <h5 class="card-header"><?= utf8_decode($document->titulo) ?></h5>
                            <div class="card-body">
                                <p class="card-text"><?= utf8_decode($document->descripcion) ?></p>
                                <p class="card-text d-flex justify-content-around text-info">
                                    <span>Grado inicial: <?= $document->grado_desde ?></span>
                                    <span>Grado final: <?= $document->grado_hasta ?></span>
                                </p>
                                <a href="<?= Route::url("/admin/ing/files/$document->archivo") ?>" class="btn btn-sm btn-primary btn-block" title="Descargar archivo">Descargar</a>
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
            <h1 class="text-center text-warning">No hay documentos para descargar</h1>
        <?php endif ?>
        <div class="text-center">
            <a href="<?= Route::url('/') ?>" class="btn btn-secondary mx-auto my-5">Volver al menú principal</a>

        </div>
    </section>

</body>

</html>