<?php

namespace Classes;

use Classes\Util;
use Classes\Controllers\Teacher;
use Classes\Controllers\Student;

class Login
{

   public static function login($request, $location)
   {
      if ($location === 'foro') {        
         // php 5 version
         (new self)->foro($request);
         // php 7 version 
         // self::foro($request);
      }
   }

   private function foro($request)
   {      
      $teacher = new Teacher();
      if ($teacher->login(trim($request['username']), trim($request['password']))->logged === true) {   
         
         $_SESSION['logged'] = [
            "user" => ['id' => $teacher->id],
            'type' => 'profesor'
         ];
         Route::redirect('/profesor');
      } else {         
         $student = new Student();
         if ($student->login(trim($request['username']), trim($request['password']))->logged === true) {
            $_SESSION['logged'] = [
               "user" => ['id' => $student->mt],
               'type' => 'estudiante'
            ];
            Route::redirect('/estudiante');
         } else {
            $_SESSION['errorLogin'] = 'No coincide con los datos, intentelo otra vez';

            Route::redirect();
         }
      }
   }
}
