<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Student;
use App\Models\Teacher;
use Classes\Util;
use Classes\Route;
use Classes\Server;

Server::is_post();


if (isset($_POST['changeStudentUser'])) {
   $id_student = $_POST['id_student'];

   $student = Student::findOrFail($id_student);
   $student->usuario = $_POST['username'];

   if ($_POST['password'] !== '') {
      $student->clave = $_POST['password'];
   }
   $student->save();

   Route::redirect('/profesor/home.php');
} else if (isset($_POST['checkUser'])) {
   $studentHasUser = Student::where('usuario', $_POST['checkUser'])->exists();
   $teacherHasUser = Teacher::where('usuario', $_POST['checkUser'])->exists();

   if ($studentHasUser || $teacherHasUser) {
      $array = ['response' => true];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
