<?php
require_once '../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$student = new Student(Session::id());
$exams = $student->exams(Util::daysBefore(3));
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Examenes";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-5 px-0 pb-5">
      <h1 class="text-center mb-3">Mis Examenes</h1>
      <?php if ($exams) : ?>
         <!-- leyend -->
         <div class="card mx-auto bg-gradient-light bg-light" style="max-width: 30rem">
            <h6 class="card-header bg-gradient-info bg-info py-2">Leyenda</h6>
            <div class="card-body p-2">
               <div class="row text-center">
                  <div class="col-6">
                     <i class="fas fa-circle text-white border rounded-circle border-dark"></i> Examen sin tomar
                  </div>
                  <div class="col-6">
                     <i class="fas fa-circle text-success"></i> Examen tomado
                  </div>
                  <div class="col-12 mt-2">
                     <i class="far fa-square text-danger"></i> Examen vencido
                  </div>
               </div>
            </div>
         </div>
      <?php endif ?>
      <?php if (Session::get('examTaken')) : ?>
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
      <?php if ($exams) : ?>
         <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
            <?php foreach ($exams as $exam) : ?>
               <?php $doneExam = $student->doneExam($exam->id) ?>
               <?php $points = ($doneExam->puntos + $doneExam->bonos) ?>
               <?php $sent = $doneExam ? 'success' : 'white' ?>
               <?php $expired = $exam->fecha >= Util::date() ? '' : 'danger'; ?>
               <div class="col mb-4 exam <?= $exam->id ?>">
                  <div class="card h-100 <?= $expired === 'danger' ? "border-{$expired}" : "" ?>">
                     <h6 class="card-header bg-gradient-primary bg-primary d-flex justify-content-between">
                        <?= "{$exam->curso} - {$exam->desc}" ?>
                        <i class="fas fa-circle text-<?= $sent ?>"></i></h6>
                     <div class="card-body ">
                        <h5 class="card-title"><?= $exam->titulo ?></h5>
                        <p class="card-text"><?= "Valor: $exam->valor" ?></p>
                        <p class="card-text"><span class="<?= ($points >= $exam->valor*0.70) ? "text-success" :"text-danger" ?>"><?= $points > 0 ? "Puntos conseguidos: $points" : ""?></span></p>

                     </div>
                     <div class="card-footer bg-gradient-secondary bg-secondary d-flex justify-content-between">
                        <small class="text-primary blend-screen"><?= Util::formatDate($exam->fecha, true) ?></small>
                        <small class="text-primary blend-screen"><?= (strpos($exam->hora, '(') > -1 ? $exam->hora  : Util::formatTime($exam->hora)) ?></small>
                     </div>
                     <button type="button" data-exam-id="<?= $exam->id ?>" class="btn btn-info btn-block rounded-0 takeExam" <?= $expired === 'danger' || $sent === 'success' ? "aria-disabled='true' disabled" : "" ?>>Tomar Examen</button>
                  </div>
               </div>
            <?php endforeach ?>

         </div> <!-- end row -->
      <?php else : ?>
         <div class="alert alert-info mx-auto" role="alert">
            No tienes examenes pendientes! <i class="far fa-laugh-beam"></i>
         </div>
      <?php endif ?>
   </div><!-- end container -->

   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
   ?>
</body>

</html>