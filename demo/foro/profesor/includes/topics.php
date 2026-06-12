<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Foro\Topic;
use App\Models\Teacher;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;

Server::is_post();
$teacher = Teacher::find(Session::id());

if (isset($_POST['topicsByClass'])) {
   $class = $_POST['topicsByClass'];
   $data = $teacher->topics()->byClass($class)->get();

   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
} elseif (isset($_POST['insertTopic'])) {

   $teacher->topics()->create([
      'titulo' => $_POST['title'],
      'descripcion' => $_POST['description'],
      'curso' => $_POST['class'],
      'estado' => $_POST['state'],
      'desde' => $_POST['untilDate'],
      'fecha' => date('Y-m-d'),
      'hora' => date('H:i:s'),
      'year' => Admin::primaryAdmin()->year()
   ]);

   Route::redirect('/profesor/topics.php');
}
