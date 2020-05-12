<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Util;

class ExamModel extends School
{
   private $table = 't_examenes';
   protected $primary_key = 'id';
   const TABLE = 't_examenes';

   public function __construct()
   {
      parent::__construct();
   }

   protected function getExamByPK($pk)
   {
      $obj =  parent::table($this->table)
         ->where($this->primary_key, $pk)->first();
      $this->getFiles($obj);
      return $obj;
   }


   protected function getAllExams()
   {

      $obj = parent::table($this->table)
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getFiles($obj);
      return $obj;
   }

   protected function getDoneExamsByExamId($id)
   {
      $hw = $this->getExamByPK($id);
      $doneHw = parent::table('tareas_enviadas')->where([
         ['id_tarea',$id],
         ['id_profesor',$hw->id2],
         ['year',$this->info('year')]
      ])->get();
      $this->getFiles($doneHw,'id','t_tareas_archivos','id_tarea');

      return $doneHw;

   }

   protected function getExamsByTeacherId($id)
   {
      $obj = parent::table($this->table)
         ->where([
            ['id2', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getFiles($obj);
      return $obj;
   }
   protected function getExamsByTeacherIdAndClass($id, $class)
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

   protected function getExamsByClassForTeachers($class)
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
   protected function getExamsByClassForStudents($class, $date = null)
   {
      $date = $date ? $date : Util::date();
      $obj = parent::table($this->table)->select("{$this->table}.*,cursos.desc1 as `desc`")
         ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
            ["{$this->table}.fecha", '>=', $date],
            ["{$this->table}.activo", 'si'],
            ["cursos.year", $this->info('year')]
         ])
         ->orderBy("{$this->table}.fecha", 'DESC')->get();
      // $this->getFiles($obj);

      return $obj;
   }

   protected function getFiles($obj,$whereVal = null,$table = 'T_archivos',$whereCol = null)
   {
      $whereVal = !$whereVal ? $this->primary_key : $whereVal;
      $whereCol = !$whereCol ? $whereVal : $whereCol;
      if (is_object($obj)) {
         $obj = [$obj];
      }
      foreach ($obj as $Exam) {
         $files = parent::table($table)
            ->where($whereCol, $Exam->{$whereVal})->get();
         if ($files) {
            foreach ($files as $file) {
               $Exam->archivos[] = $file;
            }
         }
      }
   }
}
