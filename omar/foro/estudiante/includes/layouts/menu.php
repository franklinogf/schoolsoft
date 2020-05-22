<?php
use Classes\Controllers\School;
use Classes\Route;

$file = basename($_SERVER['SCRIPT_FILENAME']);
$pathFile = str_replace('.php', '', $file);
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/estudiante/index.php') ?>"><img src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE_W ?>" height="<?= __LOGO_SIZE_H ?>"></a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav  mr-auto ">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/index.php') ?>"><i class="fas fa-house-user text-primary"></i> Inicio</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/classes.php') ?>">Mis Cursos</a>
      </li>         
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Tareas
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item" href="<?= Route::url('/foro/estudiante/homeworks.php') ?>">Mis tareas</a>
          <a class="dropdown-item" href="<?= Route::url('/foro/estudiante/exams.php') ?>">Examenes</a>
        </div>
      </li> 
      <li class="nav-item <?= ($pathFile === 'topics' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/topics.php') ?>">Foro</a>
      </li>       
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/estudiante/profile.php') ?>"><i class="far fa-id-card text-primary"></i> Mi Perfil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> Cerrar Sesión</a>
      </li>
    </ul>

  </div>
</nav>