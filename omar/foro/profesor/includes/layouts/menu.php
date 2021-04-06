<?php

use Classes\Controllers\School;
use Classes\Route;

$file = basename($_SERVER['SCRIPT_FILENAME']);
$pathFile = str_replace('.php', '', $file);
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/profesor/index.php') ?>">
      <img class="img-fluid" src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/index.php') ?>"><i class="fas fa-house-user text-primary"></i> Inicio</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/classes.php') ?>">Mis Cursos</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/virtual.php') ?>">Salón Virtual</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'home' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/home.php') ?>">Salon Hogar</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Informes
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/listClasses.php') ?>"><i class="far fa-list-alt text-primary"></i> Lista de estudiantes por curso</a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/homeworksList.php') ?>"><i class="far fa-list-alt text-primary"></i> Lista de tareas</a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/doneHomeworksList.php') ?>"><i class="far fa-list-alt text-primary"></i> Lista de tareas entregadas</a>
          <div class="dropdown-divider"></div>
          <?php if (!__COSEY) : ?>
            <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfHomeStudents.php') ?>"><i class="far fa-file-pdf text-primary"></i> Salon Hogar</a>
          <?php endif ?>
          <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfUsersList.php') ?>"><i class="far fa-file-pdf text-primary"></i></i> Lista de Usuarios</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownForo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Foro
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownForo">
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/topics.php') ?>"><i class="far fa-comment text-primary"></i> Temas</a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/homeworks.php') ?>"><i class="fas fa-book-open text-primary"></i> Tareas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/doneHomeworks.php') ?>" href="#">Tareas Recibidas</a>
        </div>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/profile.php') ?>"><i class="far fa-id-card text-primary"></i> Mi Perfil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> Cerrar Sesión</a>
      </li>
    </ul>

  </div>
</nav>