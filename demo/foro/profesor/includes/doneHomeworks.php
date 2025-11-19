<?php
require_once __DIR__ . '/../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Controllers\Homework;

Server::is_post();

if (isset($_POST['homeworksByClass'])) {
   $class = $_POST['homeworksByClass'];
   $teacher = new Teacher(Session::id());
   if ($data = $teacher->homeworks($class, false)) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
} else if (isset($_POST['doneHomeworksByHomeworkId'])) {
   $id_homework = $_POST['doneHomeworksByHomeworkId'];
   $hw = new Homework($id_homework);
   $data = [];
   $doneHws = $hw->doneHomeworks();
   foreach ($doneHws as $key => $doneHw) {
      $student = new Student($doneHw->id_estudiante);
      $data[$key]['id'] = $doneHw->id;
      $data[$key]['nombre'] = $student->fullName();
      $data[$key]['nota'] = $doneHw->nota;
      $data[$key]['fecha'] = Util::formatDate($doneHw->fecha, true);
      $data[$key]['hora'] = Util::formatTime($doneHw->hora);
      if (property_exists($doneHw, 'archivos')) {
         foreach ($doneHw->archivos as $i => $file) {
            $data[$key]['archivos'][$i]['nombre'] = File::name($file->nombre, true);
            $data[$key]['archivos'][$i]['url'] = __STUDENT_HOMEWORKS_DIRECTORY_URL . $file->nombre;
            $data[$key]['archivos'][$i]['icon'] = File::faIcon(File::extension($file->nombre), 'lg');
         }
      }
   }
   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
