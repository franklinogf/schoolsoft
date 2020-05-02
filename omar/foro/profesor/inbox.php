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
   <div class="container-lg mt-lg-5  px-0">

   </div>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
   ?>

</body>

</html>