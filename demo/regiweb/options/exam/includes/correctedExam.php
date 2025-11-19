<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Exam;
use Classes\Controllers\Student;

Session::is_logged();
// Server::is_post();
$examId = $_REQUEST['examId'];
$studentMt = $_REQUEST['studentMt'];
$student = new Student($studentMt);
$doneExam = $student->doneExam($examId);
$doneExamId = $doneExam->id;
$exam = new Exam($examId);
$topicNumber = 1;
$examGainedPoints = 0;
// var_dump($doneExam);

function isCorrect($q, $a, $css = true)
{
   if ($css) {
      return $q == $a ? 'text-success' : 'text-danger';
   } else {
      return $q == $a;
   }
}

?>

<head>
   <?php
   $title = "Examen";
   Route::includeFile('/foro/estudiante/includes/layouts/header.php');
   ?>
</head>
<div class="container bg-white px-3 py-2 shadow">

   <h2 class="text-center mb-5"><?= $exam->titulo ?> <span class="badge badge-info">(<span class="totalExam"><?= $doneExam->puntos ?></span>/<?= $exam->valor ?>)</span></h2>
   <?php if (isset($exam->fvs->topics)) :
      $total = 0;
   ?>
      <!-- FV -->
      <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc1 === 'si' ? utf8_decode($exam->desc1_1) : utf8_decode($exam->fvs->title) ?> <span class="badge badge-info"><?= $exam->fvs->value ?></span></h4>
      <?php $count = 1 ?>
      <?php foreach ($exam->fvs->topics as $topic) :
         $done = DB::table('T_examen_terminado_fyv')->where([
            ['id_examen', $doneExamId],
            ['id_pregunta', $topic->id],
            ['id_estudiante', $studentMt]
         ])->first();
         $total += isCorrect($done->respuesta, $topic->respuesta, false) ? $topic->valor : 0;
      ?>
         <div class="form-group">
            <label class="font-weight-bold <?= isCorrect($done->respuesta, $topic->respuesta) ?>" for="fv<?= $count ?>"><?= utf8_decode("$count) $topic->pregunta"); ?> <span>(<?= $topic->valor ?>)</span></label>
            <select id="fv<?= $count ?>" class="form-control form-control-sm" disabled>
               <option <?= $done->respuesta == '' ? 'selected=""' : '' ?> value="">Sin respuesta</option>
               <option <?= $done->respuesta == 'v' ? 'selected=""' : '' ?> value="v">Verdadero</option>
               <option <?= $done->respuesta == 'f' ? 'selected=""' : '' ?> value="f">Falso</option>
            </select>
         </div>
         <?php $count++; ?>
      <?php endforeach;
       $examGainedPoints += $total;
      ?>
      <?php $topicNumber++; ?>
      <!-- END FV -->
      <p class="text-primary">Total: <b><?= $total ?></b></p>
      <hr>
   <?php endif; ?>

   <?php if (isset($exam->selects->topics)) :
      $total = 0;
   ?>
      <!-- SELECT -->
      <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc2 === 'si' ? utf8_decode($exam->desc2_1) : utf8_decode($exam->selects->title) ?><span class="badge badge-info"><?= $exam->selects->value ?></span></h4>
      <?php $count = 1 ?>
      <?php foreach ($exam->selects->topics as $topic) :
         $done = DB::table('T_examen_terminado_selec')->where([
            ['id_examen', $doneExamId],
            ['id_pregunta', $topic->id],
            ['id_estudiante', $studentMt]
         ])->first();
         $total += isCorrect($done->respuesta, $topic->correcta, false) ? $topic->valor : 0;

      ?>
         <p class="font-weight-bold <?= isCorrect($done->respuesta, $topic->correcta) ?>"><?= utf8_decode("$count) $topic->pregunta"); ?> <span>(<?= $topic->valor ?>)</span></p>
         <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
            <?php for ($i = 1; $i <= 8; $i++) : ?>
               <?php if (!empty($topic->{"respuesta{$i}"}) || $topic->{"respuesta{$i}"} === "0") : ?>
                  <div class="col form-group">
                     <div class="custom-control custom-radio">
                        <input type="radio" <?= $done->respuesta == $i ? 'checked=""' : '' ?> id="select<?= "{$count}_{$i}" ?>" class="custom-control-input" value="<?= "{$i}" ?>" disabled>
                        <label class="custom-control-label" for="select<?= "{$count}_{$i}" ?>"><?= utf8_decode($topic->{"respuesta{$i}"}) ?></label>
                     </div>
                  </div>
               <?php endif ?>
            <?php endfor ?>
         </div>
         <?php $count++; ?>
      <?php endforeach;
       $examGainedPoints += $total;
      ?>
      <?php $topicNumber++; ?>
      <!-- END SELECT -->
      <p class="text-primary">Total: <b><?= $total ?></b></p>
      <hr>
   <?php endif; ?>

   <?php if (isset($exam->pairs->topics)) :
      $total = 0;
   ?>
      <!-- PAIRS -->
      <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc3 === 'si' ? utf8_decode($exam->desc3_1) : utf8_decode($exam->pairs->title) ?><span class="badge badge-info"><?= $exam->pairs->value ?></span></h4>
      <?php $count = 1 ?>
      <?php foreach ($exam->pairs->topics as $topic) :
         $done = DB::table('T_examen_terminado_parea')->where([
            ['id_examen', $doneExamId],
            ['id_pregunta', $topic->id],
            ['id_estudiante', $studentMt]
         ])->first();
         $total += isCorrect($done->respuesta, $topic->respuesta_c, false) ? $topic->valor : 0;
      ?>
         <div class="form-row">
            <select id="pair<?= $count ?>" class="form-control col-2" disabled>
               <option value="" selected>Sin respuesta</option>
               <?php foreach ($exam->pairCodes->topics as $index => $answer) : ?>
                  <option <?= $done->respuesta == $answer->id ? 'selected=""' : '' ?> value="<?= $answer->id ?>" deisabled><?= Exam::$letters[$index + 1] ?></option>
               <?php endforeach ?>
            </select>
            <label for="pair<?= $count ?>" class="col-4 <?= isCorrect($done->respuesta, $topic->respuesta_c) ?>"><?= utf8_decode("$count) $topic->pregunta"); ?> <span>(<?= $topic->valor ?>)</span></label>
         </div>
         <?php $count++; ?>
      <?php endforeach ?>
      <div class="row row-cols-3">
         <?php foreach ($exam->pairCodes->topics as $index => $answer) : ?>
            <p><?= Exam::$letters[$index + 1] . ') ' . $answer->respuesta ?></p>
         <?php endforeach ?>
      </div>
      <?php $topicNumber++; ?>
      <!-- END PAIRS -->
      <p class="text-primary">Total: <b><?= $total ?></b></p>
      <hr>
   <?php endif; ?>

   <?php if (isset($exam->lines->topics)) :
      $total = 0;
   ?>
      <!-- LINES -->
      <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc4 === 'si' ? utf8_decode($exam->desc4_1) : utf8_decode($exam->lines->title) ?> <span class="badge badge-info"><?= $exam->lines->value ?></span></h4>
      <?php $count = 1 ?>
      <?php foreach ($exam->lines->topics as $topic) :
         $done = DB::table('T_examen_terminado_linea')->where([
            ['id_examen', $doneExamId],
            ['id_pregunta', $topic->id],
            ['id_estudiante', $studentMt]
         ])->first();
         $words = explode(' ', utf8_decode($topic->pregunta));
         $question = '';
         $count = 1;
         foreach ($words as $word) {
            if (strpos($word, '___') > -1) {
               $total += isCorrect($done->{"respuesta$count"}, $topic->{"respuesta$count"}, false) ? $topic->valor : 0;
               $class = isCorrect($done->{"respuesta$count"}, $topic->{"respuesta$count"});
               $question .= " <u class='{$class}'>{$done->{"respuesta$count"}}</u>
               ";
               // $question .= " <input class='{$class} form-control form-control-sm d-inline mt-2 rounded-0 border-top-0 border-left-0 border-right-0 border-dark shadow-sm' 
               // style='width:10em' 
               // type='text'
               // value='{$done->{"respuesta$count"}}'
               // disabled>";
               $count++;
            } else {
               $question .= " $word";
            }
         }
         $question .= '.';
      ?>
         <p><?= "$count) $question"; ?> <span>(<?= $topic->valor ?>)</span></p>

         <?php $count++ ?>
      <?php endforeach;
       $examGainedPoints += $total;
      ?>
      <?php $topicNumber++ ?>
      <!-- END LINES -->
      <p class="text-primary">Total: <b><?= $total ?></b></p>
      <hr>
   <?php endif ?>


   <?php if (isset($exam->qas->topics)) :

   ?>
      <!-- QA -->

      <h4 class="mt-3"><?= "{$topicNumber} - " ?><?= $exam->desc5 === 'si' ? utf8_decode($exam->desc5_1) : utf8_decode($exam->qas->title) ?> <span class="badge badge-info"><?= $exam->qas->value ?></span></h4>
      <?php
      $count = 1;
      $totalPoints = 0;
      foreach ($exam->qas->topics as $topic) :
         $done = DB::table('T_examen_terminado_pregunta')->where([
            ['id_examen', $doneExamId],
            ['id_pregunta', $topic->id],
            ['id_estudiante', $studentMt]
         ])->first();
         $totalPoints += $done->puntos_ganados;
      ?>
         <div class="form-group">
            <label class="font-weight-bold float-left" for="qa<?= $count ?>"><?= utf8_decode("$count) $topic->pregunta") . " $topic->valor"; ?></label>

            <input class="qaValue float-right" data-id='<?= $done->id ?>' style="width: 4rem;" type="number" class="form-control form-control-sm d-inline float-right" min='0' max="<?= $topic->valor ?>" value="<?= $done->puntos_ganados ?>">
            <textarea class="form-control normal" id="qa<?= $count ?>" disabled></textarea>
         </div>
         <?php $count++ ?>
      <?php endforeach ?>
      <?php $topicNumber++ ?>
      <!-- END QA -->
      <p class="text-primary">Total: <b id="qaTotal"><?= $totalPoints ?></b></p>
   <?php endif ?>
   <input type="hidden" id="examGainedPoint" value="<?= $examGainedPoints ?>">
   <input type="hidden" id="doneExamId" value="<?= $doneExam->id ?>">

   <p>Total de puntos: <b id="totalExam" class="totalExam"><?= $doneExam->puntos ?></b></p>


</div>