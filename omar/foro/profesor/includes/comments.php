<?php

use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Controllers\Teacher;
use Classes\Server;
use Classes\Session;

require_once '../../../app.php';

if (!isset($_SESSION['logged'])) {
   Route::redirect();
}

Server::is_post();


$teacher = new Teacher(Session::id());

$id_topic = $_POST['id_topic'];
$comment = $_POST['comment'];

$topic = new Topic($id_topic);


$topic->newComment($teacher->id, $comment, 'p');

Route::redirect('/profesor/topic.php?id=' . $id_topic);
