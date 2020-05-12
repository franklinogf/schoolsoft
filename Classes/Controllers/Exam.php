<?php

namespace Classes\Controllers;


use Classes\Models\ExamModel;


class Exam extends ExamModel
{
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
   
   public function findByClassForStudents($class,$date = null)
   {
      return $this->getExamsByClassForStudents($class,$date);
   }

   public function findByTeacher($id,$class = false)
   {
     if(!$class){
      return $this->getExamsByTeacherId($id);
     }else{
      return $this->getExamsByTeacherIdAndClass($id,$class);
     }
   }

   
}
