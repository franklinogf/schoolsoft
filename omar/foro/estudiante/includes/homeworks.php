<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Homework;

Server::is_post();
if (isset($_POST['getDoneHomeworkById'])) {
   $id_doneHomework = $_POST['getDoneHomeworkById'];
   if ($doneHw = DB::table('tareas_enviadas')->where('id_tarea', $id_doneHomework)->first()) {
      $files = DB::table('T_tareas_archivos')->where('id_tarea',$doneHw->id)->get();
      $array = [
         'response' => true,
         'data' => $doneHw,
         'files' => $files
      ];
   } else {
      $array = [
         'response' => false
      ];
   }

   echo Util::toJson($array);
} else if (isset($_POST['doneHomework'])) {

   $id_homework = $_POST['id_homework'];
   $hw = new Homework($id_homework);
   $id_teacher = $hw->id2;
   $class = $hw->curso;
   $note = $_POST['note'];

   $id_doneHomework = DB::table('tareas_enviadas')->insertGetId([
      "id_tarea" => $id_homework,
      "id_estudiante" =>  Session::id(),
      "id_profesor" => $id_teacher,
      "curso" => $class,
      "nota" => $note,
      "fecha" => Util::date(),
      "hora" => Util::time(),
      "`year`" => $hw->info('year')
   ]);

   $uniqueId = uniqid();
   $file = new File('file');
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __STUDENT_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('T_tareas_archivos')->insert([
            'nombre' => $newName,
            'id_tarea' => $id_doneHomework
         ]);
      }
   }
}else if (isset($_POST['editDoneHomework'])) {

   $id_doneHomework = $_POST['editDoneHomework'];  
   $note = $_POST['note'];

   DB::table('tareas_enviadas')->where('id',$id_doneHomework)->update([      
      "nota" => $note    
   ]);

   $uniqueId = uniqid();
   $file = new File('file');
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __STUDENT_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('T_tareas_archivos')->insert([
            'nombre' => $newName,
            'id_tarea' => $id_doneHomework
         ]);
      }
   }
}else if (isset($_POST['delExistingFile'])) {

   $file_id = $_POST['delExistingFile'];

   $file = DB::table('T_tareas_archivos')->where('id', $file_id)->first();

   File::delete(__STUDENT_HOMEWORKS_DIRECTORY, $file->nombre);

   DB::table('T_tareas_archivos')->where('id', $file_id)->delete();
}

