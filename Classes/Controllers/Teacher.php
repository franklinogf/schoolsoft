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
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $this;
  }

  public function classes()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeacherClasses($this->id);
  }

  public function homeStudents()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getHomeStudents($this->grado);
  }

  public function profilePicture()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }

    if ($this->foto_name != '') {
      $picturePath = __TEACHER_PROFILE_PICTURE . $this->foto_name;
    } else {
      $picturePath = __NO_PROFILE_PICTURE;
    }

    return $picturePath;
  }

  public function lastTopic()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getLastTeacherTopic($this->id);
  }

  public function topicsByClass($class)
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersTopicsByClass($this->id, $class);
  }

  public function topics()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersTopics($this->id);
  }
  public function homeworks($class = false)
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    if ($class) {
      return $this->getTeachersHomeworksByClass($this->id, $class);
    } else {
      return $this->getTeachersHomeworks($this->id);
    }
  }


  public function fullName()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    $fullName = ucwords(strtolower("{$this->nombre} {$this->apellidos}"));
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

  public function save()
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));

    if (count($propsArray) > 0) {
      if (isset($this->{$this->primary_key})) {
        $this->updateTeacher($propsArray);
      } else {
        echo 'insert <hr>';
      }
    } else {
      throw new \Exception('Debe de asignar valor a las propiedades en primer lugar');
    }
  }
}
