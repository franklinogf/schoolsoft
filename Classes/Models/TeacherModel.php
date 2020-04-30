<?php

namespace Classes\Models;

use Classes\Controllers\Homework;
use Classes\Controllers\School;
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

  protected function getTeacherClasses($id)
  {
    $year = $this->info('year');
    $obj =  parent::table('cursos')
      ->where([
        ['year', $year],
        ['id', $id]
      ])->orderBy('curso', 'ASC')->get();
    return $obj;
  }

  protected function getHomeStudents($grade)
  {
    $year = $this->info('year');
    $obj =  parent::table(StudentModel::TABLE)
      ->where([
        ['grado', $grade],
        ['year', $year],
        ['fecha_baja', '0000-00-00']
      ])
      ->orderBy('apellidos')->get();


    return $obj;
  }

  protected function getLastTeacherTopic($id)
  {

    $year = $this->info('year');
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
      ->orderBy('detalle_foro_entradas.fecha', 'DESC')->first();

    return $obj;
  }

  protected function getTeachersTopicsByClass($id, $class)
  {

    $year = $this->info('year');
    $obj =  parent::table('foro_entradas')
      ->where([
        ['creador_id', $id],
        ['curso', $class],
        ['year', $year]
      ])->orderBy('fecha DESC, hora DESC')->get();

    return $obj;
  }

  protected function getTeachersTopics($id)
  {

    $year = $this->info('year');
    $obj =  parent::table('foro_entradas')
      ->where([
        ['creador_id', $id],
        ['year', $year]
      ])->orderBy('fecha DESC, hora DESC')->get();

    return $obj;
  }

  protected function getTeachersHomeworks($id)
  {
    $hw = new Homework();   
    $obj = $hw->findByTeacher($id);
    return $obj;
  }
  protected function getTeachersHomeworksByClass($id, $class)
  {
    $hw = new Homework();   
    $obj = $hw->findByTeacher($id,$class);
    return $obj;
  }

  protected function teacherLogin($username, $password)
  {
    $obj =  parent::table($this->table)
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
}
