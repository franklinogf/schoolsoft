<?php

namespace Classes\Models;

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
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    return $this->select($query,[$pk]);
  }


  protected function getAllTeachers()
  {
    $query = "SELECT * FROM {$this->table} ORDER BY apellidos";
    return $this->selectAll($query);
  }
 
  protected function getTeacherClasses($id)
  {
    $query = "SELECT * FROM `cursos` where  `year` = ? AND `id` = ?  ORDER BY curso";    
    $year = $this->get('year');
    return $this->selectAll($query,[$year,$id]);    
  }

  protected function getHomeStudents($grade)
  {
    $table = StudentModel::TABLE;
    $query = "SELECT * FROM {$table} WHERE `grado` = ? AND `year`= ? AND `fecha_baja`='0000-00-00' ORDER BY `apellidos`,`usuario`";
    $year = $this->get('year');
    return $this->selectAll($query,[$grade,$year]);
  }

  protected function getLastTeacherTopic($id)
  {

    $query = "SELECT `e`.`id`,`e`.`titulo`,`c`.`curso`,`c`.`desc1`,`d`.`fecha`,`d`.`hora` FROM `detalle_foro_entradas` as `d`
            INNER JOIN `foro_entradas` AS `e` ON `e`.`id` = `d`.`entrada_id`
            INNER JOIN `cursos` AS `c` ON `c`.`curso` = `e`.`curso`
            WHERE `c`.`id`= ? AND `c`.`year`=? AND `e`.`year`= ? AND `e`.`estado`='a'
            ORDER BY `d`.`fecha` DESC,`d`.`hora` DESC LIMIT 1";
  
  $year = $this->get('year');  
  return $this->select($query,[$id,$year,$year]);
  }
  protected function getTeacherByUser($username)
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario= ? ";
    return $this->select($query,[$username]);
  }

  protected function teacherLogin($username, $password)
  {
   
    $query = "SELECT * FROM {$this->table} WHERE usuario = ? AND clave = ?";  

    return $this->select($query,[$username,$password]);
  }

  protected function updateTeacher($propsArray)
  {

    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
