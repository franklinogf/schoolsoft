<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\VirtualClass;
use Classes\Util;
use Classes\Server;
use Classes\Session;



Server::is_post();
$teacher = Teacher::find(Session::id());

if (isset($_POST['find'])) {
   $year = Admin::primaryAdmin()->year();

   $virtualClass = $teacher->virtualClasses()->active()->ofClass($_POST['find'])->first();

   if ($virtualClass) {
      $array = [
         'response' => true,
         'data' => [
            "id" => $virtualClass->id,
            'link' => $virtualClass->link,
            'title' => $virtualClass->titulo,
            'date' => $virtualClass->fecha->format('Y-m-d'),
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
   $year = Admin::primaryAdmin()->year();

   $id =  VirtualClass::insertGetId([
      'link' => $_POST['link'],
      'titulo' => $_POST['title'],
      'fecha' => $_POST['date'],
      'hora' => $_POST['time'],
      'year' => $year,
      'curso' => $_POST['add'],
      'clave' => $_POST['password'],
      'activo' => true,
      'id_profesor' => $teacher->id,
      'informacion' => $_POST['information'],
   ]);
   $array = [
      'response' => true,
      'data' => ["id" => $id]
   ];
   echo Util::toJson($array);
} else if (isset($_POST['update'])) {
   VirtualClass::find($_POST['update'])
      ->update([
         'link' => $_POST['link'],
         'titulo' => $_POST['title'],
         'fecha' => $_POST['date'],
         'hora' => $_POST['time'],
         'clave' => $_POST['password'],
         'informacion' => $_POST['information'],
      ]);
} else if (isset($_POST['delete'])) {
   VirtualClass::where('id', $_POST['delete'])
      ->update(['activo' => false]);
}
