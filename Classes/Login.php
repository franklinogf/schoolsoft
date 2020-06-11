<?php

namespace Classes;

use Classes\Controllers\Teacher;
use Classes\Controllers\Student;

class Login
{
   private static $errorLoginMessage = "No coincide con los datos, intentelo otra vez";
   public static function login($request, $location)
   {
      self::{$location}($request);
      // if ($location === 'foro') {
      //    // php 5 version
      //    // (new self)->foro($request);
      //    // php 7 version 
      //    self::foro($request);
      // } else if ($location === 'regiweb') {         
      //    self::regiweb($request);
      // }
   }
   private static function regiweb($request)
   {
      $teacher = new Teacher();
      if ($teacher->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "regiweb",
            "user" => ['id' => $teacher->id],
            'type' => 'profesor'
         ];
         Route::redirect('/home.php');
      } else {
         $_SESSION['errorLogin'] = self::$errorLoginMessage;

         Route::redirect();
      }
   }
   private static function foro($request)
   {
      $teacher = new Teacher();
      if ($teacher->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "foro",
            "user" => ['id' => $teacher->id],
            'type' => 'profesor'
         ];
         Route::redirect('/profesor');
      } else {
         $student = new Student();
         if ($student->login(trim($request['username']), trim($request['password']))->logged === true) {
            $_SESSION['logged'] = [
               'location' => "foro",
               "user" => ['id' => $student->mt],
               'type' => 'estudiante'
            ];
            Route::redirect('/estudiante');
         } else {
            $_SESSION['errorLogin'] = self::$errorLoginMessage;

            Route::redirect();
         }
      }
   }
}
