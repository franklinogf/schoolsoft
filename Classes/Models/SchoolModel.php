<?php
namespace Classes\Models;

use Classes\DataBase;


class SchoolModel extends DataBase
{
  private $table = 'colegio';
  private $primary_key = 'id';
  const TABLE = 'colegio';

  protected function getSchoolByPK($pk)
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

  protected function getSchoolByUser($user = 'administrador')
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario = ?";
    $db = $this->connect();

    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if($obj = $result->fetch_assoc() ){    
      return (object) $obj;
     }
     return false;
  }
  
  protected function getSchool()
  {
    $this->getSchoolByUser();
  }
}
