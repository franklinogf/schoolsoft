<?php

namespace Classes\Controllers;

use Classes\Models\ParentsModel;


class Parents extends ParentsModel
{
  public function __construct($value = '')
  {
    parent::__construct();
    if ($value !== '') {
      $this->findPK($value);
    }
  }

  public function findPK($pk)
  {
    $array = $this->getParentsByPK($pk);
    if ($array) {
      foreach ($array as $key => $value) {
        $this->{$key} = $value;
      }
      return $this;
    }
    return false;
  }

  public function login($username, $password)
  {

    if ($array = $this->parentsLogin($username, $password)) {
      foreach ($array as $key => $value) {
        $this->{$key} = $value;
      }

      $this->logged = true;
    } else {

      $this->logged = false;
    }
    return $this;
  }

  public function kids($table = 'year')
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    $student = new Student();
    return $student->findById($this->id, $table);
  }




  public function save()
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));
    if (count($propsArray) > 0) {      
      if (isset($this->{$this->primary_key})) {
        $this->updateParents($propsArray);
      }
    } else {
      $this->exception('Debe de asignar valor a las propiedades en primer lugar');
    }
  }

  private function exception($message = "Primero debe de buscar la información de los padres")
  {
    throw new \Exception($message);
  }
}
