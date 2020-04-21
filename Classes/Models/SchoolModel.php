<?php

namespace Classes\Models;

use Classes\DataBase;


class SchoolModel extends DataBase
{
  private $table = 'colegio';
  protected $primary_key = 'id';
  const TABLE = 'colegio';

  protected function getSchoolByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";    

    return $this->select($query,[$pk]);
  }

  protected function getSchoolByUser($user = 'administrador')
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario = ?";
    return $this->select($query,[$user]);
  }

  protected function getSchool()
  {
    return $this->getSchoolByUser();
  }

  
}
