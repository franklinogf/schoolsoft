<?php

namespace Classes\Models;

use Classes\Controllers\School;

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
      ->select('DISTINCT curso, descripcion')
      ->where([
        ['year', $year],
        ['ss', $ss]
      ])->orderBy('curso')->get();
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
      ->select('year.*')
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

  protected function updateStudent($propsArray)
  {
    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
