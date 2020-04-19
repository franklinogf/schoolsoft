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
    return $array;
  }
  public function fullName()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un estudiante');
    }
    $fullName = ucwords(strtolower("{$this->nombre} {$this->apellidos}"));
    return $fullName;
  }

  public function classes()
  {
    if (!isset($this->ss)) {
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
}
