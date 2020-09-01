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

  public function unreadMessages()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getUnreadMessages($this->id);
  }

  public function profilePicture()
  {
    if (!isset($this->id)) {
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
  public function homeworks($class = false, $all = true)
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    return $this->getTeachersHomeworks($this->id, $class, $all);
  }


  public function fullName()
  {
    if (!isset($this->id)) {
      throw new \Exception('Primero debe de buscar un profesor');
    }
    $fullName = mb_strtoupper("{$this->nombre} {$this->apellidos}",'UTF-8');
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
