<?php

namespace Classes\Models;

use Classes\Util;
use Classes\Controllers\School;


class TopicModel extends School
{
  private $table = 'foro_entradas';
  protected $primary_key = 'id';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getTopicByPK($pk)
  {

    $query = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
    return $this->select($query,[$pk]);
  }


  protected function getAllTopics()
  {
    $query = "SELECT * FROM {$this->table} AND `year` = ? ORDER BY `fecha`";
    $year = $this->get('year');
    return $this->selectAll($query,[$year]);
  }

  protected function getTopicComments($id)
  {
    $query = "SELECT * FROM `detalle_foro_entradas` WHERE `entrada_id` = ? ORDER BY `fecha` DESC,`hora` DESC";
    return $this->selectAll($query,[$id]);
  }

  protected function insertTopicComments($id, $type, $id_topic, $desc)
  {
    $query = "INSERT INTO `detalle_foro_entradas` (`creador_id`,`tipo`,`entrada_id`,`descripcion`,`fecha`,`hora`,`year`)
    VALUES (?,?,?,?,?,?,?)";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $date = Util::date();
    $time = Util::time();
    $stmt->bind_param('isissss', $id, $type, $id_topic, $desc, $date, $time, $year);
    $stmt->execute();
  }
  protected function updateTopic($propsArray)
  {

    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }
}
