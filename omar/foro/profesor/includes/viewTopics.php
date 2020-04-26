<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Controllers\Topic;
use Classes\Server;


Server::is_post();

$id_topic = $_POST['id_topic'];

$topic = new Topic($id_topic);
$topic->titulo = $_POST['title'];
$topic->descripcion = $_POST['description'];
$topic->estado = $_POST['state'];
$topic->desde = $_POST['untilDate'];
$topic->save();

Route::redirect('/profesor/viewTopic.php?id=' . $id_topic);
