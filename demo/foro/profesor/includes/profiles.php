<?php

require_once '../../../app.php';

use Classes\File;
use Classes\Route;
use Classes\Controllers\Teacher;
use Classes\Server;

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

if ($file = new File('picture')) {
   $fileName = $teacher->id . '.jpg';
   $teacher->foto_name = $fileName;

   $file->upload($fileName, __TEACHER_PROFILE_PICTURE);
}

$teacher->save();

Route::redirect('/profesor/profile.php');
