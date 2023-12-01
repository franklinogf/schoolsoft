<?php

namespace Classes\Models;

use Classes\Controllers\Homework;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Models\StudentModel;

class TeacherModel extends School
{
  private $table = 'profesor';
  protected $primary_key = 'id';
  const TABLE = 'profesor';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getTeacherByPK($pk)
  {
    $obj =  parent::table($this->table)->where($this->primary_key, $pk)->first();
    return $obj;
  }


  protected function getAllTeachers()
  {

    $obj =  parent::table($this->table)->orderBy('apellidos')->get();
    return $obj;
  }
  protected function getTeacherByGrade($grade)
  {
    $obj = parent::table('profesor')->where('grado', $grade)->first();
    return $obj;
  }
  protected function getTeacherByUser($username)
  {
    $obj = parent::table($this->table)->where('usuario', $username)->first();

    return $obj;
  }
  protected function getTeacherClasses($id)
  {
    $year = $this->info('year2');
    if (__COSEY) {
      $desc = (__LANG === "es") ? "descripcion" : "desc2";
      $obj =  parent::table('padres')->select("DISTINCT curso, {$desc} as desc1")
        ->where([
          ['year', $year],
          ['id', $id]
        ])->orderBy('curso', 'ASC')->get();
    } else {
      $desc = (__LANG === "es") ? "descripcion" : "desc2";
      $obj =  parent::table('padres')->select("DISTINCT curso, {$desc} as desc1")
        ->where([
          ['year', $year],
          ['id', $id]
        ])->orderBy('curso', 'ASC')->get();
    }
    return $obj;
  }
  protected function getTeacherClassCredit($id, $class)
  {
    $year = $this->info('year2');
    $obj =  parent::table('cursos')->select('credito')
      ->where([
        ['curso', $class],
        ['year', $year],
        ['id', $id]
      ])->first();
    return $obj->credito;
  }

  protected function getAllTeacherStudents($id, $table = 'padres')
  {
    $year = $this->info('year2');
    $obj = parent::table($table)->where([
      ['year', $year],
      ['id', $id]
    ])->groupBy('ss')->orderBy('apellidos')->get();
    
    return $obj;
    // $students = new Student();
    // return $students->findById($id, $table);
  }

  protected function getHomeStudents($grade)
  {
    $year = $this->info('year2');
    if (__COSEY) {
      $obj =  parent::table(StudentModel::TABLE)
        ->where([
          // ['grado', $grade],
          ['year', $year],
          ['fecha_baja', '0000-00-00']
        ])
        ->orderBy('apellidos')->get();
    } else {
      $obj =  parent::table(StudentModel::TABLE)
        ->where([
          ['grado', $grade],
          ['year', $year],
          ['fecha_baja', '0000-00-00']
        ])
        ->orderBy('apellidos')->get();
    }

    return $obj;
  }
  protected function getUnreadMessages($id)
  {
    $year = $this->info('year2');
    $obj = parent::table('foro_mensajes', !__COSEY)->where([
      ['enviado_por', '<>', 'p'],
      ['id_p', $id],
      ['leido_p', '<>', 'si'],
      ['year', $year]
    ])->get();

    return count($obj);
  }

  protected function getLastTeacherTopic($id)
  {

    $year = $this->info('year2');
    if (__COSEY) {
      $obj =  parent::table('detalle_foro_entradas', !__COSEY)
        ->select('foro_entradas.titulo,foro_entradas.curso,padres.descripcion as desc1,foro_entradas.id,detalle_foro_entradas.fecha,detalle_foro_entradas.hora')
        ->join('foro_entradas', 'detalle_foro_entradas.entrada_id', '=', 'foro_entradas.id')
        ->join('padres', 'foro_entradas.curso', '=', 'padres.curso')
        ->where([
          ['padres.id', $id],
          ['padres.year', $year],
          ['foro_entradas.year', $year],
          ['foro_entradas.estado', 'a']
        ])
        ->orderBy('detalle_foro_entradas.fecha DESC, detalle_foro_entradas.hora DESC')->first();
    } else {


      $obj =  parent::table('detalle_foro_entradas')
        ->select('foro_entradas.titulo,foro_entradas.curso,cursos.desc1,foro_entradas.id,detalle_foro_entradas.fecha,detalle_foro_entradas.hora')
        ->join('foro_entradas', 'detalle_foro_entradas.entrada_id', '=', 'foro_entradas.id')
        ->join('cursos', 'foro_entradas.curso', '=', 'cursos.curso')
        ->where([
          ['cursos.id', $id],
          ['cursos.year', $year],
          ['foro_entradas.year', $year],
          ['foro_entradas.estado', 'a']
        ])
        ->orderBy('detalle_foro_entradas.fecha DESC, detalle_foro_entradas.hora DESC')->first();
    }

    return $obj;
  }

  protected function getTeachersTopicsByClass($id, $class)
  {

    $year = $this->info('year2');
    $obj =  parent::table('foro_entradas', !__COSEY)
      ->where([
        ['creador_id', $id],
        ['curso', $class],
        ['year', $year]
      ])->orderBy('fecha DESC, hora DESC')->get();

    return $obj;
  }

  protected function getTeachersTopics($id)
  {

    $year = $this->info('year2');
    $obj =  parent::table('foro_entradas', !__COSEY)
      ->where([
        ['creador_id', $id],
        ['year', $year]
      ])->orderBy('fecha DESC, hora DESC')->get();

    return $obj;
  }


  protected function getTeachersHomeworks($id, $class = false, $all = true)
  {
    $hw = new Homework();
    $obj = $hw->findByTeacher($id, $class, $all);
    return $obj;
  }

  protected function teacherLogin($username, $password)
  {
    $obj =  parent::table($this->table, !__COSEY)
      ->where([
        ['usuario', $username],
        ['clave', $password]
      ])->first();

    return $obj;
  }

  protected function updateTeacher($propsArray)
  {

    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
  protected function addTeacher($propsArray)
  {
    $this->insertTable($this->table, $propsArray);
  }
}
