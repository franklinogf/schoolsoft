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
    $obj = parent::table($this->table, !__COSEY)
      ->where($this->primary_key, $pk)->first();

    return $obj;
  }

  protected function getStudentBySS($ss, $table)
  {

    $year = $this->year();
    $obj = parent::table($table)->where([
      ['year', $year],
      ['ss', $ss]
    ])->first();
    return $obj;
  }

  protected function getStudentsById($id, $table)
  {
    $year = $this->year();
    $obj = parent::table($table)->where([
      ['year', $year],
      ['id', $id]
    ])->orderBy('grado DESC')->get();
    return $obj;
  }

  protected function getAllStudents($year, $includeUnenrolled)
  {
    $year = $year !== null ? $year : $this->year();

    if (!$includeUnenrolled) {
      $data = [['year', $year], ['codigobaja', '0']];
    } else {
      $data = ['year', $year];
    }

    $obj = parent::table($this->table)->where($data)->orderBy('apellidos')->get();
    return $obj;
  }

  protected function getStudentClasses($ss)
  {
    $year = $this->year();
    $desc = __LANG === 'es' ? 'descripcion' : 'desc2';
    $obj = parent::table('padres')
      ->select("DISTINCT id, curso, $desc as descripcion")
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
    $obj = parent::table('tareas_enviadas', !__COSEY)->where([
      ['id_tarea', $id_hw],
      ['id_estudiante', $mt],
      ['year', $this->year()]
    ])->first();
    return $obj;
  }

  protected function getStudentExams($ss, $date = null, $time = false)
  {
    $classes = $this->getStudentClasses($ss);
    $obj = [];
    foreach ($classes as $class) {
      $exam = new Exam();
      $studentExam = $exam->findByClassForStudents($class->curso, $date, $time);
      if ($studentExam) {
        foreach ($studentExam as $exam) {
          $obj[] = $exam;
        }
      }
    }
    return $obj;
  }

  protected function getStudentDoneExamById($mt, $id_exam)
  {
    $obj = parent::table('T_examenes_terminados', !__COSEY)->where([
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

  protected function getStudentsByClass($class, $table = 'padres', $summer = false)
  {
    $year = $this->year();
    if (!$summer) {
      $where = [
        ["$table.curso", $class],
        ['year.year', $year],
        ["$table.year", $year],
        ["$table.baja", ''],
      ];
    } else {
      $where = [
        ["$table.curso", $class],
        ['year.year', $year],
        ["$table.year", $year],
        ["$table.baja", ''],
        ["$table.verano", '2']
      ];
    }

    $obj = parent::table("$table")->join('year', "$table.ss", '=', 'year.ss')
      ->where($where)->orderBy('year.apellidos asc,year.nombre asc')->get();

    return $obj;
  }

  protected function getStudentsByCurs($ss, $table = 'padres', $summer = false)
  {
    $year = $this->year();
    if (!$summer) {
      $where = [
        ["$table.ss", $ss],
        ['year.year', $year],
        ["$table.year", $year],
        ["$table.baja", ''],
      ];
    } else {
      $where = [
        ["$table.curso", $ss],
        ['year.year', $year],
        ["$table.year", $year],
        ["$table.baja", ''],
        ["$table.verano", '2']
      ];
    }

    $obj = parent::table("$table")->join('year', "$table.ss", '=', 'year.ss')
      ->where($where)->orderBy('year.apellidos')->get();

    return $obj;
  }
  protected function getStudentsByGrade($grade, $table = 'year', $year = null)
  {
    $year = $year !== null ? $year : $this->year();
    $where = [
      ["$table.grado", $grade],
      ["$table.year", $year],
      ["$table.fecha_baja", '0000-00-00'],
    ];


    $obj = parent::table("$table")->where($where)->orderBy('year.apellidos')->get();

    return $obj;
  }

  protected function studentLogin($username, $password)
  {
    $year = $this->year();
    $obj = parent::table($this->table, !__COSEY)->where([
      ['usuario', $username],
      ['clave', $password],
      ['year', $year]
    ])->first();
    return $obj;
  }
  protected function getUnreadMessages($id)
  {
    $year = $this->year();
    $obj = parent::table('foro_mensajes', !__COSEY)->where([
      ['enviado_por', '<>', 'e'],
      ['id_e', $id],
      ['leido_e', '<>', 'si'],
      ['year', $year]
    ])->get();

    return count($obj);
  }

  protected function getLastStudentTopic($id)
  {

    $year = $this->year();
    if (__COSEY) {
      $obj = parent::table('foro_entradas', !__COSEY)
        ->select('foro_entradas.titulo,foro_entradas.curso,padres.descripcion as desc1,foro_entradas.id,foro_entradas.fecha,foro_entradas.hora,foro_entradas.desde')
        ->join('padres', 'padres.curso', '=', 'foro_entradas.curso')
        ->join('year', 'year.ss', '=', 'padres.ss')
        ->where([
          ['year.mt', $id],
          ['padres.year', $year],
          ['foro_entradas.estado', 'a']
        ])
        ->orderBy('foro_entradas.fecha DESC, foro_entradas.hora DESC')->first();
    } else {
      $desc = __LANG === 'es' ? 'desc1' : 'desc2';
      $obj = parent::table('foro_entradas')
        ->select("foro_entradas.titulo,foro_entradas.curso,cursos.$desc as desc1,foro_entradas.id,foro_entradas.fecha,foro_entradas.hora,foro_entradas.desde")
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
    }

    return $obj;
  }

  protected function getLastCommentedStudentTopic($id)
  {

    $year = $this->year();
    if (__COSEY) {
      $obj = parent::table('detalle_foro_entradas', !__COSEY)
        ->select('foro_entradas.titulo,foro_entradas.curso,padres.descripcion as desc1,foro_entradas.id,detalle_foro_entradas.fecha,detalle_foro_entradas.hora')
        ->join('foro_entradas', 'detalle_foro_entradas.entrada_id', '=', 'foro_entradas.id')
        ->join('padres', 'padres.curso', '=', 'foro_entradas.curso')
        ->join('year', 'year.ss', '=', 'padres.ss')
        ->where([
          ['year.mt', $id],
          ['padres.year', $year],
          ['foro_entradas.estado', 'a']
        ])
        ->orderBy('detalle_foro_entradas.fecha DESC, detalle_foro_entradas.hora DESC')->first();
    } else {
      $desc = __LANG === 'es' ? 'desc1' : 'desc2';
      $obj = parent::table('detalle_foro_entradas')
        ->select("foro_entradas.titulo,foro_entradas.curso,cursos.$desc as desc1,foro_entradas.id,detalle_foro_entradas.fecha,detalle_foro_entradas.hora")
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
    }

    return $obj;
  }

  protected function updateStudent($propsArray)
  {
    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
  protected function addStudent($propsArray)
  {
    $this->insertTable($this->table, $propsArray);
  }
  protected function deleteStudent()
  {
    $this->deleteTable($this->table, $this->primary_key, $this->{$this->primary_key});
  }
}
