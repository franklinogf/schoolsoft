<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Controllers\Student;
use Classes\Server;


Server::is_post();

if (isset($_POST['homeworksByClass'])) {

   $student = new Student;

   if ($data = $student->findByClass($_POST['homeworksByClass'])) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
