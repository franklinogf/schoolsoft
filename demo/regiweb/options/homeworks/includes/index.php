<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Homework;
use Classes\Controllers\School;
use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();
if (isset($_POST['getHomework'])) {
   $id_homework = $_POST['getHomework'];
   $hw = new Homework($id_homework);
   echo Util::toJson($hw);
} else if (isset($_POST['addHomework'])) {
   $id_teacher = Session::id();
   $school = new School();
   $id_homework = DB::table('tbl_documentos')->insertGetId([
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
      'year' => $school->info('year'),
      'hora' => Util::time()
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
   Route::includeFile('/regiweb/options/homeworks/includes/mailHomeworks.php');

   Route::redirect('/options/homeworks/');
} else if (isset($_POST['editHomework'])) {
   $id_teacher = Session::id();
   $id_homework = $_POST['document_id'];

   DB::table('tbl_documentos',!__COSEY)->where('id_documento', $id_homework)->update([
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

   Route::redirect('/options/homeworks/');
} else if (isset($_POST['delHomework'])) {

   $id_homework = $_POST['delHomework'];
   $homework = DB::table('tbl_documentos',!__COSEY)->where('id_documento', $id_homework)->first();

   if ($homework->nombre_archivo !== '') {
      File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $homework->nombre_archivo);
   }
   DB::table('tbl_documentos',!__COSEY)->where('id_documento', $id_homework)->delete();

   $files = DB::table('t_archivos')->where('id_documento', $id_homework)->get();
   if ($files) {
      foreach ($files as $file) {
         File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $file->nombre);
      }
      DB::table('t_archivos',!__COSEY)->where('id_documento', $id_homework)->delete();
   }
} else if (isset($_POST['delExistingFile'])) {

   $file_id = $_POST['delExistingFile'];

   $file = DB::table('t_archivos',!__COSEY)->where('id', $file_id)->first();

   File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $file->nombre);

   DB::table('t_archivos',!__COSEY)->where('id', $file_id)->delete();
}
