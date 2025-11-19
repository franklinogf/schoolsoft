<?php
require_once __DIR__ . '/app.php';

use Classes\Route;
use App\Models\Admin;

$school = Admin::primaryAdmin();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $school->colegio ?></title>
    <link rel="icon" href="<?= school_logo() ?>" />
    <?= Route::bootstrapCSS() ?>
    <?php Route::css("/css/main.css", true) ?>
    <?php Route::fontawasome(); ?>
</head>

<body>
    <header class="bg-dark py-3">
        <div class="px-md-2 d-flex justify-content-center align-items-center">
            <img class="img-fluid" src="<?= school_logo() ?>" alt="School Logo" width='<?= school_config('app.logo.size.home') ?>'>
            <h1 class="display-4 text-white"><?= $school->colegio ?></h1>
        </div>
    </header>
    <main class="bg-light">
        <div class="container d-flex align-items-center justify-content-center" style='height:15rem'>
            <div class="row row-cols-2 row-cols-md-4 w-100">
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/admin/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Administración") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/regiweb/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Regiweb") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/parents/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Padres") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/foro/login.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Foro") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/calendarix/calendar.php') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Calendario") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="#" class="btn btn-primary btn-block shadow-lg"><?= __("Solicitudes") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <a href="<?= Route::url('/documents/') ?>" class="btn btn-primary btn-block shadow-lg"><?= __("Documentos") ?></a>
                </div>
                <div class="col mb-2 px-1">
                    <div class="dropdown dropdown-menu-lg-right">
                        <button class="btn btn-primary btn-block shadow-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            <?= __('Cafetería') ?>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= Route::url('/cafeteria/login.php') ?>"><?= __('Caja registradora') ?></a>
                            <a class="dropdown-item" href="<?= Route::url('/cafeteria/self-service/') ?>"><?= __('Auto servicio') ?></a>
                        </div>
                    </div>
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
                            <p class="card-text"><?= $school->men_ini ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text"><?= $school->men_nota ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="bg-dark py-5 position-relative">
        <div class="container" style='height:10rem'>
            <div class="row">
                <div class="<?= $school->dir2 !== '' ? 'col-sm-6' : 'col-sm-12' ?>">
                    <div class="card bg-transparent border-0 text-white">
                        <div class="card-body">
                            <p class="card-text"><?= $school->dir1 ?></p>
                            <p class="card-text"><?= $school->dir3 ?></p>
                            <p class="card-text"><?= "$school->pueblo1, $school->esta1 $school->zip1" ?></p>
                            <p class="card-text"><i class="fa-solid fa-phone"></i> <?= $school->telefono ?></p>
                            <p class="card-text"><i class="fa-solid fa-fax"></i> <?= $school->fax ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($school->dir2 !== ''): ?>
                    <div class="col-sm-6">
                        <div class="card bg-transparent border-0 text-white">
                            <div class="card-body">
                                <p class="card-text"><?= $school->dir2 ?></p>
                                <p class="card-text"><?= $school->dir4 ?></p>
                                <p class="card-text"><?= "$school->pueblo2, $school->esta2 $school->zip2" ?></p>
                                <p class="card-text"><i class="fa-solid fa-phone"></i> <?= $school->telefono ?></p>
                                <p class="card-text"><i class="fa-solid fa-fax"></i> <?= $school->fax ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <img class="img-fluid position-absolute" src="<?= asset('images/logo-schoolsoft.gif') ?>" alt="School Logo" style='width:15rem;top:50px;right:15px;'>
        <p class="text-monospace text-center text-white mt-5 mb-0"><?= __('Derechos reservados') ?> &copy; <?= date('Y') ?></p>
    </footer>
    <?= Route::includeFile('/includes/layouts/scripts.php', true) ?>
</body>

</html>