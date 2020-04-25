<?php
use Classes\Util;
use Classes\Route;
use Classes\Controllers\Student;


require_once '../../../app.php';

if (!isset($_SESSION['logged'])) {
   Route::redirect();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

  if (isset($_POST['homeworksByClass'])){   

      $student = new Student;   

      if($data = $student->findByClass($_POST['homeworksByClass']) ){
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
