<?php

namespace Classes\Controllers;

use Classes\Models\TopicModel;


class Topic extends TopicModel
{

  public function __construct($value = '')
  {

    parent::__construct();
    if ($value !== '') {
      $this->find($value);
    }
  }

  public function All()
  {
    return $this->getAllTopics();
  }

  public function find($pk)
  {
    $array = $this->getTopicByPK($pk);
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $array;
  }

  public function comments()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un tema');
    }
    return $this->getTopicComments($this->id);
  }

  public function newComment($creatorId, $desc, $type)
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un tema');
    }
    return $this->insertTopicComments($creatorId, $type, $this->id, $desc);
  }

  public function save()
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));

    if (isset($this->{$this->primary_key})) {
      $this->updateTopic($propsArray);
    } else {
      echo 'insert <hr>';
    }
  }
}
