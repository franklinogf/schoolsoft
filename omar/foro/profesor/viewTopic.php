<?php
require_once '../../app.php';

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;
use Classes\Session;

Session::is_logged();

$topic = new Topic($_GET['id']);
$teacher = new Teacher($topic->creador_id);
$student = new Student();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - <?= $topic->titulo ?></title>
  <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>
  <!-- <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" data-auto-replace-svg="nest"></script> -->

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-2">

    <div class="row mt-3">
      <div class="col-lg-4">
        <button type="button" id="editTopicBtn" class="btn btn-outline-primary btn-lg btn-block mb-3">
          <i class="fas fa-edit fa-flip-horizontal"></i> Editar tema
        </button>
      </div>
      <div class="col-lg-8 d-flex justify-content-end align-items-end">
        <i class="fas fa-toggle-on fa-3x <?= ($topic->estado === 'a' ? 'text-success' : 'text-danger') ?> "></i>
      </div>
    </div>


    <div class="card">
      <h4 id="title" class="card-header text-center bg-gradient-primary bg-primary rounded-0"><?= $topic->titulo ?></h4>
      <div class="card-body">
        <h5 class="card-title"><?= $teacher->fullName(); ?></h5>
        <p id="description" class="card-text"><?= $topic->descripcion ?></p>
      </div>
      <div class="card-footer text-white d-flex justify-content-between bg-gradient-secondary bg-secondary">
        <span><?= Util::formatDate($topic->fecha, true, true) ?></span>
        <span><?= Util::formatTime($topic->hora) ?></span>
      </div>
    </div>

    <div class="container mt-3 pb-5">
      <?php if ($topic->estado === 'a') : ?>
        <form action="<?= Route::url('/foro/profesor/includes/comments.php') ?>" method="POST">
          <input type="hidden" name="id_topic" value="<?= $topic->id ?>">
          <div class="form-group">
            <label for="comment">Comentario nuevo</label>
            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
          </div>
          <button class="btn btn-primary" type="submit">Comentar</button>
        </form>
      <?php endif ?>


      <?php foreach ($topic->comments() as $comment) : ?>
        <div class="media bg-gradient-light bg-light mt-3 pt-3 px-3">
          <img src="<?= __NO_PROFILE_PICTURE ?>" class="mr-3" alt="profile picture" width="64" height="64">
          <div class="media-body">

            <h5 class="mt-0"><?= ($comment->tipo === 'p' ? '<i class="fas fa-user-tie fa-sm"></i> ' . $teacher->fullName() : '<i class="fas fa-user-graduate fa-sm"></i>' . $student->find($comment->creador_id)->fullName()) ?></h5>
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
          <form action="<?= Route::url('/foro/profesor/includes/viewTopics.php') ?>" method="POST">
            <div class="modal-body">
              <input type="hidden" name="id_topic" id="id_topic" value="<?= $topic->id ?>">
              <div class="form-row">
                <div class="form-group col-12">
                  <label for="modalTitle">Titulo del tema:</label>
                  <input type="text" class="form-control" name='title' id="modalTitle" value="<?= $topic->titulo ?>">
                </div>
                <div class="form-group  col-12">
                  <label for="modalDescription">Descripcion del tema</label>
                  <textarea class="form-control" name="description" id="modalDescription" rows="3" required><?= $topic->descripcion ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-6">
                  <label class="d-block">Tema disponible?</label>

                  <div class="custom-control custom-radio custom-control-inline">
                    <input class="custom-control-input" type="radio" name="state" id="radio1" value="a" <?= ($topic->estado === 'a' ? 'checked' : '') ?>>
                    <label class="custom-control-label" for="radio1">Si</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input class="custom-control-input" type="radio" name="state" id="radio2" value="c" <?= ($topic->estado === 'c' ? 'checked' : '') ?>>
                    <label class="custom-control-label" for="radio2">No</label>
                  </div>

                </div>
                <div class="form-group col-6">
                  <label for="modalUntilDate">Disponible hasta:</label>
                  <input type="date" class="form-control" name='untilDate' id="modalUntilDate" min='<?= Util::date() ?>' value="<?= $topic->desde ?>">
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
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