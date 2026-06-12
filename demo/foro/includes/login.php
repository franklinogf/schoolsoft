<?php

use App\Models\Student;
use App\Models\Teacher;
use Classes\Route;

include '../../app.php';


if ($_SERVER["REQUEST_METHOD"] == 'POST') {

   $location = null;

   $teacherUser =  Teacher::where([
      ['usuario', $_POST['username']],
      ['clave', $_POST['password']]
   ])->first();


   if ($teacherUser) {
      $location = 'foro/profesor/';
      $user = $teacherUser;
   } else {

      $studentUser =  Student::where([
         ['usuario', $_POST['username']],
         ['clave', $_POST['password']]
      ])->first();

      if ($studentUser) {
         $location = 'foro/estudiante/';
         $user = $studentUser;
      }
   }


   if ($user && $location) {

      $_SESSION['logged'] = [
         'acronym' => __SCHOOL_ACRONYM,
         'location' => 'foro',
         "user" => ['id' => $user->id],
      ];
      $_SESSION['start'] = time();
      $_SESSION['id1'] = $user->id;
      $_SESSION['usua1'] = $user->usuario;

      Route::redirect($location, false);
   } else {
      $_SESSION['errorLogin'] = __("No coincide con los datos, intentelo otra vez");

      Route::redirect();
   }
} else {
   Route::error();
}
