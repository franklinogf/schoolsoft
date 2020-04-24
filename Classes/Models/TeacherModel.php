<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\DataBase\DB;
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
    $obj =  DB::table($this->table)->where($this->primary_key, $pk)->first();
    return $obj;
  }


  protected function getAllTeachers()
  {

    $obj =  DB::table($this->table)->orderBy('apellidos')->get();
    return $obj;
  }

  protected function getTeacherClasses($id)
  {
    $year = $this->info('year');
    $obj =  DB::table('cursos')
      ->where([
        ['year', $year],
        ['id', $id]
      ])->orderBy('curso','ASC')->get();
    return $obj;
  }

  protected function getHomeStudents($grade)
  {
    $year = $this->info('year');
    $obj =  DB::table(StudentModel::TABLE)
      ->where([
        ['grado', $grade],
        ['year', $year],
        ['fecha_baja', '0000-00-00']
      ])
      ->orderBy('apellidos')->get();
      

    return $obj;
  }

  protected function getLastTeacherTopic($id)
  {

    $query = "SELECT `e`.`id`,`e`.`titulo`,`c`.`curso`,`c`.`desc1`,`d`.`fecha`,`d`.`hora` FROM `detalle_foro_entradas` as `d`
            INNER JOIN `foro_entradas` AS `e` ON `e`.`id` = `d`.`entrada_id`
            INNER JOIN `cursos` AS `c` ON `c`.`curso` = `e`.`curso`
            WHERE `c`.`id`= ? AND `c`.`year`=? AND `e`.`year`= ? AND `e`.`estado`='a'
            ORDER BY `d`.`fecha` DESC,`d`.`hora` DESC LIMIT 1";

    $year = $this->info('year');
    return $this->selectOne($query, [$id, $year, $year]);
  }
  protected function getTeacherByUser($username)
  {
    $obj =  DB::table($this->table)
      ->where('usuario', $username)->first();
    return $obj;
  }

  protected function teacherLogin($username, $password)
  {
    $obj =  DB::table($this->table)
      ->where([
        ['usuario', $username],
        ['clave', $password]
      ])->first();

    return $obj;
  }

  protected function updateTeacher($propsArray)
  {

    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
