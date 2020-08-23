<?php
require_once '../../app.php';

use Classes\Controllers\Exam;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Server;
use Classes\Util;

Session::is_logged();
Server::is_post();
$id_exam = $_POST['examId'];
$student = new Student(Session::id());

$exam = new Exam($id_exam);
$topicNumber = 1;
// Util::dump($exam);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Examen";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
   <div class="container-lg mt-md-5 px-0 pb-5">
   <button id="startExam" class="btn btn-primary mx-auto d-block my-2">Empezar examen</button>
      <div class="jumbotron py-4">
         <div class="container bg-white px-3 py-5 p-md-5 shadow blur">

            <h1 class="text-center mb-5"><?= $exam->titulo ?></h1>
            <form method="POST" action="<?= Route::url('/foro/estudiante/includes/takeExam.php') ?>" class="needs-validation" novalidate>
               <input type="hidden" name="id_exam" value="<?= $id_exam ?>">
               <?php if (isset($exam->fvs->topics)) : ?>
                  <!-- FV -->
                  <h4 class="mt-3"><?= "{$topicNumber} - "?><?= $exam->desc1 === 'si' ? $exam->desc1_1 :$exam->fvs->title ?> <span class="badge badge-info"><?= $exam->fvs->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->fvs->topics as $topic) : ?>
                     <div class="form-group">
                        <label class="font-weight-bold" for="fv<?= $count ?>"><?= "$count) $topic->pregunta"; ?></label>
                        <select id="fv<?= $count ?>" class="form-control" name="fv[<?= $topic->id ?>]" required>
                           <option value="" selected>Selecciona la respuesta</option>
                           <option value="v">Verdadero</option>
                           <option value="f">Falso</option>
                        </select>
                     </div>
                     <?php $count++; ?>
                  <?php endforeach ?>
                  <?php $topicNumber++; ?>
                  <!-- END FV -->
               <?php endif; ?>

               <?php if (isset($exam->selects->topics)) : ?>
                  <!-- SELECT -->
                  <h4 class="mt-3"><?= "{$topicNumber} - "?><?= $exam->desc2 === 'si' ? $exam->desc2_1 :$exam->selects->title ?><span class="badge badge-info"><?= $exam->selects->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->selects->topics as $topic) : ?>
                     <p class="font-weight-bold"><?= "$count) $topic->pregunta"; ?></p>
                     <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                        <?php for ($i = 1; $i <= 8; $i++) : ?>
                           <?php if (!empty($topic->{"respuesta{$i}"})) : ?>
                              <div class="col form-group">
                                 <div class="custom-control custom-radio">
                                    <input type="radio" id="select<?= "{$count}_{$i}" ?>" name="select[<?= "{$topic->id}" ?>]" class="custom-control-input" value="<?= "{$i}" ?>" required>
                                    <label class="custom-control-label" for="select<?= "{$count}_{$i}" ?>"><?= $topic->{"respuesta{$i}"} ?></label>
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

               <?php if (isset($exam->pairs->topics)) : ?>
                  <!-- PAIRS -->
                  <h4 class="mt-3"><?= "{$topicNumber} - "?><?= $exam->desc3 === 'si' ? $exam->desc3_1 :$exam->pairs->title ?><span class="badge badge-info"><?= $exam->pairs->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->pairs->topics as $topic) : ?>
                     <div class="form-group row">
                        <label class="col-8" for="pair<?= $count ?>"><?= "$count) $topic->pregunta"; ?></label>
                        <select id="pair<?= $count ?>" class="form-control col-4" name="pair[<?= $topic->id ?>]" required>
                           <option value="" selected>Selecciona la respuesta</option>
                           <?php foreach ($exam->pairCodes->topics as $answer) : ?>
                              <option value="<?= $answer->id ?>"><?= $answer->respuesta ?></option>
                           <?php endforeach ?>
                        </select>

                     </div>
                     <?php $count++; ?>
                  <?php endforeach ?>
                  <?php $topicNumber++; ?>
                  <!-- END PAIRS -->
               <?php endif; ?>

               <?php if (isset($exam->lines->topics)) : ?>
                  <!-- LINES -->
                  <h4 class="mt-3"><?= "{$topicNumber} - "?><?= $exam->desc4 === 'si' ? $exam->desc4_1 :$exam->lines->title ?> <span class="badge badge-info"><?= $exam->lines->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->lines->topics as $topic) : ?>
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


               <?php if (isset($exam->qas->topics)) : ?>
                  <!-- QA -->

                  <h4 class="mt-3"><?= "{$topicNumber} - "?><?= $exam->desc5 === 'si' ? $exam->desc5_1 :$exam->qas->title ?> <span class="badge badge-info"><?= $exam->qas->value ?></span></h4>
                  <?php $count = 1 ?>
                  <?php foreach ($exam->qas->topics as $topic) : ?>
                     <div class="form-group">
                        <label class="font-weight-bold" for="qa<?= $count ?>"><?= "$count) $topic->pregunta"; ?></label>
                        <textarea class="form-control normal" id="qa<?= $count ?>" name="qa[<?= $topic->id ?>]" required></textarea>
                     </div>
                     <?php $count++ ?>
                  <?php endforeach ?>
                  <?php $topicNumber++ ?>
                  <!-- END QA -->
               <?php endif ?>
               <div class="alert alert-danger invisible" role="alert">
                  Debe de completar el examen antes de continuar
               </div>
               <button type="submit" class="btn btn-primary btn-block">Terminar examen</button>
            </form>
         </div>
      </div>
   </div>

   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
   ?>
</body>

</html>