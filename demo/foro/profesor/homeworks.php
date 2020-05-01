<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\File;
use Classes\Util;

Session::is_logged();

$teacher = new Teacher(Session::id());
$homeworks = $teacher->homeworks();
$classes = $teacher->classes();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <title>Foro - Tareas</title>
   <?php  
   Route::includeFile('/foro/profesor/includes/layouts/links.php');
   ?>

</head>

<body class='pb-5'>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>
   <div class="container-lg mt-5 px-0">

      <h1 class="text-center mb-3">Mis Tareas</h1>

      <div class="alert alert-warning mx-auto" role="alert">
         Si el PDF es muy grande entre a esta <a class="alert-link" href="https://www.ilovepdf.com/es/comprimir_pdf" target="_blank">pagina</a> para comprimir.
      </div>

      <form method="POST" action="<?= Route::url('/foro/profesor/includes/homeworks.php') ?>" enctype="multipart/form-data">
         <div class="jumbotron py-4">
            <div class="form-group">
               <label for="title">Titulo</label>
               <input class="form-control" type="text" name="title" id="title" required>
            </div>

            <div class="form-group mt-2">
               <label for="description">Descripción</label>
               <textarea class="form-control" name="description" id="description"></textarea>
            </div>

            <div class="form-row">
               <div class="col-6 col-lg-4 mt-2">
                  <label for="class">Curso</label>
                  <select class="form-control" name="class" id="class">
                     <?php foreach ($classes as $class) : ?>
                        <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                     <?php endforeach ?>
                  </select>
               </div>
               <div class="col-12 col-lg-4 mt-2">
                  <label for="sinceDate">Fecha Inicial</label>
                  <input class="form-control" type="date" name="sinceDate" id="sinceDate" value="<?= Util::date('Y-m-d') ?>" required>
                  <small class="form-text text-info">La tarea estará disponible en esta fecha</small>
               </div>
               <div class="col-12 col-lg-4 mt-2">
                  <label for="untilDate">Fecha Final</label>
                  <input class="form-control" type="date" name="untilDate" id="untilDate">
                  <small class="form-text text-info">La tarea dejara de estar disponible despues de esta fecha</small>
               </div>
            </div>

            <div class="form-group mt-2">
               <label class="d-block">Tarea disponible?</label>
               <div class="custom-control custom-radio custom-control-inline">
                  <input class="custom-control-input" type="radio" name="state" id="radio1" value="si" required>
                  <label class="custom-control-label" for="radio1">Si</label>
               </div>
               <div class="custom-control custom-radio custom-control-inline">
                  <input class="custom-control-input" type="radio" name="state" id="radio2" value="no">
                  <label class="custom-control-label" for="radio2">No</label>
               </div>
            </div>

            <label>Links</label>
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
               <button type='button' class="btn btn-secondary addFile d-block mx-auto">Agregar archivo</button>
            </div>

            <div class="form-group mb-0 mt-5">
               <button id="homeworkFormBtn" type='submit' class="btn btn-primary btn-block" name="addHomework">Guardar</button>
            </div>

         </div>
      </form>
      <div class="alert alert-warning mx-auto" role="alert">
         Al guardar la tarea se le enviara un correo a los padres.
      </div>
   </div>
   <!-- homework list -->
   <div class="container">

      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">

         <?php foreach ($homeworks as $homework) : ?>
            <div class="col mb-4 homework <?= $homework->id_documento ?>">
               <div class="card">
                  <h6 class="card-header bg-primary"><?= $homework->curso ?></h6>
                  <div class="card-body">
                     <h5 class="card-title"><?= $homework->titulo ?></h5>
                     <p class="card-text"><?= $homework->descripcion ?></p>
                  </div>
                  <div class="card-footer bg-transparent">
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
                              <a data-file-id="<?= $file->id ?>" href="#" target="_blank" data-toggle="tooltip" title='<?= $file->nombre ?>' class="btn btn-outline-secondary btn-sm"><?= File::faIcon(File::extension($file->nombre))." Archivo " . ($i + 1) ?> </a>
                           <?php endforeach ?>
                        </div>
                     <?php endif ?>

                  </div>
                  <div class="card-footer text-center bg-transparent">
                     <div class="row row-cols-2">
                        <div class="col">
                           <button data-homework-id="<?= $homework->id_documento ?>" data-toggle="tooltip" title="Editar" class="btn btn-outline-primary btn-sm btn-block editHomework"><i class="fas fa-edit"></i></button>
                        </div>
                        <div class="col">
                           <button data-homework-id="<?= $homework->id_documento ?>" data-toggle="tooltip" title="Eliminar" class="btn btn-outline-danger btn-sm btn-block delHomework"><i class="fas fa-trash-alt"></i></button>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer bg-secondary d-flex justify-content-lg-between">
                     <small class="text-primary blend-screen"><?= Util::formatDate($homework->fec_in, true) ?></small>
                     <small class="text-primary blend-screen"><?= $homework->hora ?></small>
                  </div>
               </div>

            </div>
         <?php endforeach ?>


      </div> <!-- end row -->

   </div><!-- end container -->

   <?php
   Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
   ?>
</body>

</html>