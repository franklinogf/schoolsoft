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

   public function findByClass($class)
   {
      return $this->getHomeworksByClass($class);
   }

   public function findByTeacher($id,$class = false)
   {
     if(!$class){
      return $this->getHomeworksByTeacherId($id);
     }else{
      return $this->getHomeworksByTeacherIdAndClass($id,$class);
     }
   }

   
}
