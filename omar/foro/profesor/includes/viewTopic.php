<?php
require_once '../../../app.php';

use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Topic;


Server::is_post();

if (isset($_POST['topicById'])) {
   $topic_id = $_POST['topicById'];  
   $data = new Topic($topic_id);
   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}elseif (isset($_POST['editTopic'])) {
   $id_topic = $_POST['id_topic'];

   $topic = new Topic($id_topic);
   $topic->titulo = $_POST['title'];
   $topic->descripcion = $_POST['description'];
   $topic->estado = $_POST['state'];
   $topic->desde = $_POST['untilDate'];
   $topic->save();

   Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
}elseif (isset($_POST['newComment'])) {

   $id_topic = $_POST['newComment'];  
   $comment = $_POST['comment'];
   
   $topic = new Topic($id_topic);   
   
   $topic->newComment(Session::id(), $comment, 'p');
   $teacher = new Teacher(Session::id());
   $array = [
      'fullName'=> $teacher->fullName(),
      'profilePicture'=> $teacher->profilePicture(),
      'date'=> Util::formatDate(Util::date(), true, true),
      'time'=> Util::formatTime(Util::time())
   ];
   echo Util::toJson($array);
   // Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
}