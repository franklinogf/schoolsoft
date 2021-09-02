<?php
require_once '../../../../app.php';

use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['homeworksByClass'])) {
   $class = $_POST['homeworksByClass'];

   $data = $teacher->homeworks($class,false);

   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
