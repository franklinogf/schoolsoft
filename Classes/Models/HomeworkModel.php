<?php

namespace Classes\Models;

use Classes\Controllers\School;

class HomeworkModel extends School
{
   private $table = 'tbl_documentos';
   protected $primary_key = 'id_documento';
   const TABLE = 'tbl_documentos';

   public function __construct()
   {
      parent::__construct();
   }

   protected function getHomeworkByPK($pk)
   {
      $obj =  parent::table($this->table)
      ->where($this->primary_key, $pk)->first();
      $this->getFiles($obj); 
      return $obj;
   }


   protected function getAllHomeworks()
   {

      $obj = parent::table($this->table)
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getFiles($obj);
      return $obj;
   }

   protected function getHomeworksByTeacherId($id)
   {
      $obj = parent::table($this->table)
         ->where([
            ['id2', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getFiles($obj);
      return $obj;
   }
   protected function getHomeworksByTeacherIdAndClass($id,$class)
   {
      $obj = parent::table($this->table)
         ->where([
            ['curso', $class],
            ['id2', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getFiles($obj);
      return $obj;
   }

   protected function getHomeworksByClass($class)
   {
      $obj = parent::table($this->table)
         ->where([
            ['curso', $class]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();

      $this->getFiles($obj);

      return $obj;
   }

   protected function getFiles($obj)
   {
     if(is_object($obj)){
        $obj = [$obj];
     }
      foreach ($obj as $homework) {
         $files = parent::table('T_archivos')
            ->where($this->primary_key, $homework->{$this->primary_key})->get();
         if ($files) {
            foreach ($files as $file) {
               $homework->archivos[] = $file;
            }
         }
      }
   }
}
