<?php

namespace Classes\Controllers;

use Classes\Models\StudentModel;


class Student extends StudentModel
{
  public $imagen;
  public $genero;
  public $nombre;
  public $apellidos;
  public $ss;

  public function __construct($value = '')
  {
    parent::__construct();
    if ($value !== '') {

      if (strpos($value, '-')) {
        $this->findBySS($value);
      } else {
        $this->findPK($value);
      }
    }
  }

  public function All($year = null, $includeUnenrolled = false)
  {
    return $this->getAllStudents($year, $includeUnenrolled);
  }

  public function findPK($pk)
  {
    $array = $this->getStudentByPK($pk);
    if ($array) {
      foreach ($array as $key => $value) {
        $this->{$key} = $value;
      }
      return $this;
    }
    return false;
  }

  public function findBySS($ss, $table = 'year')
  {
    $array = $this->getStudentBySS($ss, $table);
    if(!$array) return false;
    foreach ($array as $key => $value) {
      $this->{$key} = $value;
    }
    return $this;
  }

  public function findById($id, $table = 'year')
  {
    return $this->getStudentsById($id, $table);
  }

  public function fullName($utf8Decode = false)
  {
    // if (!isset($this->{$this->primary_key})) {
    //   $this->exception();
    // }
    $fullName = $utf8Decode ? utf8_decode("{$this->nombre} {$this->apellidos}")
      : mb_strtoupper("{$this->nombre} {$this->apellidos}", 'UTF-8');
    return $fullName;
  }
  public function profilePicture()
  {
    // if (!isset($this->{$this->primary_key})) {
    //   $this->exception();
    // }
      $picturePath = $this->imagen != ''
        ? __STUDENT_PROFILE_PICTURE_URL . $this->imagen
        : ($this->genero === 'F' || $this->genero === '1'
          ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
          : __NO_PROFILE_PICTURE_STUDENT_MALE);
    

    return $picturePath;
  }
  public function lastTopic()
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getLastStudentTopic($this->{$this->primary_key});
  }
  public function lastCommentedTopic()
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getLastCommentedStudentTopic($this->{$this->primary_key});
  }
  public function unreadMessages()
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getUnreadMessages($this->{$this->primary_key});
  }

  public function classes()
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getStudentClasses($this->ss);
  }
  public function homeworks($date = null)
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getStudentHomeworks($this->ss, $date);
  }


  public function doneHomework($id_hw)
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getStudentDoneHomeworkById($this->{$this->primary_key}, $id_hw);
  }

  public function exams($date = null, $time = false)
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getStudentExams($this->ss, $date, $time);
  }

  public function doneExam($id_exam)
  {
    if (!isset($this->{$this->primary_key})) {
      $this->exception();
    }
    return $this->getStudentDoneExamById($this->{$this->primary_key}, $id_exam);
  }

  public function login($username, $password)
  {

    if ($array = $this->studentLogin($username, $password)) {
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
    return $this->getStudentByUser($username);
  }
  public function findByClass($class, $table = 'padres', $summer = false)
  {
    return $this->getStudentsByClass($class, $table, $summer);
  }
  public function findByCurs2($ss, $table = 'padres', $summer = false)
  {
    return $this->getStudentsByCurs($ss, $table, $summer);
  }

  public function findByGrade($grade, $year = null, $table = 'year')
  {
    return $this->getStudentsByGrade($grade, $table, $year);
  }

  public function save($type = 'edit')
  {
    // get self public class, no parents classes
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));
    if (count($propsArray) > 0) {
      if (isset($this->{$this->primary_key}) && $type === 'edit') {
        $this->updateStudent($propsArray);
      } else {
        $this->addStudent($propsArray);
      }
    } else {
      $this->exception('Debe de asignar valor a las propiedades en primer lugar');
    }
  }

  public function delete()
  {
    $propsArray = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));
    if (count($propsArray) > 0) {
      if (isset($this->{$this->primary_key})) {
        $this->deleteStudent();
      }
    } else {
      $this->exception('Debe de asignar valor a las propiedades en primer lugar');
    }
  }

  private function exception($message = "Primero debe de buscar un estudiante")
  {
    throw new \Exception($message);
  }
}
