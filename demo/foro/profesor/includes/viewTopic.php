<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Foro\Topic;
use App\Models\Foro\TopicComment;
use App\Models\Teacher;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;


Server::is_post();

if (isset($_POST['topicById'])) {
   $topic_id = $_POST['topicById'];
   $data = Topic::find($topic_id);
   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
} elseif (isset($_POST['editTopic'])) {
   $id_topic = $_POST['id_topic'];

   $topic = Topic::findOrFail($id_topic);

   $topic->update([
      'titulo' => $_POST['title'],
      'descripcion' => $_POST['description'],
      'estado' => $_POST['state'],
      'desde' => $_POST['untilDate'],
   ]);


   Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
} else if (isset($_POST['newComment'])) {

   $id_topic = $_POST['newComment'];
   $comment = $_POST['comment'];

   $topic = Topic::findOrFail($id_topic);

   $teacher = Teacher::findOrFail(Session::id());

   $topic->comments()->create([
      'creador_id' => $teacher->id,
      'tipo' => TopicComment::TEACHER_TYPE,
      'descripcion' => $comment,
      'fecha' => date('Y-m-d'),
      'hora' => date('H:i:s'),
      'year' => Admin::primaryAdmin()->year()
   ]);

   $array = [
      'fullName' => $teacher->fullName,
      'profilePicture' => $teacher->profilePicture,
      'date' => Util::formatDate(date('Y-m-d'), true, true),
      'time' => Util::formatTime(date('H:i:s'))
   ];
   echo Util::toJson($array);
} else if (isset($_POST['delTopic'])) {
   $id_topic = $_POST['delTopic'];
   $topic = Topic::findOrFail($id_topic);
   $topic->delete();
} else if (isset($_POST['delComment'])) {
   $id_comment = $_POST['delComment'];

   $comment = TopicComment::findOrFail($id_comment);

   $comment->delete();
}
