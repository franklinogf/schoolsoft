<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\VirtualClass;
use App\Models\VirtualClassAttendance;
use Classes\Util;
use Classes\Server;
use Classes\Session;


Server::is_post();
$student = Student::findOrFail(Session::id());

if (isset($_POST['find'])) {

   $virtualClass = VirtualClass::query()
      ->active()
      ->ofClass($_POST['find'])
      ->where(['id_profesor' =>  $_POST['teacherId']])->first();

   if ($virtualClass) {
      $array = [
         'response' => true,
         'data' => [
            "id" => $virtualClass->id,
            'link' => $virtualClass->link,
            'title' => $virtualClass->titulo,
            'date' => $virtualClass->fecha,
            'time' => $virtualClass->hora,
         ]
      ];
   } else {
      $array = [
         'response' => false
      ];
   }

   echo Util::toJson($array);
} else if (isset($_POST['asis'])) {
   $virtualAsis = VirtualClassAttendance::query()->where([
      "id_virtual" => $_POST['asis'],
      "ss_estudiante" => $student->ss
   ])->exists();

   if (!$virtualAsis) {
      VirtualClassAttendance::query()->create([
         "id_virtual" => $_POST['asis'],
         "ss_estudiante" => $student->ss,
         "fecha" => date('Y-m-d'),
         "hora" => date('H:i:s'),
         "year" => Admin::primaryAdmin()->year(),
      ]);
   }
}
