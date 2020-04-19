<?php

namespace Classes;

use Classes\Util;
use Classes\Controllers\Teacher;
use Classes\Controllers\Student;

class Login
{

   public static function login($request,$location)
   {
      if($location === 'foro'){

         self::foro($request);
      }      
      
   }

   private function foro($request){
      $teacher = new Teacher();
      if ($teacher->login($request['username'], $request['password'])->logged === true) {
         echo 'profesor';
         $_SESSION['logged'] = [           
            "user" => ['id'=>$teacher->id],
            'type'=> 'profesor'
         ];
        Route::redirect('/foro/profesor');     
      } else {
         echo 'estudiante';
         $student = new Student();
         if ($student->login($request['username'], $request['password'])->logged === true) {
            $_SESSION['logged'] = [               
               "user" => ['id'=>$student->mt],
               'type'=> 'estudiante'
            ];
            Route::redirect('/foro/estudiante');
         }else{
            $_SESSION['errorLogin'] = 'No coincide con los datos, intentelo otra vez';
            
            Route::redirect('/foro');
         }
      }
   }
}
