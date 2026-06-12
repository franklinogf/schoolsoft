<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Homework;
use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;


Session::is_logged();
Server::is_post();
if (isset($_POST['getHomework'])) {
   $id_homework = $_POST['getHomework'];
   $hw = Homework::findOrFail($id_homework);
   echo Util::toJson($hw);
} else if (isset($_POST['addHomework'])) {
   $id_teacher = Session::id();
   $year = Admin::primaryAdmin()->year();
   $id_homework = Homework::insertGetId([
      'titulo' => $_POST["title"],
      'descripcion' => $_POST["description"],
      'id2' => $id_teacher,
      'fec_in' => $_POST["sinceDate"],
      'fec_out' => $_POST["untilDate"],
      'curso' => $_POST["class"],
      'lin1' => $_POST["link1"],
      'lin2' => $_POST["link2"],
      'lin3' => $_POST["link3"],
      'enviartarea' => $_POST["state"],
      'year' => $year,
      'hora' => date('H:i:s'),
   ]);

   $file = new File('file');
   $uniqueId = uniqid();
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __TEACHER_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('t_archivos')->insert([
            'nombre' => $newName,
            'id_documento' => $id_homework
         ]);
      }
   }
   Route::includeFile('/foro/profesor/includes/email/mailHomeworks.php');

   Route::redirect('/profesor/homeworks.php');
} else if (isset($_POST['editHomework'])) {
   $id_teacher = Session::id();
   $id_homework = $_POST['document_id'];

   Homework::query()
      ->where('id_documento', $id_homework)
      ->update([
         'titulo' => $_POST["title"],
         'descripcion' => $_POST["description"],
         'fec_in' => $_POST["sinceDate"],
         'fec_out' => $_POST["untilDate"],
         'curso' => $_POST["class"],
         'lin1' => $_POST["link1"],
         'lin2' => $_POST["link2"],
         'lin3' => $_POST["link3"],
         'enviartarea' => $_POST["state"]
      ]);

   $file = new File();
   $uniqueId = uniqid();
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __TEACHER_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('t_archivos')->insert([
            'nombre' => $newName,
            'id_documento' => $id_homework
         ]);
      }
   }

   Route::redirect('/profesor/homeworks.php');
} else if (isset($_POST['delHomework'])) {

   $id_homework = $_POST['delHomework'];
   $homework = Homework::query()->findOrFail($id_homework);

   if ($homework->nombre_archivo !== '') {
      File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $homework->nombre_archivo);
   }
   $homework->delete();

   $files = DB::table('t_archivos')->where('id_documento', $id_homework)->get();
   if ($files) {
      foreach ($files as $file) {
         File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $file->nombre);
      }
      DB::table('t_archivos', !__COSEY)->where('id_documento', $id_homework)->delete();
   }
} else if (isset($_POST['delExistingFile'])) {

   $file_id = $_POST['delExistingFile'];

   $file = DB::table('t_archivos', !__COSEY)->where('id', $file_id)->first();

   File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $file->nombre);

   DB::table('t_archivos', !__COSEY)->where('id', $file_id)->delete();
}
