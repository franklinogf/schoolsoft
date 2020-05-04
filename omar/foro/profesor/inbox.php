<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <title>Foro - Mensajes</title>

   <?php
   Route::includeFile('/foro/profesor/includes/layouts/links.php');
   ?>

</head>

<body>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>

   <div class="container mt-5 pb-5">
      <div class="row shadow-lg inbox">
         <div class="col-12 col-md-4 p-0">
            <div id="inboxTitle" class="col p-2">
               <div class="btn-group">
                  <button class="btn btn-secondary btn-sm" type="button">
                     Nuevo Mensaje
                  </button>
                  <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  </button>
                  <div class="dropdown-menu">
                     <h6 class="dropdown-header">Mensajes:</h6>
                     <a data-option="inbound" class="dropdown-item message-option active" href="#">Recibidos</a>
                     <a data-option="outbound" class="dropdown-item message-option" href="#">Enviados</a>
                  </div>
               </div>
            </div>
            <!-- messages list -->
            <div id="messages" class="col h-100 p-0 overflow-auto custom-scroll">
               <div class="d-flex justify-content-center align-items-center h-100 font-bree">
                  Cargando...
               </div>
            </div>
         </div>

         <div id="message" class="col-12 col-md-8 align-self-start bg-gradient-light bg-light overflow-auto custom-scroll border-inbox border-secondary">
            <div class="d-flex justify-content-center align-items-center h-100 font-bree">
               Seleccione un mensaje
            </div>
         </div>

      </div>
   </div>


   <?php
   Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
   ?>
</body>

</html>