<?php

namespace Classes\Models;

use Classes\DataBase\DB;


class SchoolModel extends DB
{
  private $table = 'colegio';
  protected $primary_key = 'usuario';
  const TABLE = 'colegio';

  protected function getSchoolByPK($pk)
  {
    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";

    return $this->selectOne($query, [$pk]);
  }

  protected function getSchoolByUser($user = 'administrador')
  {
    $query = "SELECT * FROM {$this->table} WHERE usuario = ?";
    return $this->selectOne($query, [$user]);
  }

  protected function getSchool()
  {
    return $this->getSchoolByUser();
  }
  protected function adminLogin($username, $password)
  {
    $obj =  parent::table($this->table, !__COSEY)
      ->where([
        ['usuario', $username],
        ['clave', $password]
      ])->first();

    return $obj;
  }
  protected function updateAdmin($propsArray)
  {
    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }

  protected function getAllGrades($year,$with12)
  {
    if($with12){
      $obj = DB::table("year")->select('DISTINCT grado')->where([['year', $year], ['activo', '']])->orderBy('grado')->get();
    }else{
      $obj = DB::table("year")->select('DISTINCT grado')->where([['year', $year], ['activo', '']])->whereRaw("AND grado not like '12%'")->orderBy('grado')->get();
    }
   return $obj;
  }
}
