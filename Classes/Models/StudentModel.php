<?php

namespace Classes\Models;

use Classes\Controllers\Exam;
use Classes\Controllers\School;
use Classes\Controllers\Homework;

class StudentModel extends School
{
  private $table = 'year';
  protected $primary_key = 'mt';
  const TABLE = 'year';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getStudentByPK($pk)
  {
    $obj = parent::table($this->table)
      ->where($this->primary_key, $pk)->first();

    return $obj;
  }

  protected function getStudentBySS($ss)
  {

    $year = $this->info('year');
    $obj = parent::table($this->table)->where([
      ['year', $year],
      ['ss', $ss]
    ])->first();
    return $obj;
  }

  protected function getAllStudents()
  {
    $year = $this->info('year');
    $obj = parent::table($this->table)->where([
      ['year', $year],
      ['baja', '']
    ])->orderBy('apellidos')->get();
    return $obj;
  }

  protected function getStudentClasses($ss)
  {
    $year = $this->info('year');
    $obj = parent::table('padres')
      ->select('DISTINCT id, curso, descripcion')
      ->where([
        ['year', $year],
        ['ss', $ss]
      ])->orderBy('curso')->get();
    return $obj;
  }
  protected function getStudentHomeworks($ss, $date = null)
  {
    $classes = $this->getStudentClasses($ss);
    $obj = [];
    foreach ($classes as $class) {
      $hw = new Homework();
      if ($homework = $hw->findByClassForStudents($class->curso, $date))
        $obj[] = $homework;
    }
    if (count($obj) > 0) {
      return call_user_func_array('array_merge', $obj);
    } else {
      return [];
    }
  }

  protected function getStudentDoneHomeworkById($mt, $id_hw)
  {
    $obj = parent::table('tareas_enviadas')->where([
      ['id_tarea', $id_hw],
      ['id_estudiante', $mt],
      ['year', $this->info('year')]
    ])->first();
    return $obj;
  }

  protected function getStudentExams($ss, $date = null, $time = false)
  {
    $classes = $this->getStudentClasses($ss);
    $obj = [];
    foreach ($classes as $class) {
      $exam = new Exam();
      $studenExam = $exam->findByClassForStudents($class->curso, $date, $time);
      if ($studenExam) {
        $obj[] = $studenExam;
      }
    }
    return $obj;
  }

  protected function getStudentDoneExamById($mt, $id_exam)
  {
    $obj = parent::table('T_examenes_terminados')->where([
      ['id_examen', $id_exam],
      ['id_estudiante', $mt],
      ['year', $this->info('year')]
    ])->first();
    return $obj;
  }

  protected function getStudentByUser($username)
  {
    $obj = parent::table($this->table)->where('usuario', $username)->first();

    return $obj;
  }

  protected function getStudentsByClass($class)
  {
    $year = $this->info('year');

    $obj = parent::table('padres')
      ->select('year.*,padres.descripcion')
      ->join('year', 'padres.ss', '=', 'year.ss')
      ->where([
        ['padres.curso', $class],
        ['year.year', $year],
        ['padres.year', $year],
        ['baja', '']
      ])->orderBy('year.apellidos')->get();

    return $obj;
  }

  protected function studentLogin($username, $password)
  {
    $obj = parent::table($this->table)->where([
      ['usuario', $username],
      ['clave', $password]
    ])->first();
    return $obj;
  }
  protected function getUnreadMessages($id)
  {
    $year = $this->info('year');
    $obj = parent::table('foro_mensajes')->where([
      ['enviado_por', '<>', 'e'],
      ['id_e', $id],
      ['leido_e', '<>', 'si'],
      ['year', $year]
    ])->get();

    return count($obj);
  }

  protected function getLastStudentTopic($id)
  {

    $year = $this->info('year');
    $obj =  parent::table('foro_entradas')
      ->select('foro_entradas.titulo,foro_entradas.curso,cursos.desc1,foro_entradas.id,foro_entradas.fecha,foro_entradas.hora,foro_entradas.desde')
      ->join('padres', 'padres.curso', '=', 'foro_entradas.curso')
      ->join('cursos', 'padres.curso', '=', 'cursos.curso')
      ->join('year', 'year.ss', '=', 'padres.ss')
      ->where([
        ['year.mt', $id],
        ['cursos.year', $year],
        ['padres.year', $year],
        ['foro_entradas.estado', 'a']
      ])
      ->orderBy('foro_entradas.fecha DESC, foro_entradas.hora DESC')->first();

    return $obj;
  }

  protected function getLastCommentedStudentTopic($id)
  {

    $year = $this->info('year');
    $obj =  parent::table('detalle_foro_entradas')
      ->select('foro_entradas.titulo,foro_entradas.curso,cursos.desc1,foro_entradas.id,detalle_foro_entradas.fecha,detalle_foro_entradas.hora')
      ->join('foro_entradas', 'detalle_foro_entradas.entrada_id', '=', 'foro_entradas.id')
      ->join('padres', 'padres.curso', '=', 'foro_entradas.curso')
      ->join('cursos', 'padres.curso', '=', 'cursos.curso')
      ->join('year', 'year.ss', '=', 'padres.ss')
      ->where([
        ['year.mt', $id],
        ['cursos.year', $year],
        ['padres.year', $year],
        ['foro_entradas.estado', 'a']
      ])
      ->orderBy('detalle_foro_entradas.fecha DESC, detalle_foro_entradas.hora DESC')->first();

    return $obj;
  }

  protected function updateStudent($propsArray)
  {
    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
