<?php
require_once '../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$student = new Student(Session::id());
$lastTopic = $student->lastTopic();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Inicio";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-5  px-0">
  <div class="jumbotron pt-4">
      <!-- clock and messages -->
      <div class="d-flex justify-content-between mb-3">
        <div>
          <a class="btn btn-secondary" href="<?= Route::url('/foro/estudiante/inbox.php') ?>">
            <i class="far fa-envelope text-primary"></i> Mensajes <span class="badge badge-pill badge-info unreadMessages"><?= $student->unreadMessages() ?></span>
          </a>
        </div>
        <div>
          <i class="far fa-clock d-inline-block text-secondary"></i>
          <div class="react-clock d-inline-block"></div>
        </div>
      </div>
      <h2><?= $student->genero === 'F' ? 'Bienvenida' : 'Bienvenido' ?> <?= $student->fullName(); ?></h2>
      <?php if ($lastTopic) : ?>
        <div class="card mx-auto mt-5">
          <h4 class="card-header bg-gradient-info bg-info">
            Ultimo tema comentado
          </h4>
          <div class="card-body border-info">
            <h5 class="card-title"><?= $lastTopic->titulo ?></h5>
            <p class="card-text text-monospace"> El ultimo comentario se ha hecho en el curso <?= "{$lastTopic->curso} ({$lastTopic->desc1})" ?> </p>
            <a class="btn btn-primary" href="viewTopic.php?id=<?= $lastTopic->id ?>">Ir al tema</a>
          </div>
          <div class="card-footer text-muted d-flex justify-content-between">
            <span><?= Util::formatDate($lastTopic->fecha, true, true) ?></span>
            <span><?= Util::formatTime($lastTopic->hora) ?></span>
          </div>
        </div>
      <?php else : ?>
        <div class="alert alert-info mt-5 mb-0" role="alert">
          No tiene comentarios nuevos en los temas de conversación
        </div>

      <?php endif ?>
    </div>
  </div>
  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
  Route::js('/react-components/Clock.js', true);
  ?>

</body>

</html>