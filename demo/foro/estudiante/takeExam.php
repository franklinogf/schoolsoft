<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Exam;
use Classes\Controllers\Student;

Session::is_logged();
Server::is_post();
$id_exam = $_POST['examId'];
$student = new Student(Session::id());

$exam = new Exam($id_exam);
$topicNumber = 1;
// Util::dump($exam);
$lang = new Lang([
   ['Empezar examen', 'Start exam'],
   ['Examen', 'Exam'],
   ['No estoy listo', 'I am not ready'],
   ['Verdadero', 'True'],
   ['Falso', 'False'],
   ['Terminar examen', 'Finish exam'],
   ['Va a empezar el examen', 'The exam will start'],
   ['Tiene', 'You have'],
   ['minutos para terminar', 'minutes to finish'],
   ['Si sale de la pantalla completa se tomara como realizado', 'If you go out of the full screen the exam will end'],
   ['Asi que tenga Cuidado!', 'So be careful!'],
   ['Cancelar', 'Cancel'],
   ['Atención!', 'Warning!'],
   ['Aun tiene preguntas sin contestar, está seguro de que desea continuar?', 'You still have unanswered questions, are you sure you want to continue?'],
   ['Cancelar', 'Cancel'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = $lang->translation('Examen');
   Route::includeFile('/foro/estudiante/includes/layouts/header.php');
   ?>
</head>

<body class='pb-5'>
   <div class="container-lg mt-md-5 px-0 pb-5">

      <div id="timeRectangule" class="rectangule hidden">
         <svg class="rectangule">
            <polygon points="0,0 100,0 100,50 0,50" />
         </svg>
         <p id="timer" class="text-primary font-weight-bold"><?= $exam->tiempo ?></p>
      </div>
      <div id="menuButtons">
         <button id="askForExam" class="btn btn-primary mx-auto d-block my-2"><?= $lang->translation("Empezar examen") ?></button>
         <a href="<?= Route::url('/foro/estudiante/exams.php') ?>" class="btn btn-secondary mx-auto d-block my-2"><?= $lang->translation("No estoy listo") ?></a>
      </div>
      <div class="jumbotron py-4 blur">
         <div class="container bg-white px-3 py-5 p-md-5 shadow">

            <h1 class="text-center mb-5"><?= utf8_decode($exam->titulo) ?></h1>
            <form method="POST" action="<?= Route::url('/foro/estudiante/includes/takeExam.php') ?>" class="needs-validation" novalidate>
               <input type="hidden" name="id_exam" value="<?= $id_exam ?>">
               <input type="hidden" name="id_student" value="<?= $student->mt ?>">
               <?php if (isset($exam->fvs->topics)): ?>
                  <!-- FV -->
                  <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc1 === 'si' ? utf8_decode($exam->desc1_1) : utf8_decode($exam->fvs->title) ?> <span class="badge badge-info"><?= $exam->fvs->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->fvs->topics as $topic): ?>
                     <div class="form-group">
                        <label class="font-weight-bold" for="fv<?= $count ?>"><?= utf8_decode("$count) $topic->pregunta"); ?></label>
                        <select id="fv<?= $count ?>" class="form-control" name="fv[<?= $topic->id ?>]" required>
                           <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                           <option value="v"><?= $lang->translation("Verdadero") ?></option>
                           <option value="f"><?= $lang->translation("Falso") ?></option>
                        </select>
                     </div>
                     <?php $count++; ?>
                  <?php endforeach ?>
                  <?php $topicNumber++; ?>
                  <!-- END FV -->
               <?php endif; ?>

               <?php if (isset($exam->selects->topics)): ?>
                  <!-- SELECT -->
                  <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc2 === 'si' ? utf8_decode($exam->desc2_1) : utf8_decode($exam->selects->title) ?><span class="badge badge-info"><?= $exam->selects->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->selects->topics as $topic): ?>
                     <p class="font-weight-bold"><?= utf8_decode("$count) $topic->pregunta"); ?></p>
                     <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                           <?php if (!empty($topic->{"respuesta{$i}"}) || $topic->{"respuesta{$i}"} === "0"): ?>
                              <div class="col form-group">
                                 <div class="custom-control custom-radio">
                                    <input type="radio" id="select<?= "{$count}_{$i}" ?>" name="select[<?= "{$topic->id}" ?>]" class="custom-control-input" value="<?= "{$i}" ?>" required>
                                    <label class="custom-control-label" for="select<?= "{$count}_{$i}" ?>"><?= utf8_decode($topic->{"respuesta{$i}"}) ?></label>
                                 </div>
                              </div>
                           <?php endif ?>
                        <?php endfor ?>
                     </div>
                     <?php $count++; ?>
                  <?php endforeach ?>
                  <?php $topicNumber++; ?>
                  <!-- END SELECT -->
               <?php endif; ?>

               <?php if (isset($exam->pairs->topics)): ?>
                  <!-- PAIRS -->
                  <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc3 === 'si' ? utf8_decode($exam->desc3_1) : utf8_decode($exam->pairs->title) ?><span class="badge badge-info"><?= $exam->pairs->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->pairs->topics as $topic): ?>
                     <div class="form-group row">
                        <label class="col-8" for="pair<?= $count ?>"><?= utf8_decode("$count) $topic->pregunta"); ?></label>
                        <select id="pair<?= $count ?>" class="form-control col-4" name="pair[<?= $topic->id ?>]" required>
                           <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                           <?php foreach ($exam->pairCodes->topics as $answer): ?>
                              <option value="<?= $answer->id ?>"><?= utf8_decode($answer->respuesta) ?></option>
                           <?php endforeach ?>
                        </select>

                     </div>
                     <?php $count++; ?>
                  <?php endforeach ?>
                  <?php $topicNumber++; ?>
                  <!-- END PAIRS -->
               <?php endif; ?>

               <?php if (isset($exam->lines->topics)): ?>
                  <!-- LINES -->
                  <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc4 === 'si' ? utf8_decode($exam->desc4_1) : utf8_decode($exam->lines->title) ?> <span class="badge badge-info"><?= $exam->lines->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->lines->topics as $topic): ?>
                     <?php
                     $question = utf8_decode($topic->pregunta);
                     $question = str_replace('___', "
                  <input class='form-control form-control-sm d-inline mt-2 rounded-0 border-top-0 border-left-0 border-right-0 border-dark shadow-sm' 
                  style='width:10em' 
                  type='text' 
                  name='line[{$topic->id}][]' required>", $question);
                     ?>
                     <p><?= "$count) $question"; ?></p>

                     <?php $count++ ?>
                  <?php endforeach ?>
                  <?php $topicNumber++ ?>
                  <!-- END LINES -->
               <?php endif ?>


               <?php if (isset($exam->qas->topics)): ?>
                  <!-- QA -->

                  <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc5 === 'si' ? utf8_decode($exam->desc5_1) : utf8_decode($exam->qas->title) ?> <span class="badge badge-info"><?= $exam->qas->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->qas->topics as $topic): ?>
                     <div class="form-group">
                        <label class="font-weight-bold" for="qa<?= $count ?>"><?= utf8_decode("$count) $topic->pregunta"); ?></label>
                        <textarea class="form-control normal" id="qa<?= $count ?>" name="qa[<?= $topic->id ?>]" required></textarea>
                     </div>
                     <?php $count++ ?>
                  <?php endforeach ?>
                  <?php $topicNumber++ ?>
                  <!-- END QA -->
               <?php endif ?>

               <button type="submit" class="btn btn-primary btn-block"><?= $lang->translation("Terminar examen") ?></button>
            </form>

         </div>
      </div>
   </div>
   <!-- modal start exam -->
   <div class="modal fade" id="modalExam" data-backdrop="static" data-keyboard="false" tabindex="9998" aria-labelledby="modalExamLabel" aria-hidden="true">
      <div class="modal-dialog  modal-dialog-centered">
         <div class="modal-content">
            <h5 class="modal-header bg-warning"><?= $lang->translation("Va a empezar el examen") ?>.</h5>
            <div class="modal-body">
               <p><?= $lang->translation("Tiene") ?> <b class="text-primary"><?= $exam->tiempo ?></b> <?= $lang->translation("minutos para terminar") ?>.</p>
               <p><?= $lang->translation("Si sale de la pantalla completa se tomara como realizado") ?>.</p>
               <p><b><?= $lang->translation("Asi que tenga Cuidado!") ?></b></p>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cancelar") ?></button>
               <button id="startExam" type="button" class="btn btn-warning"><?= $lang->translation("Empezar Examen") ?></button>
            </div>
         </div>
      </div>
   </div>
   <!-- modal alert -->
   <div class="modal fade" id="modalAlert" data-backdrop="static" data-keyboard="false" tabindex="9998" aria-labelledby="modalAlertLabel" aria-hidden="true">
      <div class="modal-dialog  modal-dialog-centered">
         <div class="modal-content">
            <h5 class="modal-header bg-danger"><?= $lang->translation("Atención!") ?></h5>
            <div class="modal-body">
               <?= $lang->translation("Aun tiene preguntas sin contestar, está seguro de que desea continuar?") ?>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cancelar") ?></button>
               <button id="finishExam" type="button" class="btn btn-danger"><?= $lang->translation("Terminar examen") ?></button>
            </div>
         </div>
      </div>
   </div>

   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>
</body>

</html>