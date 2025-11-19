<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Util;
use Classes\Controllers\Topic;
use Classes\Server;

Server::is_post();

if (isset($_POST['topicsByClass'])) {
   $topic = new Topic();
   $class = $_POST['topicsByClass'];  
   $data = $topic->ByClass($class);

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
