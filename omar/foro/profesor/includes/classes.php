<?php
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

require_once '../../../app.php';

if (!isset($_SESSION['logged'])) {
   Route::redirect();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

  if (isset($_POST['studentsByClass'])){      
      $student = new Student;     
      if($data = $student->findByClass($_POST['studentsByClass']) ){
         $array = [
            'response'=>true,
            'data'=> $data
         ];
      }else{
         $array = ['response'=>false];
      }
      echo Util::toJson($array);

   }else if(isset($_POST['studentByPK'])){
      $student = new Student;     
      if($data = $student->findPK($_POST['studentByPK']) ){
         $array = [
            'response'=>true,
            'data'=> $data
         ];
      }else{
         $array = ['response'=>false];
      }
      echo Util::toJson($array);
   }
} else {
   Route::error();
}
