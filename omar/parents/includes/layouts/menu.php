<?php

use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;



$pathFile = Route::pathFolder();

$lang = new Lang([
  ['Notas', 'Grades'],
  ['Asistencia', 'Attendance'],
  ['Opciones', 'Options'],
  ['Disciplina', 'Discipline'],
  ['Mi Perfil', 'My profile'],
  ['Cerrar Sesión', 'Sign off'],
]);

?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/regiweb/home.php') ?>">
      <img class="img-fluid" src="<?= school_logo() ?>" alt="Logo" width="<?= school_config('logo.size.menu') ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?= ($pathFile === 'grades' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/grades') ?>"><?= $lang->translation('Notas') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'discipline' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/discipline') ?>"><?= $lang->translation('Disciplina') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'attendance' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/attendance/') ?>"><?= $lang->translation('Asistencia') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'options' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/parents/options/') ?>"><?= $lang->translation('Opciones') ?></a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/parents/profile.php') ?>"><i class="far fa-id-card text-primary"></i> <?= $lang->translation('Mi Perfil') ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/parents/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> <?= $lang->translation('Cerrar Sesión') ?></a>
      </li>
    </ul>

  </div>
</nav>