<?php

require_once '../../../../app.php';

use Classes\File;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();

$filePath = "admin/users/documents/files/";

if ($_POST['option'] === 'save') {
   $ss = $_POST['addDocumentStudentSs'];
   $title = $_POST['title'];
   $date = $_POST['date'];
   $file = new File();
   if ($file->amount > 0) {
      $nextId = DB::getNextAutoIncrementIdFromTable('estudiantes_docs');
      $newName = "$ss($nextId).jpg";
      $file::upload($file->files, $filePath, $newName);
      DB::table("estudiantes_docs")->insert([
         'ss_estudiante' => $ss,
         'titulo' => $title,
         'fecha' => $date,
         'nombre_archivo' => $newName,
      ]);
   }


   Route::redirect("/users/documents/index.php");
} else if ($_POST['option'] === 'edit') {
   $title = $_POST['title'];
   $date = $_POST['date'];
   $file = new File();
   $id = $_POST['addDocumentId'];
   if ($file->amount > 0) {
      $document = DB::table("estudiantes_docs")->select('nombre_archivo')->where(['id', $id])->first();
      $file::upload($file->files, $filePath, $document->nombre_archivo);
   }
   DB::table("estudiantes_docs")->where(['id', $id])->update([
      'titulo' => $title,
      'fecha' => $date,
   ]);
   $ss = $_POST['addDocumentStudentSs'];

   Route::redirect("/users/documents/index.php");
} else if ($_POST['option'] === 'delete') {
   $id = $_POST['addDocumentId'];
   $document = DB::table("estudiantes_docs")->select('nombre_archivo')->where(['id', $id])->first();
   File::delete($filePath, $document->nombre_archivo);
   DB::table("estudiantes_docs")->where(['id', $id])->delete();
}
