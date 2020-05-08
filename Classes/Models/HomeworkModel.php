<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Util;

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
   protected function getHomeworksByTeacherIdAndClass($id, $class)
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

   protected function getHomeworksByClassForTeachers($class)
   {
      $obj = parent::table($this->table)->select("{$this->table}.*,cursos.desc1 as `desc`")
      ->join('cursos',"cursos.curso","=","{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
            ["cursos.year", $this->info('year')]
         ])->orderBy("{$this->table}.{$this->primary_key}", 'DESC')->get();

      $this->getFiles($obj);

      return $obj;
   }
   protected function getHomeworksByClassForStudents($class)
   {
      $obj = parent::table($this->table)->select("{$this->table}.*,cursos.desc1 as `desc`")
      ->join('cursos',"cursos.curso","=","{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
            ["{$this->table}.fec_out", '>=', Util::date()],
            ["cursos.year", $this->info('year')]
         ])
         ->orderBy("{$this->table}.fec_out", 'DESC')->get();  
      $this->getFiles($obj);

      return $obj;
   }

   protected function getFiles($obj)
   {
      if (is_object($obj)) {
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
