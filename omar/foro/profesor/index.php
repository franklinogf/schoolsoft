<?php
require_once '../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;

Session::is_logged();
$teacher = new Teacher(Session::id());
$lastTopic = $teacher->lastTopic();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - Inicio</title>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-5  px-0">
    <div class="jumbotron">
      <div class="text-right">
        <i class="far fa-clock d-inline-block text-secondary"></i>
        <div class="react-clock d-inline-block"></div>
      </div>
      <h2>Bienvenido <?= $teacher->fullName(); ?></h2>
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
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  Route::js('/react-components/Clock.js', true);
  ?>

</body>

</html>