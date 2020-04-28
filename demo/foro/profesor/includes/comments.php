<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Server;
use Classes\Session;



Server::is_post();


$id_topic = $_POST['id_topic'];
$comment = $_POST['comment'];

$topic = new Topic($id_topic);


$topic->newComment(Session::id(), $comment, 'p');

Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
