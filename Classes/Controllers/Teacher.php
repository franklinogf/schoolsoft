<?php

namespace Classes\Controllers;


use Classes\Models\TeacherModel;


class Teacher extends TeacherModel
{
  public function __construct($value = '')
  {

    parent::__construct();
    if ($value !== '') {
      $this->findPK($value);
    }
  }

  public function All()
  {
    return $this->getAllTeachers();
  }

  public function findPK($pk)
  {
    $array = $this->getTeacherByPK($pk);
    if (!is_object($array)) {
      return false;
    }
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $this;
  }
  public function findByGrade($grade)
  {
    return $this->getTeacherByGrade($grade);
  }
  //get all the students of a teacher from padres table
  public function getAllStudents($table = 'padres')
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getAllTeacherStudents($this->{$this->primary_key}, $table);
  }

  public function classes()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeacherClasses(id: $this->{$this->primary_key});
  }

  public function classCredit($class)
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeacherClassCredit($this->{$this->primary_key}, $class);
  }

  public function homeStudents()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getHomeStudents($this->grado);
  }

  public function unreadMessages()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getUnreadMessages($this->{$this->primary_key});
  }

  public function profilePicture()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }

    if ($this->foto_name != '') {
      $picturePath = __TEACHER_PROFILE_PICTURE_URL . $this->foto_name;
    } else {
      if ($this->genero === 'Femenino') {
        $picturePath = __NO_PROFILE_PICTURE_TEACHER_FEMALE;
      } else {
        $picturePath = __NO_PROFILE_PICTURE_TEACHER_MALE;
      }
    }

    return $picturePath;
  }

  public function lastTopic()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getLastTeacherTopic($this->{$this->primary_key});
  }

  public function topicsByClass($class)
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersTopicsByClass($this->{$this->primary_key}, $class);
  }

  public function topics()
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersTopics($this->{$this->primary_key});
  }
  public function homeworks($class = false, $all = true)
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersHomeworks($this->{$this->primary_key}, $class, $all);
  }


  public function fullName($utf8Decode = false)
  {
    if (!isset($this->{$this->primary_key})) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    $fullName = $utf8Decode ? utf8_decode("{$this->nombre} {$this->apellidos}")
      : mb_strtoupper("{$this->nombre} {$this->apellidos}", 'UTF-8');
    return $fullName;
  }

  public function login($username, $password)
  {

    if ($array = $this->teacherLogin($username, $password)) {
      foreach ($array as $key => $value) {
        $this->{$key} = $value;
      }

      $this->logged = true;
    } else {

      $this->logged = false;
    }
    return $this;
  }
  public function findByUser($username)
  {
    return $this->getTeacherByUser($username);
  }

  public function save($type = 'edit')
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));

    if (count($propsArray) > 0) {
      if (isset($this->{$this->primary_key}) && $type === 'edit') {
        $this->updateTeacher($propsArray);
      } else {
        $this->addTeacher($propsArray);
      }
    } else {
      throw new \Exception('Debe de asignar valor a las propiedades en primer lugar');
    }
  }
}
