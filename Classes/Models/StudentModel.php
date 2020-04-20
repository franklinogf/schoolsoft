<?php
namespace Classes\Models;

use Classes\Controllers\School;

class StudentModel extends School
{
  private $table = 'year';
  private $primary_key = 'mt';
  const TABLE = 'year';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getStudentByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    return $this->selectTable($query,[$pk]);
  }

  protected function getStudentBySS($ss)
  {

    $query = "SELECT * FROM {$this->table} WHERE `ss` = ? AND `year` = ? ";
    $year = $this->get('year');

    return $this->selectTable($query,[$ss,$year]);
  }

  protected function getAllStudents()
  {
    $query = "SELECT * FROM {$this->table} WHERE  `year` = ? AND fecha_baja='0000-00-00' ORDER BY apellidos";
    $year = $this->get('year');
    return $this->selectTable($query,[$year]);
  }

  protected function getStudentClasses($ss)
  {
    $query = "SELECT DISTINCT curso, descripcion FROM padres WHERE  `year` = ? and ss = ? ORDER BY curso";
    $year = $this->get('year');
    return $this->selectTable($query,[$year,$ss]);

  }
  protected function studentLogin($username, $password)
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario= ? AND clave = ?";
    return $this->selectTable($query,[$username,$password]);
  }

  protected function updateStudent($propsArray)
  {
    $this->updateTable($this->table,$this->primary_key,$this->{$this->primary_key},$propsArray);
  }
}
