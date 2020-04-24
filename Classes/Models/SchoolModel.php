<?php

namespace Classes\Models;

use Classes\DataBase\DB;


class SchoolModel extends DB
{
  private $table = 'colegio';
  protected $primary_key = 'id';
  const TABLE = 'colegio';

  protected function getSchoolByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";    

    return $this->selectOne($query,[$pk]);
  }

  protected function getSchoolByUser($user = 'administrador')
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario = ?";
    return $this->selectOne($query,[$user]);
  }

  protected function getSchool()
  {
    return $this->getSchoolByUser();
  }

  
}
