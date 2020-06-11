<?php

use Classes\Controllers\School;
use Classes\Route;

$file = basename($_SERVER['SCRIPT_FILENAME']);
$pathFile = str_replace('.php', '', $file);
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/regiweb/home.php') ?>">
      <img class="img-fluid" src="<?= __DEFAULT_LOGO_REGIWEB ?>" alt="Logo" width="<?= __LOGO_SIZE ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">      
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/regiweb/classes.php') ?>">Cursos</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'options' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/regiweb/options.php') ?>">Opciones</a>
      </li> 
      <li class="nav-item <?= ($pathFile === 'reports' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/regiweb/reports.php') ?>">Informes</a>
      </li> 
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/regiweb/profile.php') ?>"><i class="far fa-id-card text-primary"></i> Mi Perfil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/regiweb/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> Cerrar Sesión</a>
      </li>
    </ul>

  </div>
</nav>