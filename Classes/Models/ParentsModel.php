<?php

namespace Classes\Models;

use Classes\Controllers\School;

class ParentsModel extends School
{
  private $table = 'madre';
  protected $primary_key = 'id';

  public function __construct()
  {
    parent::__construct();
  }

  protected function parentsLogin($username, $password)
  {
    $obj = parent::table($this->table)->where([
      ['usuario', $username],
      ['clave', $password]
    ])->first();
    return $obj;
  }
  protected function getParentsByPK($pk)
  {
    $obj = parent::table($this->table, !__COSEY)
      ->where($this->primary_key, $pk)->first();

    return $obj;
  }

  protected function updateParents($propsArray)
  {
    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
