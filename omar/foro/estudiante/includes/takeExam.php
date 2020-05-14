<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Exam;
use Classes\Controllers\Student;
use Classes\Util;

Session::is_logged();
Server::is_post();

$insertsArray = [];
$id_exam = $_POST['id_exam'];
$student = new Student(Session::id());
$exam = new Exam($id_exam);

//save done Exam
$id_doneExam = DB::table('t_examenes_terminados')->insertGetId([
   "id_examen" => $id_exam,
   "id_estudiante" => $student->mt,
   "ss_estudiante" => $student->ss,
   "curso" => $exam->curso,
   "year" => $student->info('year')
]);


foreach ($_POST['fv'] as $id => $answer) {
   $insertsArray['t_examen_terminado_fyv'][] = [
      "id_examen" => $id_doneExam,
      "id_estudiante" => $student->mt,
      "ss_estudiante" => $student->ss,
      "id_pregunta" => $id,
      "respuesta" => $answer
   ];
}

foreach ($_POST['select'] as $id => $answer) {
   $insertsArray['t_examen_terminado_selec'][] = [
      "id_examen" => $id_doneExam,
      "id_estudiante" => $student->mt,
      "ss_estudiante" => $student->ss,
      "id_pregunta" => $id,
      "respuesta" => $answer
   ];
}


foreach ($_POST['pair'] as $id => $answer) {
   $insertsArray['t_examen_terminado_parea'][] = [
      "id_examen" => $id_doneExam,
      "id_estudiante" => $student->mt,
      "ss_estudiante" => $student->ss,
      "id_pregunta" => $id,
      "respuesta" => $answer
   ];
}


foreach ($_POST['line'] as $id => $answers) {
   $insertsArray['t_examen_terminado_linea'][$id] = [
      "id_examen" => $id_doneExam,
      "id_estudiante" => $student->mt,
      "ss_estudiante" => $student->ss,
      "id_pregunta" => $id
   ];
   foreach ($answers as $i => $answer) {
      $index = $i + 1;
      $insertsArray['t_examen_terminado_linea'][$id]["respuesta{$index}"] = $answer;
   }
}

foreach ($_POST['qa'] as $id => $answer) {
   $insertsArray['t_examen_terminado_pregunta'][] = [
      "id_examen" => $id_doneExam,
      "id_estudiante" => $student->mt,
      "ss_estudiante" => $student->ss,
      "id_pregunta" => $id,
      "respuesta" => $answer
   ];
}
foreach ($insertsArray as $table => $data) {   
   DB::table($table)->insert($data);
}
Session::set("examTaken", "Examen realizado con exito");
Route::redirect('/estudiante/exams.php');
