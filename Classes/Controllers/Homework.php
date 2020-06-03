<?php

namespace Classes\Controllers;


use Classes\Models\HomeworkModel;


class Homework extends HomeworkModel
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
      $array = $this->getHomeworkByPK($id);     
      foreach ($array as $key => $value) {
         $this->{$key} = $value;
      }
      return $this;
   }

   public function All()
   {
      return $this->getAllHomeworks();
   }

   // public function findByClassForTeacher($class)
   // {
   //    return $this->getHomeworksByClassForTeachers($class);
   // }
   
   public function doneHomeworks()   
   {
      if (!isset($this->{$this->primary_key})) {
         throw new \Exception('Primero debe de buscar una tarea');
       }
      return $this->getDoneHomeworksByHomeworkId($this->{$this->primary_key});
   }
   
   public function findByClassForStudents($class,$date = null)
   {
      return $this->getHomeworksByClassForStudents($class,$date);
   }

   public function findByTeacher($id,$class = false,$all=true)
   {    
      return $this->getHomeworksForTeachers($id,$class,$all);
     
   }

   
}
