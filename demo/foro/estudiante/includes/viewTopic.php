<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Foro\Topic;
use App\Models\Foro\TopicComment;
use App\Models\Student;
use Classes\Util;
use Classes\Server;
use Classes\Session;


Server::is_post();

if (isset($_POST['newComment'])) {

   $id_topic = $_POST['newComment'];
   $comment = $_POST['comment'];

   $topic = Topic::findOrFail($id_topic);

   $student = Student::findOrFail(Session::id());

   $topic->comments()->create([
      'creador_id' => $student->id,
      'tipo' => TopicComment::STUDENT_TYPE,
      'descripcion' => $comment,
      'fecha' => date('Y-m-d'),
      'hora' => date('H:i:s'),
      'year' => Admin::primaryAdmin()->year()
   ]);

   $array = [
      'fullName' => $student->fullName,
      'profilePicture' => $student->profilePicture,
      'date' => Util::formatDate(date('Y-m-d'), true, true),
      'time' => Util::formatTime(date('H:i:s'))
   ];

   echo Util::toJson($array);
}
