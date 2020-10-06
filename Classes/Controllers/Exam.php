<?php

namespace Classes\Controllers;


use Classes\Models\ExamModel;


class Exam extends ExamModel
{
   public static $letters = [
      '1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E',
      '6' => 'F', '7' => 'G', '8' => 'H', '9' => 'I', '10' => 'J',
      '11' => 'K', '12' => 'L', '13' => 'M', '14' => 'N', '15' => 'O',
      '16' => 'P', '17' => 'Q', '18' => 'R', '19' => 'S', '20' => 'T',
      '21' => 'U', '22' => 'V', '23' => 'W', '24' => 'X', '25' => 'Y', '26' => 'Z'
   ];
   public function __construct($value = '')
   {

      parent::__construct();
      if ($value !== '') {
         $this->findById($value);
      }
   }
   public function findById($id)
   {
      $array = $this->getExamByPK($id);
      foreach ($array as $key => $value) {
         $this->{$key} = $value;
      }
      return $this;
   }

   public function All()
   {
      return $this->getAllExams();
   }

   public function findByClassForTeacher($class)
   {
      return $this->getExamsByClassForTeachers($class);
   }

   public function doneExams()
   {
      if (!isset($this->{$this->primary_key})) {
         throw new \Exception('Primero debe de buscar un examen');
      }
      return $this->getDoneExamsByExamId($this->{$this->primary_key});
   }

   public function findByClassForStudents($class, $date = null, $time = false)
   {
      return $this->getExamsByClassForStudents($class, $date, $time);
   }

   public function findByTeacher($id, $class = false)
   {
      if (!$class) {
         return $this->getExamsByTeacherId($id);
      } else {
         return $this->getExamsByTeacherIdAndClass($id, $class);
      }
   }
}
