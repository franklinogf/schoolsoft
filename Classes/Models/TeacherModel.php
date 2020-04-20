<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Models\StudentModel;

class TeacherModel extends School
{
  private $table = 'profesor';
  private $primary_key = 'id';
  const TABLE = 'profesor';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getTeacherByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    return $this->selectTable($query,[$pk]);
  }


  protected function getAllTeachers()
  {
    $query = "SELECT * FROM {$this->table} ORDER BY apellidos";
    return $this->selectTable($query);
  }

  protected function getTeacherClasses($id)
  {
    $query = "SELECT * FROM `cursos` where  `year` = ? AND `id` = ?  ORDER BY curso";    
    $year = $this->get('year');
    return $this->selectTable($query,[$year,$id]);

    return $this->toObject($obj);
  }

  protected function getHomeStudents($grade)
  {
    $table = StudentModel::TABLE;
    $query = "SELECT * FROM {$table} WHERE `grado` = ? AND `year`= ? and `fecha_baja`='0000-00-00' ORDER BY `apellidos`,`usuario`";
    $year = $this->get('year');
    return $this->selectTable($query,[$grade,$year]);
  }

  protected function getLastTeacherTopic($id)
  {

    $query = "SELECT `e`.`id`,`e`.`titulo`,`c`.`curso`,`c`.`desc1`,`d`.`fecha`,`d`.`hora` FROM `detalle_foro_entradas` as `d`
            INNER JOIN `foro_entradas` AS `e` ON `e`.`id` = `d`.`entrada_id`
            INNER JOIN `cursos` AS `c` ON `c`.`curso` = `e`.`curso`
            WHERE `c`.`id`= ? AND `c`.`year`=? AND `e`.`year`= ? AND `e`.`estado`='a'
            ORDER BY `d`.`fecha` DESC,`d`.`hora` DESC LIMIT 1";
  
  $year = $this->get('year');  
  return $this->selectTable($query,[$id,$year,$year]);
  }

  protected function teacherLogin($username, $password)
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario= ? AND clave = ?";
    return $this->selectTable($query,[$username,$password]);
  }
}
