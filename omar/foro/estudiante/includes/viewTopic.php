<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Topic;
use Classes\Controllers\Student;


Server::is_post();

if (isset($_POST['newComment'])) {

   $id_topic = $_POST['newComment'];  
   $comment = $_POST['comment'];
   
   $topic = new Topic($id_topic);   
   
   $topic->newComment(Session::id(), $comment, 'e');
   $student = new Student(Session::id());
   $array = [
      'fullName'=> $student->fullName(),
      'profilePicture'=> $student->profilePicture(),
      'date'=> Util::formatDate(Util::date(), true, true),
      'time'=> Util::formatTime(Util::time())
   ];
   echo Util::toJson($array);
}