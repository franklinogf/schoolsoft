<?php
require_once '../../../app.php';

use Classes\File;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

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
      ])->groupBy('code')->orderBy('fecha DESC, hora DESC')->get();
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

   DB::table('foro_mensajes')->insert([
      "enviado_por" => 'p',
      "code" => $data['codigo'],
      "id_p" => $data['id_p'],
      "id_e" => $data['id_e'],
      "titulo" => $data['titulo'],
      "asunto" => "RE: " . $data['asunto'],
      "mensaje" => $data['respondMessage'],
      "leido_p" => 'si',
      "year" => $data['year'],
      "fecha" => Util::date(),
      "hora" => Util::time()

   ]);
} else if (isset($_POST['newMessage'])) {
   $school = new School();
   $id_teacher = Session::id();
   $students_mt = $_POST['students'];
   $title = $_POST['title'];
   $message = $_POST['message'];
   $subject = $_POST['subject'];
   $code = DB::table("foro_mensajes")->select('MAX(code) as maxCode')->first();
   $code = (int) $code->maxCode + 1;


   $file = new File();
   foreach ($file->files as $file) {
      $newName = "({$id_teacher}-{$code}) $file->name";
      if (File::upload($file, __MESSAGES_FILES_DIRECTORY, $newName)) {
         DB::table('T_mensajes_archivos')->insert([
            'nombre' => $newName,
            'mensaje_code' => $code
         ]);
      }
   }

   foreach ($students_mt as $mt) {
      DB::table('foro_mensajes')->insert([
         "enviado_por" => 'p',
         "code" => $code,
         "id_p" => $id_teacher,
         "id_e" => $mt,
         "titulo" => $title,
         "asunto" => $subject,
         "mensaje" => $message,
         "leido_p" => 'si',
         "year" => $school->info('year'),
         "fecha" => Util::date(),
         "hora" => Util::time()

      ]);
   }
} else if (isset($_POST['changeStatus'])) {

   $message_id = $_POST['changeStatus'];

   DB::table('foro_mensajes')->where('id', $message_id)->update(['leido_p' => 'si']);
   $teacher = new Teacher(Session::id());
   $array = [
      'unreadMessages' => $teacher->unreadMessages()
   ];
   echo Util::toJson($array);
}
