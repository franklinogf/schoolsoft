<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
$homeworks = $teacher->homeworks();
$classes = $teacher->classes();
$lang = new Lang([
   ["Tareas", "Homeworks"],
   ["Si el PDF es muy pesado entre a esta", "If the PDF is to large, go to this"],
   ["pagina", "website"],
   ["para comprimir.", "to compress it."],
   ["Titulo", "Title"],
   ["Descripción", "Description"],
   ["Curso", "Class"],
   ["Fecha Inicial", "Initial date"],
   ["Fecha final", "Final date"],
   ["La tarea estará disponible en esta fecha", "The homework will be available on this date"],
   ["La tarea dejará de estar disponible después de esta fecha", "The homework will no longer be available after this date"],
   ["¿Recibir archivos de los estudiantes?", "Receive files from students?"],
   ["Si", "Yes"],
   ["Si: se puede recibir archivos de los estudiantes", "Yes: files can be received from students"],
   ["No: no se va a recibir archivos de los estudiantes", "No: files are not going to be received from students"],
   ["Links", "Links"],
   ["Link", "Link"],
   ["Agregar archivos", "Add files"],
   ["Guardar", "Save"],
   ["Al guardar la tarea se le enviara un correo a los padres.", "When saving the homework, an email will be sent to the parents."],
   ["Archivo", "File"],
   ["Sin fecha de finalización","No final date"]
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = $lang->translation("Tareas");
   Route::includeFile('/regiweb/includes/layouts/header.php');
   ?>
</head>

<body class='pb-5'>
   <?php
   Route::includeFile('/regiweb/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-5 px-0">

      <h1 class="text-center mb-3"><?= $lang->translation("Tareas") ?></h1>

      <div class="alert alert-warning mx-auto" role="alert">
         <?= $lang->translation("Si el PDF es muy pesado entre a esta") ?> <a class="alert-link" href="https://www.ilovepdf.com/es/comprimir_pdf" target="_blank"><?= $lang->translation("pagina") ?></a> <?= $lang->translation("para comprimir.") ?>
      </div>

      <form method="POST" action="<?= Route::url('/regiweb/options/homeworks/includes/index.php') ?>" enctype="multipart/form-data">
         <div class="jumbotron py-4">
            <div class="form-group">
               <label for="title"><?= $lang->translation("Titulo") ?></label>
               <input class="form-control" type="text" name="title" id="title" required>
            </div>

            <div class="form-group mt-2">
               <label for="description"><?= $lang->translation("Descripción") ?></label>
               <textarea class="form-control" name="description" id="description"></textarea>
            </div>

            <div class="form-row">
               <div class="col-6 col-lg-4 mt-2">
                  <label for="class"><?= $lang->translation("Curso") ?></label>
                  <select class="form-control" name="class" id="class">
                     <option value=""><?= $lang->translation("Seleccionar") ?></option>
                     <?php foreach ($classes as $class) : ?>
                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                     <?php endforeach ?>
                  </select>
               </div>
               <div class="col-12 col-lg-4 mt-2">
                  <label for="sinceDate"><?= $lang->translation("Fecha Inicial") ?></label>
                  <input class="form-control" type="date" name="sinceDate" id="sinceDate" min="<?= Util::date() ?>" value="<?= Util::date() ?>" required>
                  <small class="form-text text-info"><?= $lang->translation("La tarea estará disponible en esta fecha") ?></small>
               </div>
               <div class="col-12 col-lg-4 mt-2">
                  <label for="untilDate"><?= $lang->translation("Fecha Final") ?></label>
                  <input class="form-control" type="date" name="untilDate" id="untilDate" min="<?= Util::date() ?>">
                  <small class="form-text text-info"><?= $lang->translation("La tarea dejará de estar disponible después de esta fecha") ?></small>
               </div>
            </div>

            <div class="form-group mt-2">
               <label class="d-block"><?= $lang->translation("¿Recibir archivos de los estudiantes?") ?></label>
               <div class="custom-control custom-radio custom-control-inline">
                  <input class="custom-control-input" type="radio" name="state" id="radio1" value="si" required>
                  <label class="custom-control-label" for="radio1"><?= $lang->translation("Si") ?></label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input class="custom-control-input" type="radio" name="state" id="radio2" value="no">
                  <label class="custom-control-label" for="radio2">No</label>
               </div>
               <small class="form-text text-info"><?= $lang->translation("Si: se puede recibir archivos de los estudiantes") ?></small>
               <small class="form-text text-info"><?= $lang->translation("No: no se va a recibir archivos de los estudiantes") ?></small>
            </div>

            <label><?= $lang->translation("Links") ?></label>
            <div class="form-row">
               <div class="form-group col-12 col-lg-4">
                  <input id="link1" class="form-control" type="text" name="link1" placeholder="link 1">
               </div>
               <div class="form-group col-12 col-lg-4">
                  <input id="link2" class="form-control" type="text" name="link2" placeholder="link 2">
               </div>
               <div class="form-group col-12 col-lg-4">
                  <input id="link3" class="form-control" type="text" name="link3" placeholder="link 3">
               </div>
            </div>

            <div class="form-group my-3">
               <button type='button' class="btn btn-secondary addFile d-block mx-auto"><?= $lang->translation("Agregar archivos") ?></button>
            </div>

            <div class="form-group mb-0 mt-5">
               <button id="homeworkFormBtn" type='submit' class="btn btn-primary btn-block" name="addHomework"><?= $lang->translation("Guardar") ?></button>
            </div>

         </div>
      </form>
      <div class="alert alert-warning mx-auto" role="alert">
         <?= $lang->translation("Al guardar la tarea se le enviara un correo a los padres.") ?>
      </div>
   </div>
   <!-- homework list -->
   <div class="container">

      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">

         <?php foreach ($homeworks as $homework) : ?>
            <?php $expired = ($homework->fec_out >= Util::date() || $homework->fec_out === '0000-00-00' ? 'success' : 'warning'); ?>
            <div class="col mb-4 homework <?= $homework->id_documento ?>">
               <div class="card border-<?= $expired ?>">
                  <h6 class="card-header bg-gradient-primary bg-primary d-flex justify-content-between"><?= "$homework->curso - $homework->desc" ?> <i class="fas fa-circle text-<?= $expired ?>"></i></h6>
                  <div class="card-body ">
                     <h5 class="card-title"><?= $homework->titulo ?></h5>
                     <p class="card-text"><?= $homework->descripcion ?></p>
                  </div>
                  <div class="card-footer bg-white">
                     <small class="card-text text-warning d-block"><?= $homework->fec_out !== '0000-00-00' ? $lang->translation("Fecha final:") . " " . Util::formatDate($homework->fec_out, true) : $lang->translation("Sin fecha de finalización") ?></small>
                     <?php if (!empty($homework->lin1) || !empty($homework->lin2) || !empty($homework->lin3)) : ?>
                        <div class="btn-group btn-group-sm w-100 mt-2">
                           <?php for ($i = 1; $i <= 3; $i++) : ?>
                              <?php if ($homework->{"lin{$i}"} !== '') : ?>
                                 <a href="<?= $homework->{"lin{$i}"} ?>" target="_blank" data-toggle="tooltip" title='<?= $homework->{"lin{$i}"} ?>' class="btn btn-outline-info px-1"><i class="fas fa-external-link-alt"></i> <?= $lang->translation("Link") ?> <?= $i ?> </a>
                              <?php endif ?>
                           <?php endfor ?>
                        </div>
                     <?php endif ?>

                     <?php if (property_exists($homework, 'archivos')) : ?>
                        <div class="btn-group-vertical w-100 mt-2">
                           <?php foreach ($homework->archivos as $i => $file) : ?>
                              <a data-file-id="<?= $file->id ?>" href="<?= __TEACHER_HOMEWORKS_DIRECTORY_URL . $file->nombre ?>" data-toggle="tooltip" title='<?= File::name($file->nombre, true) ?>' class="btn btn-outline-dark btn-sm downloadFIle" download="<?= File::name($file->nombre, true) ?>"><?= File::faIcon(File::extension($file->nombre)) . " " . $lang->translation("Archivo") . " " . ($i + 1) ?> </a>
                           <?php endforeach ?>
                        </div>
                     <?php endif ?>

                  </div>
                  <div class="card-footer text-center bg-white">
                     <div class="row row-cols-2">
                        <div class="col">
                           <button data-homework-id="<?= $homework->id_documento ?>" data-toggle="tooltip" title="Editar" class="btn btn-info btn-sm btn-block editHomework"><i class="fas fa-edit"></i></button>
                        </div>
                        <div class="col">
                           <button data-homework-id="<?= $homework->id_documento ?>" data-toggle="tooltip" title="Eliminar" class="btn btn-danger btn-sm btn-block delHomework"><i class="fas fa-trash-alt"></i></button>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer bg-gradient-secondary bg-secondary d-flex justify-content-between">
                     <small class="text-primary blend-screen"><?= Util::formatDate($homework->fec_in, true) ?></small>
                     <small class="text-primary blend-screen"><?= Util::formatTime($homework->hora) ?></small>
                  </div>
               </div>

            </div>
         <?php endforeach ?>


      </div> <!-- end row -->

   </div><!-- end container -->

   <?php
   Route::includeFile('/includes/layouts/progressBar.php', true);
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>
</body>

</html>