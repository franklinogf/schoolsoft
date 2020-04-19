<?php

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;
use Classes\Directories;

require_once '../../app.php';

if (!isset($_SESSION['logged'])) {
  Route::redirect('/foro');
}

$topic = new Topic($_GET['id']);
$teacher = new Teacher($topic->creador_id);
$student = new Student();

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro -  <?= $topic->titulo ?></title>
  <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-2 px-0">
    <div class="row mt-3">
      <div class="col-lg-4"><button type="button" id="editTopicBtn" class="btn btn-outline-primary btn-lg btn-block">Editar tema</button></div>
    </div>


    <div class="card mt-3">
      <h4 id="title" class="card-header text-center bg-primary rounded-0">
        <?= $topic->titulo ?>
      </h4>
      <div class="card-body">
        <h5 class="card-title"><?= $teacher->fullName(); ?></h5>
        <p id="description" class="card-text"><?= $topic->descripcion ?></p>
      </div>
      <div class="card-footer text-white d-flex justify-content-between bg-secondary">
        <span><?= Util::formatDate($topic->fecha, true, true) ?></span>
        <span><?= Util::formatTime($topic->hora) ?></span>
      </div>
    </div>

    <div class="container mt-3 pb-5">
      <form action="<?= Route::url('/foro/profesor/includes/comments.php') ?>" method="POST">
        <input type="hidden" name="id_topic" value="<?= $topic->id ?>">
        <div class="form-group">
          <label for="comment">Comentario nuevo</label>
          <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Comentar</button>
      </form>
      </textarea>
      
      <?php foreach ($topic->comments() as $comment) : ?>
        <div class="media bg-light mt-3 pt-3 px-3">
          <img src="<?= Directories::$noProfilePicture ?>" class="mr-3" alt="student profile picture" width="64" height="64">
          <div class="media-body">
            <h5 class="mt-0"><?= ($comment->tipo === 'p' ? '<i class="fas fa-user-tie fa-sm"></i> ' . $teacher->fullName() : '<i class="fas fa-user-graduate fa-sm"></i> ' . $student->find($comment->creador_id)->fullName()) ?></h5>
            <p><?= $comment->descripcion ?></p>
            <p class="text-muted text-right"><?= Util::formatDate($comment->fecha, true, true) . ' ' . Util::formatTime($comment->hora) ?></p>
          </div>
        </div>
      <?php endforeach ?>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title">Modificar tema</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form method="POST">
            <div class="modal-body">
              <input type="hidden" name="id_topic" id="id_topic" value="<?= $topic->id ?>">
              <div class="form-row">
                <div class="form-group col-12">
                  <label for="modalTitle">Titulo del tema:</label>
                  <input type="text" class="form-control" name='title' id="modalTitle">
                </div>
                <div class="form-group  col-12">
                  <label for="modalDescription">Descripcion del tema</label>
                  <textarea class="form-control" name="description" id="modalDescription" rows="3" required></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');

  ?>

</body>

</html>