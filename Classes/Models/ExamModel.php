<?php

namespace Classes\Models;

use Classes\Controllers\School;
use Classes\Util;
use stdClass;

class ExamModel extends School
{
   private $table = 'T_examenes';
   protected $primary_key = 'id';
   const TABLE = 'T_examenes';

   public function __construct()
   {
      parent::__construct();
   }

   protected function getExamByPK($pk)
   {
      $obj =  parent::table($this->table,!__COSEY)
         ->where($this->primary_key, $pk)->first();
      $this->getExamTopics($obj);
      return $obj;
   }


   protected function getAllExams()
   {

      $obj = parent::table($this->table,!__COSEY)
         ->orderBy($this->primary_key, 'DESC')->get();
      $this->getExamTopics($obj);
      return $obj;
   }

   protected function getDoneExamsByExamId($id)
   {
      $hw = $this->getExamByPK($id);
      $doneHw = parent::table('tareas_enviadas',!__COSEY)->where([
         ['id_tarea', $id],
         ['id_profesor', $hw->id2],
         ['year', $this->year()]
      ])->get();
      $this->getExamTopics($doneHw);

      return $doneHw;
   }

   protected function getExamsByTeacherId($id)
   {
      $obj = parent::table($this->table,!__COSEY)
         ->where([
            ['id_maestro', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      // $this->getExamTopics($obj);
      return $obj;
   }
   protected function getExamsByTeacherIdAndClass($id, $class)
   {
      $obj = parent::table($this->table,!__COSEY)
         ->where([
            ['curso', $class],
            ['id_maestro', $id]
         ])
         ->orderBy($this->primary_key, 'DESC')->get();
      // $this->getExamTopics($obj);
      return $obj;
   }

   protected function getExamsByClassForTeachers($class)
   {
      if(__COSEY){
         $obj = parent::table($this->table,!__COSEY)->select("{$this->table}.*,padres.desc1 as `desc`")
         ->join('padres', "padres.curso", "=", "{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
               ["padres.year", $this->year()]
         ])->orderBy("{$this->table}.{$this->primary_key}", 'DESC')->get();
      }else{
         $obj = parent::table($this->table,!__COSEY)->select("{$this->table}.*,cursos.desc1 as `desc`")
         ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
         ->where([
            ["{$this->table}.curso", $class],
               ["cursos.year", $this->year()]
         ])->orderBy("{$this->table}.{$this->primary_key}", 'DESC')->get();
      }

      $this->getExamTopics($obj);

      return $obj;
   }
   protected function getExamsByClassForStudents($class, $date = null, $time = false)
   {
      $date = $date ? $date : Util::date();
      if($time){
         if(__COSEY){
            $obj = parent::table($this->table,!__COSEY)->select("{$this->table}.*,padres.descripcion as `desc`")
            ->join('padres', "padres.curso", "=", "{$this->table}.curso")
            ->where([
               ["{$this->table}.curso", $class],
               ["{$this->table}.activo", 'si'],
                  ["padres.year", $this->year()],
               ["{$this->table}.fecha", $date]
            ])
            ->WhereRaw("AND ? >= {$this->table}.hora AND ? <= {$this->table}.hora_final",[
               Util::time(),
               Util::time()
            ])
            ->orderBy("{$this->table}.fecha", 'DESC')->get();    
         }      else{
            $desc = __LANG === 'es' ? 'desc1' : 'desc2';
            $obj = parent::table($this->table)->select("{$this->table}.*,cursos.$desc as `desc`")
            ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
            ->where([
               ["{$this->table}.curso", $class],
               ["{$this->table}.activo", 'si'],
                  ["cursos.year", $this->year()],
               ["{$this->table}.fecha", $date]
            ])
            ->WhereRaw("AND ? >= {$this->table}.hora AND ? <= {$this->table}.hora_final",[
               Util::time(),
               Util::time()
            ])
            ->orderBy("{$this->table}.fecha", 'DESC')->get();    
         }
         }else{
            if(__COSEY){
               $obj = parent::table($this->table,!__COSEY)->select("DISTINCT {$this->table}.*,padres.descripcion as `desc`")
               ->join('padres', "padres.curso", "=", "{$this->table}.curso")
               ->where([
                  ["{$this->table}.curso", $class],
                  ["{$this->table}.activo", 'si'],
                  ["padres.year", $this->year()],
                  ["{$this->table}.fecha", '>=', $date]
               ])
               ->orderBy("{$this->table}.fecha", 'DESC')->get();
            }else{
               $desc = __LANG === 'es' ? 'desc1' : 'desc2';
               $obj = parent::table($this->table)->select("{$this->table}.*,cursos.$desc as `desc`")
               ->join('cursos', "cursos.curso", "=", "{$this->table}.curso")
               ->where([
                  ["{$this->table}.curso", $class],
                  ["{$this->table}.activo", 'si'],
                  ["cursos.year", $this->year()],
                  ["{$this->table}.fecha", '>=', $date]
               ])
               ->orderBy("{$this->table}.fecha", 'DESC')->get();
            }
      }     

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

      if(__LANG === "es"){
         $titles = [
            'fvs' => 'Falso y verdadero',
            'selects' => 'Selecciona la respuesta correcta',
            'pairs' => 'Parea',
            'lines' => 'Llena la linea en blanco',
            'qas' => 'Responde las preguntas correctamente'
         ];
      }else{
         $titles = [
            'fvs' => 'False and true',
            'selects' => 'Select the correct answer',
            'pairs' => 'Match',
            'lines' => 'Fill the blank line',
            'qas' => 'Answer the questions correctly'
         ];
      }

      if (is_object($obj)) {
         $obj = [$obj];
      }
      foreach ($obj as $exam) {
         foreach ($tables as $table => $name) {
            $objs = parent::table($table,!__COSEY)
               ->where('id_examen', $exam->id)->get();
            $exam->{$name} = new stdClass();
            $exam->{$name}->value = 0;
            foreach ($objs as $obj) {
               if (array_key_exists($name, $titles)) $exam->{$name}->title = $titles[$name];
               if (array_key_exists('valor', $obj)) $exam->{$name}->value = $exam->{$name}->value + $obj->valor;
               $exam->{$name}->topics[] = $obj;
            }
         }
      }
   }
}
