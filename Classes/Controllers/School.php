<?php

namespace Classes\Controllers;



use Classes\Models\SchoolModel;

class School extends SchoolModel
{
   protected $props = [];

   public function login($username, $password)
   {

      if ($array = $this->adminLogin($username, $password)) {
         foreach ($array as $key => $value) {
            $this->{$key} = $value;
         }

         $this->logged = true;
      } else {

         $this->logged = false;
      }
      return $this;
   }

   public function __construct($user = 'administrador')
   {
      $array = $this->getSchoolByUser($user);
      foreach ($array as $key => $value) {
         $this->set($key, $value);
      }
   }
   private function set($key, $value)
   {
      $this->props[$key] = $value;
   }

   public function info($key)
   {
      return $this->props[$key];
   }


   public static function logo($path = __DEFAULT_LOGO, $root = false)
   {
      $newPath = ($root ? __ROOT : '');
      $newPath .= __LOGO_PATH . $path;
      return $newPath;
   }
   public function save()
   {
      // get self public class, no parents classes
      $propsArray[] = array_diff_key(get_object_vars($this), get_class_vars(get_parent_class($this)));

      if (count($propsArray[0]['props']) > 0) {
         if (isset($this->props[$this->primary_key])) {
            $this->updateAdmin($propsArray[0]['props']);
         } else {
            echo 'insert <hr>';
         }
      } else {
         throw new \Exception('Debe de asignar valor a las propiedades en primer lugar');
      }
   }

   public function allGrades($with12 = true)
   {
      $grades = $this->getAllGrades($this->info('year'),$with12);
      $returnData = [];
      foreach ($grades as $grade) {
         $returnData[] = $grade->grado;
      }
      return $returnData;
   }
}
