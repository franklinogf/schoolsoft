<?php
use Classes\Controllers\School;
use Classes\Route;


$file = basename($_SERVER['SCRIPT_FILENAME']);

$pathFile = str_replace('.php', '', $file);

global $teacher;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/profesor/index.php') ?>"><img src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE_W ?>" height="<?= __LOGO_SIZE_H ?>"></a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav  mx-auto ">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/index.php') ?>"><i class="fas fa-house-user text-secondary"></i> Inicio</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/classes.php') ?>">Mis Cursos</a>
      </li>
      <li class="nav-item <?= ($pathFile === 'home' ? 'active' : '') ?>">
        <a class="nav-link text-primary" href="<?= Route::url('/foro/profesor/home.php') ?>">Salon Hogar</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Informes
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item  <?= ($pathFile === 'listClasses' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/listClasses.php') ?>"><i class="far fa-list-alt text-secondary"></i> Lista de estudiantes por curso</a>
          <a class="dropdown-item" <?= ($pathFile === 'homeworksList' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/homeworksList.php') ?>"><i class="far fa-list-alt text-secondary"></i> Lista de tareas entregadas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfHomeStudents.php') ?>"><i class="far fa-file-pdf text-secondary"></i> Salon Hogar</a>
          <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfUsersList.php') ?>"><i class="far fa-file-pdf text-secondary"></i></i> Lista de Usuarios</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownForo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Foro
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownForo">
          <a class="dropdown-item <?= ($pathFile === 'topics' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/topics.php') ?>"><i class="far fa-comment text-secondary"></i> Temas</a>
          <a class="dropdown-item <?= ($pathFile === 'homeworks' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/homeworks.php') ?>" href="#"><i class="fas fa-book-open text-secondary"></i> Tareas</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Tareas Recibidas</a>          
        </div>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item dropdown text-right">
        <a class="nav-link text-primary dropdown-toggle" href="#" id="dropDownAccount" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= isset($teacher) ? $teacher->nombre : 'username?'  ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropDownAccount">
          <a class="dropdown-item <?= ($pathFile === 'profile' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/profile.php') ?>"><i class="far fa-id-card text-secondary"></i> Mi Perfil</a>
          <a class="dropdown-item  <?= ($pathFile === 'inbox' ? 'active' : '') ?>" href="<?= Route::url('/foro/profesor/inbox.php') ?>"><i class="far fa-envelope text-secondary"></i> Mensajes <span class="badge badge-pill badge-info unreadMessages"><?= $teacher->unreadMessages() ?></span></a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= Route::url('/foro/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-secondary"></i> Cerrar Sesión</a>
        </div>
      </li>
    </ul>

  </div>
</nav>