<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Mensajes";
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>

   <div class="container mt-5 pb-5">
      <div class="mb-2">
         <div class="btn-group">
            <button id="newMessageBtn" class="btn btn-secondary btn-sm" type="button">
               Nuevo Mensaje
            </button>
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu">
               <h6 class="dropdown-header">Mensajes:</h6>
               <a data-option="inbound" class="dropdown-item messageOption active" href="#">Recibidos <span class="badge badge-pill badge-info unreadMessages"><?= $teacher->unreadMessages() ?></span></a>
               <a data-option="outbound" class="dropdown-item messageOption" href="#">Enviados</a>
            </div>
         </div>
      </div>
      <div class="row shadow-lg inbox">
         <div class="col-12 col-md-4 p-0 overflow-auto custom-scroll">
            <!-- Messages list -->
            <div id="messages" class="col h-100 p-0">
               <div class="d-flex justify-content-center align-items-center h-100 font-bree">
                  Cargando...
               </div>
            </div>
         </div>
         <!-- View Message -->
         <div id="message" class="col-12 col-md-8 align-self-start bg-gradient-light bg-light overflow-auto custom-scroll border-inbox border-secondary">
            <div class="d-flex justify-content-center align-items-center h-100 font-bree">
               Seleccione un mensaje
            </div>
         </div>

      </div>
   </div>

   <div id="respondModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="dialog">
         <input type="hidden" name="id_message">
         <div class="modal-content">
            <div class="modal-header bg-primary">
               <h5 class="modal-title">Responder a:</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form id="respondForm">
               <div class="modal-body">
                  <div class="form-group row">
                     <label for="respondSubject" class="col-2 col-form-label">Asunto:</label>
                     <div class="col">
                        <input type="text" readonly class="form-control-plaintext" id="respondSubject" value="">
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="respondMessage">Mensaje:</label>
                     <textarea id="respondMessage" class="form-control" name="message"></textarea>
                     <div class="invalid-feedback">Escriba un mensaje</div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  <button type="submit" class="btn btn-primary">Responder</button>
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
               <h5 class="modal-title">Mensaje nuevo</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body custom-scroll">
               <?php
               $tableStudentsCheckbox = true;
               Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
               Route::includeFile('/foro/profesor/includes/tables/tableStudents.php');
               ?>
            </div>
            <form enctype="multipart/form-data">
               <div class="modal-body">

                  <div class="form hidden">
                     <p class="studentsAmount text-info font-bree"></p>
                     <div class="form-group row">
                        <label for="newTitle" class="col-form-label col-md-2">Titulo:</label>
                        <div class="col-md-10">
                           <input id="newTitle" class="form-control" type="text" name="title">
                        </div>
                     </div>
                     <div class="form-group row">
                        <label for="newSubject" class="col-form-label col-md-2">Asunto:</label>
                        <div class="col-md-10">
                           <input id="newSubject" class="form-control" type="text" name="subject">
                        </div>
                     </div>
                     <div class="form-group">
                        <label for="newMessage">Mensaje:</label>
                        <textarea id="newMessage" class="form-control" name="message"></textarea>
                     </div>
                     <div class="container">
                        <button type="button" class="btn btn-primary mx-auto d-block addFile">Agregar archivo</button>
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-primary form hidden back">Atras</button>
                  <button type="button" class="btn btn-secondary closeModal">Cerrar</button>
                  <button type="submit" class="btn btn-primary form hidden">Enviar</button>
               </div>
            </form>
         </div>
      </div>
   </div>

   <div id="modalAlert" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content bg-danger">
            <div class="modal-body d-flex justify-content-between">
               <p class="mb-0">Debe de seleccionar al menos uno</p>
               <button type="button" class="close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
         </div>
      </div>
   </div>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
   ?>


</body>

</html>