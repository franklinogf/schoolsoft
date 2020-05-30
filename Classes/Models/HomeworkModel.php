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

   protected function getDoneHomeworksByHomeworkId($id)
   {
      $hw = $this->getHomeworkByPK($id);
      $doneHw = parent::table('tareas_enviadas')->where([
         ['id_tarea',$id],
         ['id_profesor',$hw->id2],
         ['year',$this->info('year')]
      ])->get();
      $this->getFiles($doneHw,'id','t_tareas_archivos','id_tarea');

      return $doneHw;

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
         ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
            ["cursos.year", $this->info('year')]
         ])->orderBy("{$this->table}.{$this->primary_key}", 'DESC')->get();

      $this->getFiles($obj);

      return $obj;
   }
   protected function getHomeworksByClassForStudents($class, $date = null)
   {
      $date = $date ? $date : Util::date();
      $obj = parent::table($this->table)->select("{$this->table}.*,cursos.desc1 as `desc`")
         ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
            ["{$this->table}.fec_out", '>=', $date],
            ["{$this->table}.enviartarea",'si'],
            ["cursos.year", $this->info('year')]
         ])
         ->orderBy("{$this->table}.fec_out", 'DESC')->get();
      $this->getFiles($obj);

      return $obj;
   }

   protected function getFiles($obj,$whereVal = null,$table = 'T_archivos',$whereCol = null)
   {
      $whereVal = !$whereVal ? $this->primary_key : $whereVal;
      $whereCol = !$whereCol ? $whereVal : $whereCol;
      if (is_object($obj)) {
         $obj = [$obj];
      }
      foreach ($obj as $homework) {
         $files = parent::table($table)
            ->where($whereCol, $homework->{$whereVal})->get();
         if ($files) {
            foreach ($files as $file) {
               $homework->archivos[] = $file;
            }
         }
      }
   }
}
