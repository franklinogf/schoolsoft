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
         ['enviado_por', '<>', 'e'],
         ['id_e', Session::id()],
         ['year', $school->info('year')]
      ])->orderBy('fecha DESC, hora DESC')->get();
   } else {
      $messages = DB::table('foro_mensajes')->where([
         ['enviado_por', 'e'],
         ['id_e', Session::id()],
         ['year', $school->info('year')]
      ])->groupBy('code')->orderBy('fecha DESC, hora DESC')->get();
   }

   if ($messages) {
      foreach ($messages as $message) {
         $links = DB::table('t_mensajes_links')->where("mensaje_code", $message->code)->get();
         $student = new Student($message->id_e);
         $teacher = new Teacher($message->id_p);
         $from = $message->enviado_por === 'e' ? 'student' : 'teacher';
         $to = $message->enviado_por === 'e' ? 'teacher' : 'student';

         $name = ${$from}->fullName();
         $profilePicture = ${$from}->profilePicture();
         $info = $message->enviado_por === 'e' ? 'yo' : 'profesor';
         $path = $message->enviado_por === 'e' ? __STUDENT_MESSAGES_FILES_DIRECTORY_URL : __TEACHER_MESSAGES_FILES_DIRECTORY_URL;
         $toName = ${$to}->fullName();
         $toProfilePicture = ${$to}->profilePicture();
         $toInfo = $message->enviado_por === 'e' ? 'profesor' : $student->grado;

         $linksArray = [];
         foreach ($links as $link) {
            $linksArray = [
               "link" => $link->link,
               "nombre" => $link->nombre
            ];
         }

         $filesArray = [];
         $files = DB::table('t_mensajes_archivos')
            ->where('mensaje_code', $message->code)->get();
         if ($files) {
            foreach ($files as $i => $file) {
               $filesArray[$i]['nombre'] = File::name($file->nombre, true);
               $filesArray[$i]['url'] = $path . $file->nombre;
               $filesArray[$i]['icon'] = File::faIcon(File::extension($file->nombre), 'lg');
            }
         }

         $data[] = [
            'codigo' => $message->code,
            'id' => $message->id,
            'id_p' => $message->id_p,
            'id_e' => $message->id_e,
            'titulo' => $message->titulo,
            'asunto' => $message->asunto,
            'mensaje' => $message->mensaje,
            'archivos' => $filesArray,
            'nombre' => strtoupper($name),
            'info' => $info,
            'foto' => $profilePicture,
            'toNombre' => strtoupper($toName),
            'toInfo' => $toInfo,
            'toFoto' => $toProfilePicture,
            'leido' => $message->leido_e,
            'enviadoPor' => $message->enviado_por,
            'links' => Util::toObject($links),
            'fecha' => Util::formatDate($message->fecha, true, true),
            'hora' => Util::formatTime($message->hora),
            'year' => $message->year

         ];
      }

      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = [
         'response' => false
      ];
   }


   echo Util::toJson($array);
} else if (isset($_POST['teacherById'])) {
   $id_teacher = $_POST['teacherById'];
   $teacher = new Teacher($id_teacher);
   $array = [
      'response' => true,
      'data' => $teacher
   ];
   echo Util::toJson($array);
} else if (isset($_POST['respondMessage'])) {
   $data = $_POST['respondMessage'];

   DB::table('foro_mensajes')->insert([
      "enviado_por" => 'e',
      "code" => $data['codigo'],
      "id_p" => $data['id_p'],
      "id_e" => $data['id_e'],
      "titulo" => $data['titulo'],
      "asunto" => "RE: " . $data['asunto'],
      "mensaje" => $data['respondMessage'],
      "leido_e" => 'si',
      "year" => $data['year'],
      "fecha" => Util::date(),
      "hora" => Util::time()

   ]);
} else if (isset($_POST['newMessage'])) {
   $school = new School();
   $mt_student = Session::id();
   $id_teacher = $_POST['teacher_id'];
   $title = $_POST['title'];
   $message = $_POST['message'];
   $subject = $_POST['subject'];
   $code = DB::table("foro_mensajes")->select('MAX(code) as maxCode')->first();
   $code = (int) $code->maxCode + 1;

   $uniqueId = uniqid();
   $file = new File();
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __STUDENT_MESSAGES_FILES_DIRECTORY, $newName)) {
         DB::table('t_mensajes_archivos')->insert([
            'nombre' => $newName,
            'mensaje_code' => $code
         ]);
      }
   }
   DB::table('foro_mensajes')->insert([
      "enviado_por" => 'e',
      "code" => $code,
      "id_p" => $id_teacher,
      "id_e" => $mt_student,
      "titulo" => $title,
      "asunto" => $subject,
      "mensaje" => $message,
      "leido_e" => 'si',
      "year" => $school->info('year'),
      "fecha" => Util::date(),
      "hora" => Util::time()

   ]);
} else if (isset($_POST['changeStatus'])) {

   $message_id = $_POST['changeStatus'];

   DB::table('foro_mensajes')->where('id', $message_id)->update(['leido_e' => 'si']);
   $student = new Student(Session::id());
   $array = [
      'unreadMessages' => $student->unreadMessages()
   ];
   echo Util::toJson($array);
}
