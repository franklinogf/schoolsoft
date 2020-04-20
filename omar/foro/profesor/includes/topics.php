<?php

use Classes\Route;
use Classes\Controllers\Topic;


require_once '../../../app.php';

if(!isset($_SESSION['logged'])){
   Route::redirect('/foro');
}

$id_topic = $_POST['id_topic'];

$topic = new Topic($id_topic);
$topic->titulo = $_POST['title'];
$topic->descripcion = $_POST['description'];
$topic->estado = $_POST['state'];
$topic->desde = $_POST['untilDate'];
$topic->save();
 


Route::redirect('/foro/profesor/topic.php?id='.$id_topic);
