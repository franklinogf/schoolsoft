<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Topic;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
if (!isset($_GET['id'])) {
  Route::error();
}
$topic = new Topic($_GET['id']);
if(!isset($topic->id)){
  Route::error();
}
$student = new Student(Session::id());
$teacher = new Teacher($topic->creador_id);
$comments = $topic->comments();
$lang = new Lang([
  ['Temas', 'Topics'],  
  ['Comentario nuevo', 'New comment'],
  ['Por favor escriba algo', 'Please write something'],
  ['Comentar', 'Comment'],
  
  
  
  
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = $topic->titulo;
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>
  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container-lg mt-lg-2">

    <div class="row mt-3">
      <div class="col-lg-4">
        <a id="back" class="btn btn-outline-secondary btn-lg btn-block mb-3" href="<?= Route::url('/foro/estudiante/topics.php') ?>">
          <i class="far fa-comment"></i> <?= $lang->translation("Temas") ?>
        </a>
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
      <?php if ($topic->estado === 'a' && Util::date() <= $topic->desde) : ?>
        <div class="form-group">
          <label for="comment"><?= $lang->translation("Comentario nuevo") ?></label>
          <textarea class="form-control" id="comment" rows="3" required></textarea>
          <div class="invalid-feedback"><?= $lang->translation("Por favor escriba algo") ?></div>
        </div>
        <button class="btn btn-primary" id="insertComment" type="submit"><?= $lang->translation("Comentar") ?></button>
      <?php endif ?>


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
                <p class="m-0 p-2"><?= $comment->descripcion ?></p>
                <p class="text-muted text-right"><?= Util::formatDate($comment->fecha, true, true) . ' ' . Util::formatTime($comment->hora) ?></p>
              </div>
            </div>
          <?php endforeach ?>
        </div>
    </div>

  </div>

  <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>