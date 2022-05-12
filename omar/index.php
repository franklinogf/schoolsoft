<?php
require_once 'app.php';

use Classes\Controllers\School;
use Classes\Route;

$school = new School();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $school->info('colegio') ?></title>
    <link rel="icon" href="<?= School::logo() ?>" />
    <?php Route::css("/css/main-bootstrap.css") ?>
</head>

<body>
    <header class="bg-dark py-3">
        <div class="container px-md-5 d-flex justify-content-between align-items-center">
            <img class="img-fluid" src="<?= School::logo() ?>" alt="School Logo" width='<?= __HOME_LOGO_SIZE ?>'>
            <h1 class="display-4 text-white"><?= $school->info('colegio') ?></h1>
        </div>
    </header>
    <main class="bg-light">
        <div class="container d-flex align-items-center justify-content-center" style='height:15rem'>
            <div class="row row-cols-4 w-100">
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg">Administración</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/regiweb/login.php') ?>" class="btn btn-primary btn-block shadow-lg">Regiweb</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/parents/login.php') ?>" class="btn btn-primary btn-block shadow-lg">Padres</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/foro/login.php') ?>" class="btn btn-primary btn-block shadow-lg">Foro</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg">Calendario</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg">Solicitudes</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/documents/') ?>" class="btn btn-primary btn-block shadow-lg">Documentos</a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg">Cafeteria</a>
                </div>
            </div>
        </div>
    </main>
    <section class="py-5">
        <div class="container d-flex align-items-center" style='height:15rem'>
            <div class="row">
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text"><?= $school->info('men_ini') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text"><?= $school->info('men_nota') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="bg-dark py-5 position-relative">
        <div class="container" style='height:10rem'>
            <div class="row">
                <div class="<?= $school->info('dir2') !== '' ? 'col-sm-6' : 'col-sm-12' ?>">
                    <div class="card bg-transparent border-0 text-white">
                        <div class="card-body">
                            <p class="card-text"><?= $school->info('dir1') ?></p>
                            <p class="card-text"><?= $school->info('dir3') ?></p>
                            <p class="card-text"><?= $school->info('pueblo1') . ', ' . $school->info('esta1') . ' ' . $school->info('zip1') ?></p>
                            <p class="card-text">Teléfono: <?= $school->info('telefono') ?> / Fax: <?= $school->info('fax') ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($school->info('dir2') !== '') : ?>
                    <div class="col-sm-6">
                        <div class="card bg-transparent border-0 text-white">
                            <div class="card-body">
                                <p class="card-text"><?= $school->info('dir2') ?></p>
                                <p class="card-text"><?= $school->info('dir4') ?></p>
                                <p class="card-text"><?= $school->info('pueblo2') . ', ' . $school->info('esta2') . ' ' . $school->info('zip2') ?></p>
                                <p class="card-text">Teléfono: <?= $school->info('telefono') ?> / Fax: <?= $school->info('fax') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <img class="img-fluid position-absolute" src="<?= __DEFAULT_LOGO_SCHOOLSOFT ?>" alt="School Logo" style='width:15rem;top:50px;right:15px;'>
        <p class="text-monospace text-center text-white">Derechos reservados &copy; <?= date('Y') ?></p>
    </footer>

</body>

</html>