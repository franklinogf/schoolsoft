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

    $obj = parent::table($this->table)->where($this->primary_key,$pk)->first();
    return $obj;
  }


  protected function getAllTopics()
  {
    $year = $this->info('year');
    $obj = parent::table($this->table)->where('year',$year)->orderBy('fecha')->get();
    return $obj;
  }

  protected function getTopicComments($id)
  {
    $obj = parent::table('detalle_foro_entradas')->where('entrada_id',$id)->orderBy('fecha DESC,hora DESC')->get();

    return $obj;
  }

  protected function insertTopicComments($id, $type, $id_topic, $desc)
  {
    $query = "INSERT INTO `detalle_foro_entradas` (`creador_id`,`tipo`,`entrada_id`,`descripcion`,`fecha`,`hora`,`year`)
    VALUES (?,?,?,?,?,?,?)";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->info('year');
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
