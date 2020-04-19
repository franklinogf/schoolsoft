<?php

use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;

require_once '../../../app.php';

if(!isset($_SESSION['logged'])){
   Route::redirect('/foro');
}

$teacher = new Teacher($_SESSION['logged']['user']['id']);

$id_topic = $_POST['id_topic'];
$comment = $_POST['comment'];

$topic = new Topic($id_topic);


$topic->newComment($teacher->id,$comment,'p');

Route::redirect('/foro/profesor/topic.php?id='.$id_topic);
