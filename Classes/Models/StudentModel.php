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
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    return $this->select($query,[$pk]);
  }

  protected function getStudentBySS($ss)
  {

    $query = "SELECT * FROM {$this->table} WHERE `ss` = ? AND `year` = ? ";
    $year = $this->get('year');

    return $this->select($query,[$ss,$year]);
  }

  protected function getAllStudents()
  {
    $query = "SELECT * FROM {$this->table} WHERE  `year` = ? AND `fecha_baja`='0000-00-00' ORDER BY `apellidos`";
    $year = $this->get('year');
    return $this->selectAll($query,[$year]);
  }

  protected function getStudentClasses($ss)
  {
    $query = "SELECT DISTINCT `curso`, `descripcion` FROM `padres` WHERE  `year` = ? and `ss` = ? ORDER BY `curso`";
    $year = $this->get('year');
    return $this->selectAll($query,[$year,$ss]);

  }
  protected function getStudentByUser($username)
  {
    $query = "SELECT * FROM {$this->table} WHERE `usuario`= ? ";
    return $this->select($query,[$username]);
  }
  protected function getStudentsByClass($class)
  {
    $query = "SELECT `e`.* FROM `padres` as `p` 
    INNER JOIN `year` AS `e` ON `p`.`ss` = `e`.`ss` 
    WHERE `p`.`curso` = ? AND `e`.`year` = ? AND `p`.`year` = ? and `e`.`fecha_baja`='0000-00-00'
    ORDER BY `e`.`apellidos`";
    
    $year = $this->get('year');
    return $this->selectAll($query,[$class,$year,$year]);
  }

  protected function studentLogin($username, $password)
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario= ? AND clave = ?";
    return $this->select($query,[$username,$password]);
  }

  protected function updateStudent($propsArray)
  {
    $this->updateTable($this->table,$this->primary_key,$this->{$this->primary_key},$propsArray);
  }
}
