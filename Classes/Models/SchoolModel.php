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

    return $this->selectTable($query,[$pk]);
  }

  protected function getSchoolByUser($user = 'administrador')
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario = ?";
    return $this->selectTable($query,[$user]);
  }

  protected function getSchool()
  {
    $this->getSchoolByUser();
  }

  
}
