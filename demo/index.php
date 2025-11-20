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
    <?php Route::css("/css/animate.css", true) ?>
    <?php Route::fontawasome(); ?>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero-header {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .hero-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0) 100%);
            pointer-events: none;
        }

        .hero-header img {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .hero-header img:hover {
            transform: scale(1.05);
        }

        .hero-header h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            font-weight: 700;
            margin-left: 1.5rem;
        }

        .portal-section {
            min-height: 450px;
            padding: 4rem 0;
            background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
        }

        .portal-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            /* overflow: hidden; */
            height: 100%;
            background: white;
        }

        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .portal-card .btn {
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .portal-card .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .portal-card .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .portal-card .btn i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .info-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #007bff;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .info-card .card-body {
            padding: 2rem;
        }

        .info-section {
            padding: 4rem 0;
            background: white;
        }

        .footer-enhanced {
            background: #343a40;
            position: relative;
        }

        .footer-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #007bff, #0056b3);
        }

        .footer-card {
            transition: all 0.3s ease;
        }

        .footer-card:hover {
            transform: translateX(5px);
        }

        .footer-card p {
            margin-bottom: 0.5rem;
            line-height: 1.8;
        }

        .footer-card i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
            color: #007bff;
        }

        .schoolsoft-logo {
            transition: all 0.3s ease;
            opacity: 0.9;
        }

        .schoolsoft-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stagger-animation>* {
            animation: fadeIn 0.6s ease-in both;
        }

        .stagger-animation>*:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stagger-animation>*:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stagger-animation>*:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stagger-animation>*:nth-child(4) {
            animation-delay: 0.4s;
        }

        .stagger-animation>*:nth-child(5) {
            animation-delay: 0.5s;
        }

        .stagger-animation>*:nth-child(6) {
            animation-delay: 0.6s;
        }

        .stagger-animation>*:nth-child(7) {
            animation-delay: 0.7s;
        }

        .stagger-animation>*:nth-child(8) {
            animation-delay: 0.8s;
        }

        @media (max-width: 768px) {
            .hero-header h1 {
                font-size: 2rem;
                margin-left: 0.5rem;
            }

            .portal-section {
                min-height: auto;
            }
        }
    </style>
</head>

<body>
    <header class="hero-header bg-dark py-4">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <img class="img-fluid animated fadeInDown" src="<?= school_logo() ?>" alt="School Logo" width='<?= school_config('app.logo.size.home') ?>'>
                <h1 class="display-4 text-white animated fadeInUp text-center"><?= $school->colegio ?></h1>
            </div>
        </div>
    </header>
    <main class="portal-section">
        <div class="container">
            <h2 class="text-center mb-4 fade-in"><?= __("Portales de Acceso") ?></h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 stagger-animation">
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/admin/login.php') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-user-shield"></i>
                                <?= __("Administración") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/regiweb/login.php') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-clipboard-list"></i>
                                <?= __("Regiweb") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/parents/login.php') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-users"></i>
                                <?= __("Padres") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/foro/login.php') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-comments"></i>
                                <?= __("Foro") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/calendarix/calendar.php') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-calendar-alt"></i>
                                <?= __("Calendario") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="#" class="btn btn-primary btn-block">
                                <i class="fas fa-file-alt"></i>
                                <?= __("Solicitudes") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <a href="<?= Route::url('/documents/') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-folder-open"></i>
                                <?= __("Documentos") ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col mb-4">
                    <div class="card portal-card shadow">
                        <div class="card-body text-center p-4">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-block dropdown-toggle" type="button" id="cafeteriaDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-utensils"></i>
                                    <?= __('Cafetería') ?>
                                </button>
                                <div class="dropdown-menu w-100" aria-labelledby="cafeteriaDropdown">
                                    <a class="dropdown-item" href="<?= Route::url('/cafeteria/login.php') ?>">
                                        <i class="fas fa-cash-register mr-2"></i><?= __('Caja registradora') ?>
                                    </a>
                                    <a class="dropdown-item" href="<?= Route::url('/cafeteria/self-service/') ?>">
                                        <i class="fas fa-shopping-cart mr-2"></i><?= __('Auto servicio') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <section class="info-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card info-card h-100 position-relative">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">
                                <i class="fas fa-info-circle mr-2"></i><?= __('Bienvenida') ?>
                            </h5>
                            <p class="card-text"><?= $school->men_ini ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card info-card h-100 position-relative">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">
                                <i class="fas fa-bell mr-2"></i><?= __('Anuncios') ?>
                            </h5>
                            <p class="card-text"><?= $school->men_nota ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer-enhanced py-5 position-relative">
        <div class="container">
            <div class="row mb-4">
                <div class="<?= $school->dir2 !== '' ? 'col-md-6' : 'col-md-8' ?> mb-4">
                    <div class="footer-card">
                        <h5 class="text-white mb-3">
                            <i class="fas fa-map-marker-alt mr-2"></i><?= __('Dirección Principal') ?>
                        </h5>
                        <div class="text-white-50">
                            <p><i class="fas fa-building"></i> <?= $school->dir1 ?></p>
                            <?php if ($school->dir3): ?>
                                <p><i class="fas fa-map"></i> <?= $school->dir3 ?></p>
                            <?php endif ?>
                            <p><i class="fas fa-location-dot"></i> <?= "$school->pueblo1, $school->esta1 $school->zip1" ?></p>
                            <p><i class="fas fa-phone"></i> <?= $school->telefono ?></p>
                            <?php if ($school->fax): ?>
                                <p><i class="fas fa-fax"></i> <?= $school->fax ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <?php if ($school->dir2 !== ''): ?>
                    <div class="col-md-6 mb-4">
                        <div class="footer-card">
                            <h5 class="text-white mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i><?= __('Dirección Secundaria') ?>
                            </h5>
                            <div class="text-white-50">
                                <p><i class="fas fa-building"></i> <?= $school->dir2 ?></p>
                                <?php if ($school->dir4): ?>
                                    <p><i class="fas fa-map"></i> <?= $school->dir4 ?></p>
                                <?php endif ?>
                                <p><i class="fas fa-location-dot"></i> <?= "$school->pueblo2, $school->esta2 $school->zip2" ?></p>
                                <p><i class="fas fa-phone"></i> <?= $school->telefono ?></p>
                                <?php if ($school->fax): ?>
                                    <p><i class="fas fa-fax"></i> <?= $school->fax ?></p>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <img class="schoolsoft-logo mb-3" src="<?= asset('images/logo-schoolsoft.gif') ?>" alt="SchoolSoft Logo" style="max-width: 200px;">
                    <p class="text-white-50 mb-0"><?= __('Derechos reservados') ?> &copy; <?= date('Y') ?> - Powered by SchoolSoft</p>
                </div>
            </div>
        </div>
    </footer>
    <?php Route::includeFile('/includes/layouts/scripts.php', true) ?>
</body>

</html>