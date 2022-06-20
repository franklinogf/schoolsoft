<?php
require_once '../../app.php';

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;
use Classes\Session;

Session::is_logged();
if (!isset($_GET['id'])) {
  Route::error();
}
$topic = new Topic($_GET['id']);
if(!isset($topic->id)){
  Route::error();
}
$teacher = new Teacher(Session::id());

$comments = $topic->comments();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $topic->titulo;
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-2">

    <div class="row mt-3">
      <div class="col-lg-4">
        <a id="back" class="btn btn-outline-secondary btn-lg btn-block mb-3" href="<?= Route::url('/foro/profesor/topics.php') ?>">
          <i class="far fa-comment"></i> Temas
        </a>
      </div>
      <div class="col-lg-4">
        <button type="button" id="editTopicBtn" class="btn btn-outline-primary btn-lg btn-block mb-3">
          <i class="fas fa-edit fa-flip-horizontal"></i> Editar tema
        </button>
      </div>
      <?php if (!$comments) : ?>
        <div class="col-lg-3">
          <button data-topic-id="<?= $topic->id ?>" class="btn btn-danger btn-lg btn-block delTopic">
            <i class="fas fa-trash-alt"></i> Borrar Tema
          </button>
        </div>
      <?php endif ?>
      <div class="<?= $comments ? 'col-lg-4' : 'col-lg-1' ?> d-flex justify-content-end align-items-end">
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

    <div class="mt-3 pb-5">
      <?php if ($topic->estado === 'a') : ?>
        <div class="form-group">
          <label for="comment">Comentario nuevo</label>
          <textarea class="form-control" id="comment" rows="3" required></textarea>
          <div class="invalid-feedback">Por favor escriba algo</div>
        </div>
        <button class="btn btn-primary" id="insertComment" type="submit">Comentar</button>
      <?php endif ?>


      <?php if ($comments) : ?>
        <div id="commentsList" class="bg-white">
          <?php foreach ($comments as $comment) : ?>
            <?php
            $student = new Student($comment->creador_id);
            $profilePicture = $comment->tipo === 'p' ? $teacher->profilePicture() : $student->profilePicture();
            ?>
            <div class="media mt-3 pt-3 px-3 border-primary-gradient-top">
              <img src="<?= $profilePicture ?>" class="align-self-center mr-3 rounded-circle" alt="profile picture" width="72" height="72">
              <div class="media-body">
                <h5 class="mt-0"><?= ($comment->tipo === 'p' ? '<i class="fas fa-user-tie fa-xs"></i> ' . $teacher->fullName() : '<i class="fas fa-user-graduate fa-xs"></i> ' . $student->fullName()) ?></h5>
                <p class="m-0 mb-2 p-2 text-break"><?= $comment->descripcion ?></p>
                <p class="text-muted text-right"><?= Util::formatDate($comment->fecha, true, true) . ' ' . Util::formatTime($comment->hora) ?></p>
                <!-- <button data-comment-id="<?php //echo $comment->id 
                                              ?>" class="btn btn-sm btn-danger mb-3 d-block ml-auto delComment">Borrar <i class="fas fa-trash-alt fa-sm"></i></button> -->
              </div>
            </div>
          <?php endforeach ?>
        </div>
      <?php endif ?>
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
          <form action="<?= Route::url('/foro/profesor/includes/viewTopic.php') ?>" method="POST">
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
                  <input type="date" class="form-control" name='untilDate' id="modalUntilDate" min='<?= Util::date() ?>' value="<?= $topic->desde !== '0000-00-00' ? $topic->desde : '' ?>">
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" name="editTopic" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>