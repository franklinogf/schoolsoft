<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();

if (isset($_POST['addHomework'])) {
   $id_teacher = Session::id();
   $document_id = DB::table('tbl_documentos')->insertGetId([
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
      'hora' => Util::time()
   ]);

   $file = new File('file');
   foreach ($file->files as $file) {
      $newName = "({$id_teacher}-{$document_id}) $file->name";
      if (File::upload($file, __TEACHER_HOMEWORKS_DIRECTORY, $newName)) {
         DB::table('T_archivos')->insert([
            'nombre' => $newName,
            'id_documento' => $document_id
         ]);
      }
   }

   Route::redirect('/profesor/homeworks.php');
} elseif (isset($_POST['delHomework'])) {

   $document_id = $_POST['delHomework'];
   $homework = DB::table('tbl_documentos')->where('id_documento', $document_id)->first();

   if ($homework->nombre_archivo !== '') {
      File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $homework->nombre_archivo);
   }
   DB::table('tbl_documentos')->where('id_documento', $document_id)->delete();

   $files = DB::table('T_archivos')->where('id_documento', $document_id)->get();
   if ($files) {
      foreach ($files as $file) {
         File::delete(__TEACHER_HOMEWORKS_DIRECTORY, $file->nombre);
      }
      DB::table('T_archivos')->where('id_documento', $document_id)->delete();
   }
}
