<?php
require_once '../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();

$student = new Student(Session::id());

$homeworks = $student->homeworks(Util::daysBefore(5));
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <title>Foro - Tareas</title>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/links.php');
   ?>

</head>

<body class='pb-5'>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-5 px-0 pb-5">

      <h1 class="text-center mb-3">Mis Tareas</h1>
      <?php if ($homeworks) : ?>
         <div class="alert alert-warning mx-auto" role="alert">
            Las tareas se borrarán al día siguiente de la fecha de entrega o vencimiento.
         </div>
      <?php endif ?>
   </div>
   <!-- homework list -->
   <div class="container">
      <?php if ($homeworks) : ?>
         <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
            <?php foreach ($homeworks as $homework) : ?>
               <?php $sent = $student->doneHomework($homework->id_documento) ? 'success' : 'white' ?>
               <?php $expired = $homework->fec_out >= Util::date() || $homework->fec_out === '0000-00-00' ? '' : 'danger'; ?>
               <div class="col mb-4 homework <?= $homework->id_documento ?>">
                  <div class="card <?= $sent !== 'white' && $expired === 'danger' ? "border-{$expired}" : "" ?>">
                     <h6 class="card-header bg-gradient-primary bg-primary d-flex justify-content-between">
                        <?= "{$homework->curso} - {$homework->desc}" ?>
                        <i class="fas fa-circle text-<?= $sent ?>"></i></h6>
                     <div class="card-body ">
                        <h5 class="card-title"><?= $homework->titulo ?></h5>
                        <p class="card-text"><?= $homework->descripcion ?></p>
                     </div>
                     <div class="card-footer bg-white">
                        <small class="card-text text-warning d-block"><?= $homework->fec_out !== '0000-00-00' ? "Fecha final: " . Util::formatDate($homework->fec_out, true) : 'Sin fecha de finalización' ?></small>
                        <?php if (!empty($homework->lin1) || !empty($homework->lin2) || !empty($homework->lin3)) : ?>
                           <div class="btn-group btn-group-sm w-100 mt-2">
                              <?php for ($i = 1; $i <= 3; $i++) : ?>
                                 <?php if ($homework->{"lin{$i}"} !== '') : ?>
                                    <a href="<?= $homework->{"lin{$i}"} ?>" target="_blank" data-toggle="tooltip" title='<?= $homework->{"lin{$i}"} ?>' class="btn btn-outline-info px-1"><i class="fas fa-external-link-alt"></i> Link <?= $i ?> </a>
                                 <?php endif ?>
                              <?php endfor ?>
                           </div>
                        <?php endif ?>

                        <?php if (property_exists($homework, 'archivos')) : ?>
                           <div class="btn-group-vertical w-100 mt-2">
                              <?php foreach ($homework->archivos as $i => $file) : ?>
                                 <a data-file-id="<?= $file->id ?>" target="_blank" href="<?= __TEACHER_HOMEWORKS_DIRECTORY_URL . $file->nombre ?>" data-toggle="tooltip" title='<?= File::name($file->nombre, true) ?>' class="btn btn-outline-dark btn-sm downloadFIle" download><?= File::faIcon(File::extension($file->nombre)) . " Archivo " . ($i + 1) ?> </a>
                              <?php endforeach ?>
                           </div>
                        <?php endif ?>

                     </div>
                     <div class="card-footer bg-gradient-secondary bg-secondary d-flex justify-content-between">
                        <small class="text-primary blend-screen"><?= Util::formatDate($homework->fec_in, true) ?></small>
                        <small class="text-primary blend-screen"><?= (strpos($homework->hora, '(') > -1 ? $homework->hora  : Util::formatTime($homework->hora)) ?></small>
                     </div>
                     <button type="button" data-homework-id="<?= $homework->id_documento ?>" class="btn btn-info btn-block rounded-0 sendHomework" <?= $expired === 'danger' ? "aria-disabled='true' disabled" : "" ?>>Enviar tarea</button>
                  </div>

               </div>
            <?php endforeach ?>

         </div> <!-- end row -->
      <?php else : ?>
         <div class="alert alert-info mx-auto" role="alert">
            No tienes tareas pendientes! <i class="far fa-laugh-beam"></i>
         </div>
      <?php endif ?>
   </div><!-- end container -->
   <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="dialog">
         <div class="modal-content">
            <div class="modal-header bg-primary">
               <h5 class="modal-title">Enviar tarea</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form enctype="multipart/form-data">
               <div class="modal-body">
                  <div class="form-group">
                     <label for="note">Nota para el profesor:</label>
                     <textarea id="note" class="form-control" name="note"></textarea>
                  </div>
                  <p class="toTeacher text-info font-bree">Archivos para enviar al profesor</p>
                  <div class="container">
                     <button type="button" class="btn btn-primary mx-auto d-block addFile">Agregar archivo</button>
                  </div>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  <button type="submit" class="btn btn-primary">Enviar</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
   ?>
</body>

</html>