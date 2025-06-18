<?php

use Classes\Lang;
use Classes\Route;
use Classes\Controllers\School;

$lang = new Lang([
  ['Inicio', 'Home'],
  ['Mis cursos', 'My classes'],
  ['Salón virtual', 'Virtual classroom'],
  ['Salón hogar', 'Home classroom'],
  ['Informes', 'Reports'],
  ['Lista de estudiantes por curso', 'Student list per class'],
  ['Lista de tareas', 'Homeworks list'],
  ['Lista de tareas entregadas', 'List of done homeworks'],
  ['Lista de clases virtuales', 'Virtual classes list'],
  ['Lista de Usuarios', 'List of users'],
  ['Temas', 'Topics'],
  ['Tareas', 'Homeworks'],
  ['Tareas recibidas', 'Homeworks received'],
  ['Mi perfil', 'My profile'],
  ['Cerrar sesión', 'Logout'],
  ['Foro', 'Forum']


]);

$pathFile = Route::pathFolder();
?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/foro/profesor/index.php') ?>">
      <img class="img-fluid" src="<?= school_logo() ?>" alt="Logo" width="<?= school_config('app.logo.size.menu') ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item  <?= ($pathFile === 'index' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/index.php') ?>"><i class="fas fa-house-user text-primary"></i> <?= $lang->translation("Inicio") ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'classes' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/classes.php') ?>"><?= $lang->translation("Mis Cursos") ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'virtual' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/virtual.php') ?>"><?= $lang->translation("Salón Virtual") ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'home' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/home.php') ?>"><?= $lang->translation("Salón Hogar") ?></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= $lang->translation("Informes") ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownInformes">
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/reports/classesList.php') ?>"><i class="far fa-list-alt text-primary"></i> <?= $lang->translation("Lista de estudiantes por curso") ?></a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/reports/homeworksList.php') ?>"><i class="far fa-list-alt text-primary"></i> <?= $lang->translation("Lista de tareas") ?></a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/reports/doneHomeworksList.php') ?>"><i class="far fa-list-alt text-primary"></i> <?= $lang->translation("Lista de tareas entregadas") ?></a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/reports/virtualList.php') ?>"><i class="far fa-list-alt text-primary"></i> <?= $lang->translation("Lista de clases virtuales") ?></a>
          <div class="dropdown-divider"></div>
          <?php if (!__COSEY) : ?>
            <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfHomeStudents.php') ?>"><i class="far fa-file-pdf text-primary"></i> <?= $lang->translation("Salón Hogar") ?></a>
          <?php endif ?>
          <a class="dropdown-item" target="_blank" href="<?= Route::url('/foro/profesor/pdf/pdfUsersList.php') ?>"><i class="far fa-file-pdf text-primary"></i></i> <?= $lang->translation("Lista de Usuarios") ?></a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropDownForo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?= $lang->translation("Foro") ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="dropDownForo">
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/topics.php') ?>"><i class="far fa-comment text-primary"></i> <?= $lang->translation("Temas") ?></a>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/homeworks.php') ?>"><i class="fas fa-book-open text-primary"></i> <?= $lang->translation("Tareas") ?></a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?= Route::url('/foro/profesor/doneHomeworks.php') ?>" href="#"><?= $lang->translation("Tareas Recibidas") ?></a>
        </div>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/profesor/profile.php') ?>"><i class="far fa-id-card text-primary"></i> <?= $lang->translation("Mi Perfil") ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/foro/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> <?= $lang->translation("Cerrar Sesión") ?></a>
      </li>
    </ul>

  </div>
</nav>