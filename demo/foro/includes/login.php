<?php

use App\Models\Student;
use App\Models\Teacher;
use Classes\Route;

include '../../app.php';


if ($_SERVER["REQUEST_METHOD"] == 'POST') {

   $location = null;
   $id = null;

   $teacherUser =  Teacher::where([
      ['usuario', $_POST['username']],
      ['clave', $_POST['password']]
   ])->first();


   if ($teacherUser) {
      $location = 'foro/profesor/';
      $user = $teacherUser;
      $id = $teacherUser->id;
   } else {

      $studentUser =  Student::where([
         ['usuario', $_POST['username']],
         ['clave', $_POST['password']]
      ])->first();

      if ($studentUser) {
         $location = 'foro/estudiante/';
         $user = $studentUser;
         $id = $studentUser->mt;
      }
   }


   if ($user && $location) {

      $_SESSION['logged'] = [
         'acronym' => __SCHOOL_ACRONYM,
         'location' => 'foro',
         "user" => ['id' => $id],
      ];
      $_SESSION['start'] = time();
      $_SESSION['id1'] = $id;
      $_SESSION['usua1'] = $user->usuario;

      Route::redirect($location, false);
   } else {
      $_SESSION['errorLogin'] = __("No coincide con los datos, intentelo otra vez");

      Route::redirect();
   }
} else {
   Route::error();
}
