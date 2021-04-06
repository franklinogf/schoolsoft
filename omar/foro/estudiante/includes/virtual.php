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
   ])->first();

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

   echo Util::toJson($array);
} 
