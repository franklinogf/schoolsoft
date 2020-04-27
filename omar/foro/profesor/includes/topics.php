<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Controllers\Teacher;
use Classes\Server;
use Classes\Session;

Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['topicsByClass'])) {
   $class = $_POST['topicsByClass'];  
   $data = $teacher->topicsByClass($class);

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
