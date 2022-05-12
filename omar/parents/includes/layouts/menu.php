<?php

use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;


$file = basename($_SERVER['SCRIPT_FILENAME']);
$pathFile = str_replace('.php', '', $file);

/* ------------------------------- Transaltion ------------------------------ */
$TRANS = [
  "es" => [
    "GRADES" => 'Notas',
    "ASSISTANCE" => 'Asistencia',
    "OPTIONS" => 'Opciones',
    "PROFILE" => 'Mi Perfil',
    "LOG_OUT" => 'Cerrar Sesión',
  ],
  "en" => [
    "GRADES" => 'Grades',
    "ASSISTANCE" => 'Asistencia',
    "OPTIONS" => 'Options',
    "PROFILE" => 'My profile',
    "LOG_OUT" => 'Log out',
  ]
];

Lang::addMenuTranslation($TRANS);
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/regiweb/home.php') ?>">
      <img class="img-fluid" src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">      
      <li class="nav-item <?= ($pathFile === 'grades' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/grades') ?>"><?= Lang::menuTranslation('GRADES') ?></a>
      </li> 
      <li class="nav-item <?= ($pathFile === 'assistance' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/assistance/') ?>"><?= Lang::menuTranslation('ASSISTANCE') ?></a>
      </li> 
      <li class="nav-item <?= ($pathFile === 'options' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/options/') ?>"><?= Lang::menuTranslation('OPTIONS') ?></a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/parents/profile.php') ?>"><i class="far fa-id-card text-primary"></i> <?= Lang::menuTranslation('PROFILE') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/parents/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> <?= Lang::menuTranslation('LOG_OUT') ?></a>
      </li>
    </ul>

  </div>
</nav>