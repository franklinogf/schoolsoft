<?php
require_once '../../../app.php';

use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['homeworksByClass'])) {
   $class = $_POST['homeworksByClass'];

   $data = DB::table('tbl_documentos')
      ->select('tbl_documentos.*')
      ->join('cursos', 'tbl_documentos.curso', '=', 'cursos.curso')
      ->where([
         ['tbl_documentos.curso', $class],
         ['tbl_documentos.id2', $teacher->id],
         ['cursos.year', $teacher->info('year')],
      ])->orderBy('tbl_documentos.fec_out','DESC')->get();      

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
