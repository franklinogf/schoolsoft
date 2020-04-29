<?php

require_once '../../../app.php';

use Classes\File;
use Classes\Route;
use Classes\Controllers\Teacher;
use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();

$id_teacher = $_POST['id_teacher'];

$teacher = new Teacher($id_teacher);
$teacher->nombre = $_POST['name'];
$teacher->apellidos = $_POST['lastName'];
$teacher->email1 = $_POST['email1'];
$teacher->email2 = $_POST['email2'];

if ($_POST['password'] !== '') {
   $teacher->clave = $_POST['password'];
}


$file = new File('picture');

if ($file->amount > 0) {
   $newName = $teacher->id . '.jpg';
   $teacher->foto_name = $newName;
   $file::upload($file->files, __TEACHER_PROFILE_PICTURE_PATH,$newName);
}

$teacher->save();

Route::redirect('/profesor/profile.php');
