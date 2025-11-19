<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$student = new Student(Session::id());
$exams = $student->exams(Util::daysBefore(30));

// Util::dump($exams);
$lang = new Lang([
   ['Mis Exámenes', 'My exams'],
   ['Leyenda', 'Legend'],
   ['Examen sin tomar', 'Not taken examn'],
   ['Examen tomado', 'Taken exam'],
   ['Examen vencido', 'Expired exam'],
   ['Valor', 'Value'],
   ['Puntos conseguidos', 'Points gained'],
   ['Tomar Examen', 'Take exam'],
   ['No tienes examenes pendientes!', 'You have no pending exams!'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = $lang->translation('Mis Exámenes');
   Route::includeFile('/foro/estudiante/includes/layouts/header.php');
   ?>
</head>

<body class='pb-5'>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-5 px-0 pb-5">
      <?php if (__SCHOOL_URL === "/omar"): ?>
         <div id="deleteExam" class="d-none">
            <input type="text" id="examId">
            <button class="btn btn-outline-danger">Borrar</button>
         </div>
      <?php endif ?>
      <h1 class="text-center mb-3"><?= $lang->translation("Mis Exámenes") ?></h1>
      <?php if ($exams): ?>
         <!-- leyend -->
         <div class="card mx-auto bg-gradient-light bg-light" style="max-width: 30rem">
            <h6 class="card-header bg-gradient-info bg-info py-2"><?= $lang->translation("Leyenda") ?></h6>
            <div class="card-body p-2">
               <div class="row text-center">
                  <div class="col-6">
                     <i class="fas fa-circle text-white border rounded-circle border-dark"></i> <?= $lang->translation("Examen sin tomar") ?>
                  </div>
                  <div class="col-6">
                     <i class="fas fa-circle text-success"></i> <?= $lang->translation("Examen tomado") ?>
                  </div>
                  <div class="col-12 mt-2">
                     <i class="far fa-square text-danger"></i> <?= $lang->translation("Examen vencido") ?>
                  </div>
               </div>
            </div>
         </div>
      <?php endif ?>
      <?php if (Session::get('examTaken')): ?>
         <div class="alert alert-success lert-dismissible mt-3 mb-0 animated zoomIn" role="alert">
            <?= Session::get('examTaken', true) ?>
            <button type="button" class="close" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
      <?php endif ?>
   </div>

   <!-- Exam list -->
   <div class="container">
      <?php if ($exams): ?>
         <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
            <?php foreach ($exams as $exam): ?>
               <?php
               $doneExam = $student->doneExam($exam->id);

               $points = $doneExam ? ($doneExam->puntos + $doneExam->bonos) : '';

               $sent = $doneExam ? true : false;
               $expired = $exam->fecha >= Util::date() ? false : true;
               if ($expired || $sent || Util::date() < $exam->fecha) {
                  $disabled = true;
               } elseif (Util::date() === $exam->fecha) {
                  if (Util::time() >= $exam->hora && Util::time() <= $exam->hora_final) {
                     $disabled = false;
                  } else {
                     $disabled = true;
                  }
               }
               ?>
               <div class="col mb-4 exam <?= $exam->id ?>">
                  <div class="card h-100 <?= $expired ? "border-danger" : "" ?>">
                     <h6 class="card-header bg-gradient-primary bg-primary d-flex justify-content-between">
                        <?= "{$exam->curso} - {$exam->desc}" ?>
                        <i class="fas fa-circle text-<?= $sent ? 'success' : 'white' ?>"></i>
                     </h6>
                     <div class="card-body ">
                        <h5 class="card-title"><?= utf8_decode($exam->titulo) ?></h5>
                        <p class="card-text"><?= $lang->translation("Valor") ?><?= ": $exam->valor" ?></p>
                        <p class="card-text">
                           <a href="<?= Route::url("/foro/estudiante/includes/pdf/pdfExam.php?examenId={$exam->id}") ?>" target="_blank">
                              <span class="<?= ($points >= $exam->valor * 0.70) ? "text-success" : "text-danger" ?>"><?= $points > 0 ? $lang->translation("Puntos conseguidos") . ": $points" : "" ?></span>
                           </a>
                        </p>

                     </div>
                     <div class="card-footer bg-gradient-secondary bg-secondary d-flex justify-content-between">
                        <small class="text-white"><?= Util::formatDate($exam->fecha, true) ?></small>
                        <small class="text-white blend-screen">
                           <span class="text-info"><?= (strpos($exam->hora, '(') > -1 ? $exam->hora : Util::formatTime($exam->hora)) ?></span> -
                           <span class="text-danger"><?= Util::formatTime($exam->hora_final) ?> </span>
                        </small>
                     </div>
                     <button type="button" data-exam-id="<?= $exam->id ?>" class="btn btn-info btn-block rounded-0 takeExam" <?= $disabled ? "aria-disabled='true' disabled" : "" ?>><?= $lang->translation("Tomar Examen") ?></button>
                  </div>
               </div>
            <?php endforeach ?>

         </div> <!-- end row -->
      <?php else: ?>
         <div class="alert alert-info mx-auto" role="alert">
            <?= $lang->translation("No tienes examenes pendientes!") ?> <i class="far fa-laugh-beam"></i>
         </div>
      <?php endif ?>
   </div><!-- end container -->

   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>
</body>

</html>