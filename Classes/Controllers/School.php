<?php

namespace Classes\Controllers;



use Classes\Models\SchoolModel;

class School extends SchoolModel
{

   protected $props = [];

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
}
