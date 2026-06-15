<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\DoneHomework;
use App\Models\Homework;
use Classes\File;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;


Server::is_post();
if (isset($_POST['getDoneHomeworkById'])) {
   $id_doneHomework = $_POST['getDoneHomeworkById'];
   if (
      $doneHw = DoneHomework::query()->where([
         'id_tarea' => $id_doneHomework,
         'id_estudiante' =>  Session::id(),
      ])->first()
   ) {
      $files = DB::table('t_tareas_archivos')->where('id_tarea', $doneHw->id)->get();
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

   $id_homework = $_POST['doneHomework'];
   $hw = Homework::findOrFail($id_homework);
   $id_teacher = $hw->id2;
   $class = $hw->curso;
   $note = $_POST['note'];

   $doneHomework = DoneHomework::query()->create([
      "id_tarea" => $id_homework,
      "id_estudiante" => Session::id(),
      "id_profesor" => $id_teacher,
      "curso" => $class,
      "nota" => $note,
      "fecha" => date('Y-m-d'),
      "hora" => date('H:i:s'),
      "year" => Admin::primaryAdmin()->year()
   ]);

   $uniqueId = uniqid();
   $file = new File('file');
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __STUDENT_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('t_tareas_archivos')->insert([
            'nombre' => $newName,
            'id_tarea' => $doneHomework->id
         ]);
      }
   }
} else if (isset($_POST['editDoneHomework'])) {

   $id_doneHomework = $_POST['editDoneHomework'];
   $note = $_POST['note'];

   DoneHomework::query()
      ->where('id', $id_doneHomework)
      ->update([
         "nota" => $note
      ]);

   $uniqueId = uniqid();
   $file = new File('file');
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __STUDENT_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('t_tareas_archivos')->insert([
            'nombre' => $newName,
            'id_tarea' => $id_doneHomework
         ]);
      }
   }
} else if (isset($_POST['delExistingFile'])) {

   $file_id = $_POST['delExistingFile'];

   $file = DB::table('t_tareas_archivos')->where('id', $file_id)->first();

   File::delete(__STUDENT_HOMEWORKS_DIRECTORY, $file->nombre);

   DB::table('t_tareas_archivos')->where('id', $file_id)->delete();
}
