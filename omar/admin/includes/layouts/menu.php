<?php

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;


$pathFile = Route::pathFolder();

/* ------------------------------- Transaltion ------------------------------ */
$lang = new Lang([
  ['Usuarios', 'Users'],
  ['Acceso', 'Access'],
  ['Cuentas por cobrar', 'Billing statement'],
  ['Mensajes', 'Messages'],
  ['Información', 'Information'],
  ['Cerrar Sesión', 'Sign off'],
]);

?>
<nav class="navbar navbar-expand-xl navbar-dark bg-gradient-secondary bg-secondary">
  <span class="navbar-brand mr-5">
    <a href="<?= Route::url('/admin/home.php') ?>">
      <img class="img-fluid" src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE ?>">
    </a>
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse " id="navbarNavDropdown">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?= ($pathFile === 'users' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/admin/users/') ?>"><?= $lang->translation('Usuarios') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'access' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/admin/access') ?>"><?= $lang->translation('Acceso') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'accounts' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/admin/accounts/') ?>"><?= $lang->translation('Cuentas por cobrar') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'messages' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/admin/messages/') ?>"><?= $lang->translation('Mensajes') ?></a>
      </li>
      <li class="nav-item <?= ($pathFile === 'information' ? 'active' : '') ?>">
        <a class="nav-link" href="<?= Route::url('/admin/information/') ?>"><?= $lang->translation('Información') ?></a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <span class="navbar-text text-white">
        <i class="fas fa-user text-primary"></i>
          <?= Session::id(); ?>
        </span>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= Route::url('/admin/includes/logout.php') ?>"><i class="fas fa-sign-out-alt text-primary"></i> <?= $lang->translation('Cerrar sesión') ?></a>
      </li>
    </ul>

  </div>
</nav>