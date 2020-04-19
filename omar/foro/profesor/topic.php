<?php

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;

require_once '../../app.php';

$topic = new Topic($_GET['id']);
$teacher = new Teacher($topic->creador_id);
$student = new Student();

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro - Inicio</title>
  <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-5 px-0">
    <div class="card">
      <h4 class="card-header text-center bg-primary rounded-0">
        <?= $topic->titulo ?>
      </h4>
      <div class="card-body">
        <h5 class="card-title"><?= $teacher->fullName(); ?></h5>
        <p class="card-text"><?= $topic->descripcion ?></p>
      </div>
      <div class="card-footer text-white d-flex justify-content-between bg-secondary">
        <span><?= Util::formatDate($topic->fecha, true, true) ?></span>
        <span><?= Util::formatTime($topic->hora) ?></span>
      </div>
    </div>

    <div class="container mt-3 pb-5">
      <form action="<?= Route::url('/foro/profesor/includes/comments.php') ?>">
        <div class="form-group">
          <label for="comment">Comentario nuevo</label>
          <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
        <button class="btn btn-primary" type="button">Comentar</button>
      </form>
      </textarea>
      <?php foreach ($topic->comments() as $comment) : ?>
        <div class="media mt-3">
          <img src="https://via.placeholder.com/64" class="mr-3" alt="student profile picture" width="64" height="64">
          <div class="media-body">
            <h5 class="mt-0"><?= ($comment->tipo === 'p' ? '<i class="fas fa-user-tie fa-sm"></i> ' . $teacher->fullName() : '<i class="fas fa-user-graduate fa-sm"></i> ' . $student->find($comment->creador_id)->fullName()) ?></h5>
            <p><?= $comment->descripcion ?></p>
            <small class="text-muted"><?= Util::formatDate($comment->fecha, true, true) . ' ' . Util::formatTime($comment->hora) ?></small>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  Route::js('/react-components/Clock.js', true);
  ?>

</body>

</html>