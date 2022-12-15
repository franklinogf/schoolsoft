<?php

use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;

$lang = new Lang([
  ['Inicio', 'Home'],
  ['Mis cursos', 'My classes'],
  ['Salón virtual', 'Virtual classroom'],  
  ['Mis tareas', 'My homeworks'],  
  ['Tareas', 'Homeworks'],  
  ['Mi perfil', 'My profile'],
  ['Cerrar sesión', 'Logout'],
  ['Foro', 'Forum'],
  ['Exámenes','Exams']


]);

$pathFile = Route::pathFolder();
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/estudiante/index.php') ?>">
      <img src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav  mr-auto ">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/index.php') ?>"><i class="fas fa-house-user text-primary"></i> <?= $lang->translation("Inicio") ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/classes.php') ?>"><?= $lang->translation("Mis Cursos") ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'virtual' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/virtual.php') ?>"><?= $lang->translation("Salón Virtual") ?></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= $lang->translation("Tareas") ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item" href="<?= Route::url('/foro/estudiante/homeworks.php') ?>"><?= $lang->translation("Mis tareas") ?></a>
          <a class="dropdown-item" href="<?= Route::url('/foro/estudiante/exams.php') ?>"><?= $lang->translation("Exámenes") ?></a>
        </div>
      </li>
      <li class="nav-item <?= ($pathFile === 'topics' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/topics.php') ?>"><?= $lang->translation("Foro") ?></a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/profile.php') ?>"><i class="far fa-id-card text-primary"></i> <?= $lang->translation("Mi Perfil") ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> <?= $lang->translation("Cerrar Sesión") ?></a>
      </li>
    </ul>

  </div>
</nav>