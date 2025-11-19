<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;


Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['find'])) {

   $virtualClass = DB::table('virtual')->where([
      ['curso', $_POST['find']],
      ['year', $teacher->info('year')],
      ['id_profesor', $teacher->id],
      ['activo', true],
   ])->first();

   if ($virtualClass) {
      $array = [
         'response' => true,
         'data' => [
            "id" => $virtualClass->id,
            'link' => $virtualClass->link,
            'title' => $virtualClass->titulo,
            'date' => $virtualClass->fecha,
            'time' => $virtualClass->hora,
            'password' => $virtualClass->clave,
            'information' => $virtualClass->informacion,
         ]
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
} else if (isset($_POST['add'])) {
   $id =  DB::table('virtual')->insertGetId([
      'link' => $_POST['link'],
      'titulo' => $_POST['title'],
      'fecha' => $_POST['date'],
      'hora' => $_POST['time'],
      'year' => $teacher->info('year'),
      'id_profesor' => $teacher->id,
      'curso' => $_POST['add'],
      'clave' => $_POST['password'],
      'informacion' => $_POST['information'],
   ]);
   $array = [
      'response' => true,
      'data' => ["id" => $id]
   ];
   echo Util::toJson($array);
} else if (isset($_POST['update'])) {
   DB::table('virtual')->where('id', $_POST['update'])
      ->update([
         'link' => $_POST['link'],
         'titulo' => $_POST['title'],
         'fecha' => $_POST['date'],
         'hora' => $_POST['time'],
         'clave' => $_POST['password'],
         'informacion' => $_POST['information'],
      ]);
} else if (isset($_POST['delete'])) {
   DB::table('virtual')->where('id', $_POST['delete'])
      ->update(['activo' => false]);
}
