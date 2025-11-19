<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Util;
use Classes\Controllers\Teacher;
use Classes\Controllers\Topic;
use Classes\Route;
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
}elseif(isset($_POST['insertTopic'])){  
  
   $topic = new Topic(); 
   $topic->creador_id = $teacher->id;
   $topic->titulo = $_POST['title'];
   $topic->descripcion = $_POST['description'];
   $topic->curso = $_POST['class'];
   $topic->tipo = 'p';
   $topic->estado = $_POST['state'];
   $topic->desde = $_POST['untilDate'];
   $topic->year = $topic->info('year');
   $topic->fecha = Util::date('Y-m-d');
   $topic->hora = Util::time();
   $topic->save();

   Route::redirect('/profesor/topics.php');
}
