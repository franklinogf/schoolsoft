<?php
namespace Classes\Controllers;

use Classes\Models\StudentModel;


class Student extends StudentModel
{
  public function __construct($value = '')
  {

    parent::__construct();
    if ($value !== '') {

      if (strpos($value, '-')) {
        $this->findBySS($value);
      } else {
       $this->find($value);
      }
    }
  }

  public function All()
  {
    return $this->getAllStudents();
  }

  public function find($pk)
  {
    $array = $this->getStudentByPK($pk);    
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $this;
  }
  public function findBySS($ss)
  {
    $array = $this->getStudentBySS($ss);
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $this;
  }
  public function fullName()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un estudiante');
    }
    $fullName = ucwords(strtolower("{$this->nombre} {$this->apellidos}"));
    return $fullName;
  }

  public function classes()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un estudiante');
    }
    return $this->getStudentClasses($this->ss);
  }

  public function login($username,$password){
    
    if ($array = $this->studentLogin($username, $password)) {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
        
        $this->logged = true;   
            
    }else{
      
      $this->logged = false;
    }
    return $this;

  }
  public function findByUser($username){
    return $this->getStudentByUser($username);
  }

  public function save()
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));

    if(count( $propsArray) > 0){
      if (isset($this->{$this->primary_key})) {
        $this->updateStudent($propsArray);
      } else {
        echo 'insert <hr>';
      }
    }else{
      throw new \Exception('Debe de asignar valor a las propiedades en primer lugar');

    }

  }



}
