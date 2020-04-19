<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Models\StudentModel;

class TeacherModel extends School
{
  private $table = 'profesor';
  private $primary_key = 'id';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getTeacherByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    $db = $this->connect();

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $pk);
    $stmt->execute();
    $result = $stmt->get_result();
    if($obj = $result->fetch_assoc() ){    
      return (object) $obj;
     }
     return false;
  }


  protected function getAllTeachers()
  {
    $query = "SELECT * FROM {$this->table} ORDER BY apellidos";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function getTeacherClasses($id)
  {
    $query = "SELECT DISTINCT curso, descripcion FROM padres WHERE  `year` = ? and id = ? ORDER BY curso";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('si', $year, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function getHomeStudents($grade)
  {
    $table = StudentModel::TABLE;
    $query = "SELECT * FROM {$table} WHERE grado = ? AND year= ? and fecha_baja='0000-00-00' ORDER BY apellidos";
    $db = $this->connect();
    $year = $this->get('year');
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $grade, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function getLastTeacherTopic($id)
  {

    $query = "SELECT e.id,e.titulo,c.curso,c.desc1,d.fecha,d.hora FROM `detalle_foro_entradas` as d
	INNER JOIN foro_entradas AS e ON e.id = d.entrada_id
	INNER JOIN cursos AS c ON c.curso = e.curso
	WHERE c.id= ? AND c.year= ?
  ORDER BY d.fecha DESC,d.hora DESC LIMIT 1";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('is', $id, $year);
    $stmt->execute();
    $result = $stmt->get_result();
   if($obj = $result->fetch_assoc() ){
    
    return (object) $obj;
   }
   return false;
  }

  protected function teacherLogin($username, $password)
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario= ? AND clave = ?";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
      $obj = $result->fetch_assoc();
      return (object) $obj;
    }

    return false;
  }
}
