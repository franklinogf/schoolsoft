<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;


Session::is_logged();
$DataTable = true;
$student = new Student(Session::id());
$lang = new Lang([
   ["Mensajes", "Inbox"],
   ["Nuevo mensaje", "New message"],
   ["Recibidos", "Received"],
   ["Enviados", "Sent"],
   ["Cargando", "Loading"],
   ["Seleccione un mensaje", "Select a message"],
   ["Responder a", "Answer to"],
   ["Asunto", "Subject"],
   ["Mensaje", "Message"],
   ["Escriba un mensaje", "Compose a message"],
   ["Cerrar", "Close"],
   ["Responder", "Answer back"],
   ["Mensaje nuevo", "New message"],
   ["Titulo", "Title"],
   ["Asunto", "Subject"],
   ["Mensaje", "Message"],
   ["Agregar Link", "Add link"],
   ["Agregar archivo", "Add file"],
   ["Atrás", "Go back"],
   ["Cerrar", "Close"],
   ["Enviar", "Send"],
   ["Debe de seleccionar al menos uno", "You must select at least one"],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = $lang->translation("Mensajes");
   Route::includeFile('/foro/estudiante/includes/layouts/header.php');
   ?>
</head>

<body>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>

   <div class="container mt-5 pb-5">
      <div class="mb-2">
         <div class="btn-group">
            <button id="newMessageBtn" class="btn btn-secondary btn-sm" type="button">
               <?= $lang->translation("Nuevo mensaje") ?>
            </button>
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu">
               <h6 class="dropdown-header"><?= $lang->translation("Mensajes") ?>:</h6>
               <a data-option="inbound" class="dropdown-item messageOption active" href="#"><?= $lang->translation("Recibidos") ?> <span class="badge badge-pill badge-info unreadMessages"><?= $student->unreadMessages() ?></span></a>
               <a data-option="outbound" class="dropdown-item messageOption" href="#"><?= $lang->translation("Enviados") ?></a>
            </div>
         </div>
      </div>
      <div class="row shadow-lg inbox">
         <div class="col-12 col-md-4 p-0 overflow-auto custom-scroll">
            <!-- Messages list -->
            <div id="messages" class="col h-100 p-0">
               <div class="d-flex justify-content-center align-items-center h-100 font-bree">
                  <?= $lang->translation("Cargando") ?>...
               </div>
            </div>
         </div>
         <!-- View Message -->
         <div id="message" class="col-12 col-md-8 align-self-start bg-gradient-light bg-light overflow-auto custom-scroll border-inbox border-secondary">
            <div class="d-flex justify-content-center align-items-center h-100 font-bree">
               <?= $lang->translation("Seleccione un mensaje") ?>
            </div>
         </div>

      </div>
   </div>

   <div id="respondModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="dialog">
         <input type="hidden" name="id_message">
         <div class="modal-content">
            <div class="modal-header bg-primary">
               <h5 class="modal-title"><?= $lang->translation("Responder a") ?>:</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form id="respondForm">
               <div class="modal-body">
                  <div class="form-group row">
                     <label for="respondSubject" class="col-2 col-form-label"><?= $lang->translation("Asunto") ?>:</label>
                     <div class="col">
                        <input type="text" readonly class="form-control-plaintext" id="respondSubject" value="">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="respondMessage"><?= $lang->translation("Mensaje") ?>:</label>
                     <textarea id="respondMessage" class="form-control" name="message"></textarea>
                     <div class="invalid-feedback"><?= $lang->translation("Escriba un mensaje") ?></div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                  <button type="submit" class="btn btn-primary"><?= $lang->translation("Responder") ?></button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <div id="newMessageModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="dialog">
         <input type="hidden" name="id_message">
         <div class="modal-content">
            <div class="modal-header bg-primary">
               <h5 class="modal-title"><?= $lang->translation("Mensaje nuevo") ?></h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body custom-scroll">
               <?php
               Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
               ?>
            </div>
            <form enctype="multipart/form-data">
               <div class="modal-body custom-scroll">

                  <div class="form hidden">
                     <p class="studentsAmount text-info font-bree"></p>
                     <div class="form-group row">
                        <label for="newTitle" class="col-form-label col-md-2"><?= $lang->translation("Titulo") ?>:</label>
                        <div class="col-md-10">
                           <input id="newTitle" class="form-control" type="text" name="title" required>
                        </div>
                     </div>
                     <div class="form-group row">
                        <label for="newSubject" class="col-form-label col-md-2"><?= $lang->translation("Asunto") ?>:</label>
                        <div class="col-md-10">
                           <input id="newSubject" class="form-control" type="text" name="subject" required>
                        </div>
                     </div>
                     <div class="form-group">
                        <label for="newMessage"><?= $lang->translation("Mensaje") ?>:</label>
                        <textarea id="newMessage" class="form-control" name="message" require></textarea>
                     </div>
                     <div class="container">
                        <button id="addLink" class="btn btn-secondary mb-3"><?= $lang->translation("Agregar Link") ?></button>
                     </div>
                     <div class="container">
                        <button type="button" class="btn btn-primary mx-auto d-block addFile"><?= $lang->translation("Agregar archivo") ?></button>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-primary form hidden back"><?= $lang->translation("Atrás") ?></button>
                  <button type="button" class="btn btn-secondary closeModal"><?= $lang->translation("Cerrar") ?></button>
                  <button type="submit" class="btn btn-primary form hidden"><?= $lang->translation("Enviar") ?></button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>

</body>

</html>