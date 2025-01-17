<?php
require_once 'app.php';

use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;

$school = new School();
$lang = new Lang([
    ['Administración', 'Administration'],
    ["Regiweb", 'Regiweb'],
    ["Padres", 'Parents'],
    ["Foro", 'Foro'],
    ["Calendario", 'Calendar'],
    ["Solicitudes", 'Requests'],
    ["Documentos", "Documents"],
    ["Cafetería", "Cafeteria"]
]);
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
    <?php Route::css("/css/main.css", true) ?>
    <?php Route::fontawasome(); ?>
</head>

<body>
    <header class="bg-dark py-3">
        <div class="px-md-2 d-flex justify-content-center align-items-center">
            <img class="img-fluid" src="<?= School::logo() ?>" alt="School Logo" width='<?= __HOME_LOGO_SIZE ?>'>
            <h1 class="display-4 text-white"><?= $school->info('colegio') ?></h1>
        </div>
    </header>
    <main class="bg-light">
        <div class="container d-flex align-items-center justify-content-center" style='height:15rem'>
            <div class="row row-cols-2 row-cols-md-4 w-100">
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/admin/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Administración") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/regiweb/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Regiweb") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/parents/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Padres") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/foro/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Foro") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/calendarix/calendar.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Calendario") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Solicitudes") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/documents/') ?>" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Documentos") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg"><?= $lang->translation("Cafetería") ?> ss</a>
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
                            <p class="card-text"><i class="fa-solid fa-phone"></i> <?= $school->info('telefono') ?></p>
                            <p class="card-text"><i class="fa-solid fa-fax"></i> <?= $school->info('fax') ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($school->info('dir2') !== ''): ?>
                    <div class="col-sm-6">
                        <div class="card bg-transparent border-0 text-white">
                            <div class="card-body">
                                <p class="card-text"><?= $school->info('dir2') ?></p>
                                <p class="card-text"><?= $school->info('dir4') ?></p>
                                <p class="card-text"><?= $school->info('pueblo2') . ', ' . $school->info('esta2') . ' ' . $school->info('zip2') ?></p>
                                <p class="card-text"><i class="fa-solid fa-phone"></i> <?= $school->info('telefono') ?></p>
                                <p class="card-text"><i class="fa-solid fa-fax"></i> <?= $school->info('fax') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <img class="img-fluid position-absolute" src="<?= __DEFAULT_LOGO_SCHOOLSOFT ?>" alt="School Logo" style='width:15rem;top:50px;right:15px;'>
        <p class="text-monospace text-center text-white mt-5 mb-0"><?= $lang->translation('Derechos reservados') ?> &copy; <?= date('Y') ?></p>
    </footer>

</body>

</html>