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

    $obj = parent::table($this->table)->where($this->primary_key, $pk)->first();
    return $obj;
  }

  protected function getTopicsByClass($class)
  {
    $year = $this->info('year');
    $obj = parent::table($this->table)->where([
      ['curso', $class],
      ['year', $year]
    ])->orderBy('fecha DESC, hora DESC')->get();
    return $obj;
  }


  protected function getAllTopics()
  {
    $year = $this->info('year');
    $obj = parent::table($this->table)->where('year', $year)->orderBy('fecha')->get();
    return $obj;
  }

  protected function getTopicComments($id)
  {
    $obj = parent::table('detalle_foro_entradas')->where('entrada_id', $id)->orderBy('fecha DESC,hora DESC')->get();
    return $obj;
  }

  protected function insertTopicComment($id, $type, $id_topic, $desc)
  {
    $year = $this->info('year');

    parent::table('detalle_foro_entradas')->insert([
      "creador_id" => $id,
      "tipo" => $type,
      "entrada_id" => $id_topic,
      "descripcion" => $desc,
      "fecha" => Util::date(),
      "hora" =>  Util::time(),
      "year" => $year
    ]);
  }
  protected function updateTopic($propsArray)
  {

    $this->updateTable($this->table, $this->primary_key, $this->{$this->primary_key}, $propsArray);
  }

  protected function insertTopic($propsArray)
  {

    $this->insertTable($this->table, $propsArray);
  }
}
