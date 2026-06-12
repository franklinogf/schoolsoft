<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Student;
use Classes\Util;

use Classes\Server;


Server::is_post();

if (isset($_POST['studentsByClass'])) {
   $students = Student::query()->byClass($_POST['studentsByClass'])->get();
   if ($students->count() > 0) {
      $array = [
         'response' => true,
         'data' => $students
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
} else if (isset($_POST['studentByPK'])) {

   $student = Student::find($_POST['studentByPK']);

   $array = $student ? [
      'response' => true,
      'data' => [
         'id' => (__COSEY) ? $student->mt : $student->id,
         'nombre' => $student->fullName,
         'usuario' => $student->usuario,
         'grado' => $student->grado,
         'genero' => $student->genero,
         'foto' => $student->profilePicture,
         'fecha' => $student->fecha->format('Y-m-d'),
         'email' => $student->email

      ]
   ] : ['response' => false];

   echo Util::toJson($array);
}
