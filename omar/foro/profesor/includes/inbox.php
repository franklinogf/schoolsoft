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
         $students = DB::table('foro_mensajes')->select('DISTINCT id_e as mt')->where([
            ['code', $message->code],
            ['enviado_por', 'p'],
            ['id_p', Session::id()],
            ['year', $school->info('year')]
         ])->get();
         $to = [];
         $student = new Student($message->id_e);
         $teacher = new Teacher($message->id_p);
         $from = $message->enviado_por === 'p' ? 'teacher' : 'student';

         $name = ${$from}->fullName();
         $profilePicture = ${$from}->profilePicture();
         $info = $message->enviado_por === 'p' ? 'yo' : $student->grado;
         $path = $message->enviado_por === 'p' ? __STUDENT_MESSAGES_FILES_DIRECTORY_URL : __TEACHER_MESSAGES_FILES_DIRECTORY_URL;

         if ($message->enviado_por === 'p') {
            foreach ($students as $student) {
               $student = new Student($student->mt);
               $to[] = [
                  "nombre" => strtoupper($student->fullName()),
                  "foto" => $student->profilePicture(),
                  "info" => $student->grado
               ];
            }
         } else {
            $to[] = [
               "nombre" => strtoupper($teacher->fullName()),
               "foto" => $teacher->profilePicture(),
               "info" => "yo"
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
            'foto' => $profilePicture,
            'info' => $info,
            'leido' => $message->leido_p,
            'enviadoPor' => $message->enviado_por,
            'to' => Util::toObject($to),
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

   $uniqueId = uniqid();
   $file = new File();
   foreach ($file->files as $file) {
      $newName = "({$uniqueId}) $file->name";
      if (File::upload($file, __TEACHER_MESSAGES_FILES_DIRECTORY, $newName)) {
         DB::table('t_mensajes_archivos')->insert([
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
