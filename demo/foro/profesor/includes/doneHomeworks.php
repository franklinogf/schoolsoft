<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Homework;
use App\Models\Student;
use App\Models\Teacher;
use Classes\File;
use Classes\Util;
use Classes\Server;
use Classes\Session;

Server::is_post();

if (isset($_POST['homeworksByClass'])) {
   $class = $_POST['homeworksByClass'];
   $teacher = Teacher::findOrFail(Session::id());
   if ($data = $teacher->homeworks()->ofClass($class)->get()) {
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
   $hw = Homework::query()->with('doneHomeworks.student')->findOrFail($id_homework);
   $data = [];
   $doneHws = $hw->doneHomeworks;
   foreach ($doneHws as $key => $doneHw) {
      $student = $doneHw->student;
      $data[$key]['id'] = $doneHw->id;
      $data[$key]['nombre'] = $student->fullName;
      $data[$key]['nota'] = $doneHw->nota;
      $data[$key]['fecha'] = $doneHw->fecha->format('d F Y');
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
