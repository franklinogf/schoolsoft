<?php

namespace Classes;

use Classes\Controllers\Parents;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

class Login
{
   private static $errorLoginMessage = "No coincide con los datos, intentelo otra vez";

   private static function startSession()
   {
      $_SESSION["start"] = time(); 
   }

   public static function login($request, $location)
   {
      self::startSession();
      self::{$location}($request);
   }


   private static function parents($request)
   {
      $parents = new Parents();
      if ($parents->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "parents",
            "user" => ['id' => $parents->id],
         ];
         Route::redirect('/');
      } else {
         $_SESSION['errorLogin'] = self::$errorLoginMessage;

         Route::redirect();
      }
   }
   private static function admin($request)
   {
      $school = new School();
      if ($school->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "admin",
            "user" => ['id' => $school->usuario],
         ];
         $_SESSION['id1'] = $school->id;
         $_SESSION['usua1'] = $school->usuario;
         Route::redirect('/');
      } else {
         $_SESSION['errorLogin'] = self::$errorLoginMessage;

         Route::redirect();
      }
   }
   private static function cafeteria($request)
   {
      $school = new School();
      if ($school->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "cafeteria",
            "user" => ['id' => $school->usuario],
         ];
         $_SESSION['id1'] = $school->id;
         $_SESSION['usua1'] = $school->usuario;
         Route::redirect('/menu.php');
      } else {
         $_SESSION['errorLogin'] = self::$errorLoginMessage;

         Route::redirect();
      }
   }
   private static function regiweb($request)
   {
      $teacher = new Teacher();
      if ($teacher->login(trim($request['username']), trim($request['password']))->logged === true) {

         $_SESSION['logged'] = [
            'location' => "regiweb",
            "user" => ['id' => $teacher->id],
         ];
         Route::redirect('/');
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
