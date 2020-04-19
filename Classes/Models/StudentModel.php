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
    $db = $this->connect();

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $pk);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_assoc();

    return (object) $obj;
  }

  protected function getStudentBySS($ss)
  {

    $query = "SELECT * FROM {$this->table} WHERE `ss` = ? AND `year` = ? ";
    $db = $this->connect();

    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('ss', $ss, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_assoc();

    return (object) $obj;
  }

  protected function getAllStudents()
  {
    $query = "SELECT * FROM {$this->table} WHERE  `year` = ? AND fecha_baja='0000-00-00' ORDER BY apellidos";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('s', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function getStudentClasses($ss)
  {
    $query = "SELECT DISTINCT curso, descripcion FROM padres WHERE  `year` = ? and ss = ? ORDER BY curso";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('ss', $year,$ss);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);

  }
  protected function studentLogin($username, $password)
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
