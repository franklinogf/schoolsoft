<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Teacher;
use Classes\Util;
use Classes\Server;


Server::is_post();

if (isset($_POST['teacherById'])) {
   $id_teacher = $_POST['teacherById'];
   $teacher = Teacher::find($id_teacher);
   if ($teacher) {
      $array = [
         'response' => true,
         'data' => [
            'nombre' => $teacher->fullName,
            'grado' => $teacher->grado,
            'genero' => $teacher->genero,
            'foto' => $teacher->profilePicture,
            'email' => $teacher->email1
         ]
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
