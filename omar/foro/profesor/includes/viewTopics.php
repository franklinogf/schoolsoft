<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Controllers\Topic;


Server::is_post();

if (isset($_POST['editTopic'])) {
   $id_topic = $_POST['id_topic'];

   $topic = new Topic($id_topic);
   $topic->titulo = $_POST['title'];
   $topic->descripcion = $_POST['description'];
   $topic->estado = $_POST['state'];
   $topic->desde = $_POST['untilDate'];
   $topic->save();

   Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
}