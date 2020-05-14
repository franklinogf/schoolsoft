<?php

require_once '../../../app.php';

use Classes\File;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Util;

Session::is_logged();
Server::is_post();

$student = new Student(Session::id());

$student->nombre = $_POST['name'];
$student->apellidos = $_POST['lastName'];
$student->email = $_POST['email'];
$student->cel = $_POST['cellPhone'];
$student->comp = $_POST['cellCompany'];

if ($_POST['password'] !== '') {
   $student->clave = $_POST['password'];
}

$file = new File('picture');
if ($file->amount > 0) {
   $newName = $student->mt . '.jpg';
   $student->imagen = $newName;
   $file::upload($file->files, __STUDENT_PROFILE_PICTURE_PATH,$newName);
}

$student->save();

Route::redirect('/estudiante/profile.php');
