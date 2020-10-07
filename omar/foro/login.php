<?php
require_once '../app.php';

use Classes\Route;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Session;

if (Session::is_logged(false)) {
   Route::redirect('/' . Session::type());
}

/* -------------------------------------------------------------------------- */
/*                           DATABASE MODIFICATIONS                           */
/* -------------------------------------------------------------------------- */

DB::table("t_mensajes_archivos")->create("
`id` INT NOT NULL AUTO_INCREMENT,
`nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
`mensaje_code` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)"
);

DB::table("tareas_enviadas")->alter("DROP INDEX `id_tarea`, ADD INDEX `id_tarea` (`id_tarea`) USING BTREE");

DB::table("t_mensajes_links")->create("
`id` INT NOT NULL AUTO_INCREMENT ,
`link` TEXT NOT NULL ,
`nombre` VARCHAR(150) NULL ,
`mensaje_code` INT NOT NULL,
 PRIMARY KEY (`id`)");
DB::table('T_archivos')->alter("RENAME TO t_archivos");
DB::table('T_tareas_archivos')->alter("RENAME TO t_tareas_archivos");

DB::table('T_examenes')->alter("
ADD `hora_final` TIME NOT NULL AFTER `hora`,
ADD `desc1` CHAR(2) NOT NULL DEFAULT 'no' AFTER `activo`,
ADD `desc1_1` TEXT NULL DEFAULT NULL AFTER `desc1`,
ADD `desc2` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc1_1`,
ADD `desc2_1` TEXT NULL DEFAULT NULL AFTER `desc2`,
ADD `desc3` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc2_1`,
ADD `desc3_1` TEXT NULL DEFAULT NULL AFTER `desc3`,
ADD `desc4` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc3_1`,
ADD `desc4_1` TEXT NULL DEFAULT NULL AFTER `desc4`,
ADD `desc5` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc4_1`,
ADD `desc5_1` TEXT NULL DEFAULT NULL AFTER `desc5`
");

/* -------------------------------------------------------------------------- */
/*                         END DATABASE MODIFICATIONS                         */
/* -------------------------------------------------------------------------- */

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title>Foro - Iniciar Sesión</title>
   <link rel="icon" href="<?= School::logo() ?>" />
   <?php
   Route::css("/css/main-bootstrap.css");
   Route::css("/css/main.css", true);
   Route::css("/css/login.css",true);
   ?>
</head>

<body class="text-center">
   <img class="mb-4 img-fluid" src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGIN_LOGO_SIZE ?>">
   <form class="form-signin" method='POST' action="<?= Route::url('/foro/includes/login.php') ?>">
      <h1 class="h3 mb-3 font-weight-normal ">Iniciar Sesión</h1>
      <?php if (Session::get('errorLogin')) : ?>
         <div class="alert alert-danger animated zoomIn" role="alert">
            <strong>Error!</strong> <?= Session::get('errorLogin', true) ?>
         </div>
      <?php endif ?>
      <label for="username" class="sr-only">Usuario:</label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-user fa-sm"></i></div>
         </div>
         <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" autofocus>
      </div>

      <label for="password" class="sr-only">Clave:</label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-key fa-sm"></i></div>
         </div>
         <input type="password" id="password" name="password" class="form-control mb-0" placeholder="Clave">
      </div>

      <button class="btn btn-md btn-primary btn-block mt-2" type="submit">Continuar</button>
      <a class="btn btn-sm btn-secondary btn-block mt-2" href="<?= Route::url('') ?>">Pagina Principal</a>
      <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
   </form>
   <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
   <?php

   Route::js('/js/app.js', true);
   Route::js('/js/login.js',true);
   ?>
</body>

</html>