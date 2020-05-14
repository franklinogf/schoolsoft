<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Util;
use stdClass;

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
      $this->getExamTopics($obj);
      return $obj;
   }


   protected function getAllExams()
   {

      $obj = parent::table($this->table)
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getExamTopics($obj);
      return $obj;
   }

   protected function getDoneExamsByExamId($id)
   {
      $hw = $this->getExamByPK($id);
      $doneHw = parent::table('tareas_enviadas')->where([
         ['id_tarea', $id],
         ['id_profesor', $hw->id2],
         ['year', $this->info('year')]
      ])->get();
      $this->getExamTopics($doneHw, 'id', 't_tareas_archivos', 'id_tarea');

      return $doneHw;
   }

   protected function getExamsByTeacherId($id)
   {
      $obj = parent::table($this->table)
         ->where([
            ['id2', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getExamTopics($obj);
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
      $this->getExamTopics($obj);
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

      $this->getExamTopics($obj);

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
            // ["{$this->table}.activo", 'si'],
            ["cursos.year", $this->info('year')]
         ])
         ->orderBy("{$this->table}.fecha", 'DESC')->get();
      // $this->getExamTopics($obj);

      return $obj;
   }

   protected function getExamTopics($obj)
   {
      $tables = [
         'T_examen_fyv' => 'fvs',
         'T_examen_selec' => 'selects',
         'T_examen_parea' => 'pairs',
         'T_examen_codigo_parea' => 'pairCodes',
         'T_examen_linea' => 'lines',
         'T_examen_pregunta' => 'qas'
      ];

      $titles = [
         'fvs' => 'Falso y verdadero',
         'selects' => 'Selecciona la respuesta correcta',
         'pairs' => 'Parea',
         'lines' => 'Llena la linea en blanco',
         'qas' => 'Responde las preguntas correctamente'
      ];

      if (is_object($obj)) {
         $obj = [$obj];
      }
      foreach ($obj as $exam) {
         foreach ($tables as $table => $name) {
            $objs = parent::table($table)
               ->where('id_examen', $exam->id)->get();
            $exam->{$name} = new stdClass();
            $exam->{$name}->value = 0;
            foreach ($objs as $obj) {
               if(array_key_exists($name,$titles)) $exam->{$name}->title = $titles[$name];
               if(array_key_exists('valor',$obj)) $exam->{$name}->value = $exam->{$name}->value + $obj->valor;
               $exam->{$name}->topics[] = $obj;
            }
         }
      }
   }
}
