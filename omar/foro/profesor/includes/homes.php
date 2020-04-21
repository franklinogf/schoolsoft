<?php

use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

require_once '../../../app.php';

if (!isset($_SESSION['logged'])) {
   Route::redirect('/foro');
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

   if (isset($_POST['changeStudentUser'])) {
      $id_student = $_POST['id_student'];

      $student = new Student($id_student);
      $student->usuario = $_POST['username'];    
     
      if ($_POST['password'] !== '') {
         $student->clave = $_POST['password'];
      }
      $student->save();
      // Util::dump($file->file);


      Route::redirect('/foro/profesor/home.php');

   }else if (isset($_POST['checkUser'])){      
      $student = new Student;
      $teacher = new Teacher;
      if($student->findByUser($_POST['checkUser']) || $teacher->findByUser($_POST['checkUser'])){
         $array = ['response'=>true];
      }else{
         $array = ['response'=>false];
      }
      echo Util::toJson($array);
   }
} else {
   Route::error();
}
