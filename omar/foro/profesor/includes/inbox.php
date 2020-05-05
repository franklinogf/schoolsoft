<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;
use Classes\Server;
use Classes\Session;

Server::is_post();
$school = new School();

if (isset($_POST['getMessages'])) {
   $data = [];
   if ($_POST['getMessages'] === 'inbound') {
      $messages = DB::table('foro_mensajes')->where([
         ['enviado_por', '<>', 'p'],
         ['id_p', Session::id()],
         ['year', $school->info('year')]
      ])->orderBy('fecha DESC, hora DESC')->get();
   } else {
      $messages = DB::table('foro_mensajes')->where([
         ['enviado_por', 'p'],
         ['id_p', Session::id()],
         ['year', $school->info('year')]
      ])->orderBy('fecha DESC, hora DESC')->get();
   }

   if ($messages) {

      foreach ($messages as $message) {
         $name = '';
         if ($message->enviado_por === 'e') {
            $student = new Student($message->id_e);
            $name = $student->fullName();
            $foto = $student->profilePicture();
            $info = $student->grado;
         } else {
            $teacher = new Teacher($message->id_p);
            $name = $teacher->fullName();
            $foto = $teacher->profilePicture();

            $info = $message->id_p === Session::id() ? 'yo' : 'profesor';
         }
         $data[] = [
            'id' => $message->id,
            'id_p' => $message->id_p,
            'id_e' => $message->id_e,
            'titulo' => $message->titulo,
            'asunto' => $message->asunto,
            'mensaje' => $message->mensaje,
            'nombre' => $name,
            'foto' => $foto,
            'leido' => $message->leido_p,
            'enviadoPor' => $message->enviado_por,
            'info' => $info,
            'fecha' => Util::formatDate($message->fecha, true, true),
            'hora' => Util::formatTime($message->hora),
            'codigo' => $message->code,
            'year' => $message->year

         ];
      }

      $array = [
         'response' => true,
         'data' => $data
      ];
   }


   echo Util::toJson($array);
} else if (isset($_POST['respondMessage'])) {
   $data = $_POST['respondMessage'];

   $in = DB::table('foro_mensajes')->insert([
      "enviado_por" => 'p',
      "code" => $data['codigo'],
      "id_p" => $data['id_p'],
      "id_e" => $data['id_e'],
      "titulo" => $data['titulo'],
      "asunto" => $data['asunto'],
      "mensaje" => $data['respondMessage'],
      "leido_p" => 'si',
      "year" => $data['year'],
      "fecha" => Util::date(),
      "hora" => Util::time()

   ]);
   var_dump($in);
} else if (isset($_POST['newMessage'])) {

  
} else if (isset($_POST['changeStatus'])) {

   $message_id = $_POST['changeStatus'];

   DB::table('foro_mensajes')->where('id', $message_id)->update(['leido_p' => 'si']);
   $teacher = new Teacher(Session::id());
   $array = [
      'unreadMessages' => $teacher->unreadMessages()
   ];
   echo Util::toJson($array);
}
