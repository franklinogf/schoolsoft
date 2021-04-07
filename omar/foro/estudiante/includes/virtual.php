<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;


Server::is_post();
$student = new Student(Session::id());

if (isset($_POST['find'])) {

   $virtualClass = DB::table('virtual')->where([
      ['curso', $_POST['find']],
      ['year', $student->info('year')],
      ['id_profesor', $_POST['teacherId']],
      ['activo', true],
   ])->first();

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
   $virtualAsis = DB::table("asistencia_virtual")->where([
      ["id_virtual", $_POST['asis']],
      ["ss_estudiante" , $student->ss],
      ["year", $student->info('year')]
   ])->first();

   if (!$virtualAsis) {
      DB::table("asistencia_virtual")->insert([
         "id_virtual" => $_POST['asis'],
         "ss_estudiante" => $student->ss,
         "fecha" => Util::date(),
         "hora" => Util::time(),
         "year" => $student->info('year'),
      ]);
   }
}
