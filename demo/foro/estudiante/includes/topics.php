<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Foro\Topic;
use Classes\Util;
use Classes\Server;

Server::is_post();

if (isset($_POST['topicsByClass'])) {
   $class = $_POST['topicsByClass'];
   $topic = Topic::query()->byClass($class)->active()->get();

   if ($topic) {
      $array = [
         'response' => true,
         'data' => $topic
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
