<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Server;

Server::is_post();


if (isset($_POST['changeStudentUser'])) {
   $id_student = $_POST['id_student'];

   $student = new Student($id_student);
   $student->usuario = $_POST['username'];

   if ($_POST['password'] !== '') {
      $student->clave = $_POST['password'];
   }
   $student->save();
   // Util::dump($file->file);


   Route::redirect('/profesor/home.php');
} else if (isset($_POST['checkUser'])) {
   $student = new Student;
   $teacher = new Teacher;
   if ($student->findByUser($_POST['checkUser']) || $teacher->findByUser($_POST['checkUser'])) {
      $array = ['response' => true];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
