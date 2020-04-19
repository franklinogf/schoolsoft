<?php

namespace Classes\Models;

use Classes\Util;
use Classes\Controllers\School;


class TopicModel extends School
{
  private $table = 'foro_entradas';
  private $primary_key = 'id';

  public function __construct()
  {
    parent::__construct();
  }

  protected function getTopicByPK($pk)
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


  protected function getAllTopics()
  {
    $query = "SELECT * FROM {$this->table} AND `year` = ? ORDER BY fecha";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $stmt->bind_param('s', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function getTopicComments($id)
  {
    $query = "SELECT * FROM detalle_foro_entradas WHERE entrada_id = ? ORDER BY fecha DESC,hora DESC";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $obj = $result->fetch_all(MYSQLI_ASSOC);

    return $this->toObject($obj);
  }

  protected function insertTopicComments($id,$type,$id_topic,$desc)
  {
    $query = "INSERT INTO detalle_foro_entradas (creador_id,tipo,entrada_id,descripcion,fecha,hora,year)
    VALUES (?,?,?,?,?,?,?)";
    $db = $this->connect();
    $stmt = $db->prepare($query);
    $year = $this->get('year');
    $date = Util::date();
    $time = Util::time();
    $stmt->bind_param('isissss', $id,$type,$id_topic,$desc,$date,$time,$year);
    $stmt->execute();     
  }
}
