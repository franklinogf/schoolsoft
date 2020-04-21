<?php
use Classes\Controllers\School;
use Classes\Controllers\Teacher;
use Classes\Route;


$file = basename($_SERVER['SCRIPT_FILENAME']);

$pathFile = str_replace('.php', '', $file);

global $teacher;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/profesor/index.php') ?>"><img src="<?= School::logo() ?>" alt="Logo" width="72" height="72"></a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav  mr-auto ">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/index.php') ?>">Inicio <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'subjects' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/subjects.php') ?>">Mis Cursos</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'home' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/home.php') ?>">Salon Hogar</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Informes
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item" href="#">Lista Cursos</a>
          <a class="dropdown-item" href="#">Tareas Entregadas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" target="_blank" href="gradopdf.php">Salon Hogar</a>
          <a class="dropdown-item" target="_blank" href="usuariospdf.php">Lista de Usuarios</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownForo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Foro
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownForo">
          <a class="dropdown-item" href="#">Foro</a>
          <a class="dropdown-item" href="#">Crear Tareas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Tareas Recibidas</a>
          <a class="dropdown-item" href="#">Opciones</a>
        </div>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item dropdown text-right">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= isset($teacher) ? $teacher->nombre : 'username?'  ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropDownAccount">
          <a class="dropdown-item <?= ($pathFile === 'profile' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/profile.php') ?>">Mi Perfil</a>
          <a class="dropdown-item" href="#">Mensajes <span class="badge badge-pill badge-primary">0</span></a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= Route::url('/foro/includes/logout.php') ?>">Cerrar Sesión</a>
        </div>
      </li>
    </ul>

  </div>
</nav>