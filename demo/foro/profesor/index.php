<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$lastTopic = $teacher->lastTopic();
$lang = new Lang([
  ['Inicio', 'Home'],
  ['Mensajes', 'Inbox'],
  ['Ultimo tema comentado', 'Last topic commented'],
  ['El ultimo comentario se ha hecho en el curso', 'The last comment was made on the class'],
  ['Ir al tema', 'Go to the topic'],
  ['No tiene comentarios nuevos en los temas de conversación', 'You have no new comments on the topics'],
  ['Bienvenido','Welcome back'],
  ['Bienvenida','Welcome back'],

]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation('Inicio');
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-5  px-0">
    <div class="jumbotron pt-4">
      <!-- clock and messages -->
      <div class="d-flex justify-content-between mb-3">
        <div>
          <a class="btn btn-secondary" href="<?= Route::url('/foro/profesor/inbox.php') ?>">
            <i class="far fa-envelope text-primary"></i> <?= $lang->translation("Mensajes") ?> <span class="badge badge-pill badge-info unreadMessages"><?= $teacher->unreadMessages() ?></span>
          </a>
        </div>
        <div>
          <i class="far fa-clock d-inline-block text-secondary"></i>
          <div class="react-clock d-inline-block"></div>
        </div>
      </div>

      <h2><?= $teacher->genero === 'Femenino' ? $lang->translation("Bienvenida") : $lang->translation("Bienvenido") ?> <?= $teacher->fullName(); ?></h2>
      <?php if ($lastTopic) : ?>
        <div class="card mx-auto mt-5">
          <h4 class="card-header bg-gradient-info bg-info">
            <?= $lang->translation("Ultimo tema comentado") ?>
          </h4>
          <div class="card-body border-info">
            <h5 class="card-title"><?= $lastTopic->titulo ?></h5>
            <p class="card-text text-monospace"><?= $lang->translation("El ultimo comentario se ha hecho en el curso") ?> <?= "{$lastTopic->curso} ({$lastTopic->desc1})" ?> </p>
            <a class="btn btn-primary" href="viewTopic.php?id=<?= $lastTopic->id ?>"><?= $lang->translation("Ir al tema") ?></a>
          </div>
          <div class="card-footer text-muted d-flex justify-content-between">
            <span><?= Util::formatDate($lastTopic->fecha, true, true) ?></span>
            <span><?= Util::formatTime($lastTopic->hora) ?></span>
          </div>
        </div>
      <?php else : ?>
        <div class="alert alert-info mt-5 mb-0" role="alert">
          <?= $lang->translation("No tiene comentarios nuevos en los temas de conversación") ?>
        </div>

      <?php endif ?>
    </div>
  </div>
  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  Route::js('/react-components/Clock.js', true);
  ?>

</body>

</html>