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
   public function get($key)
   {
      return $this->props[$key];
   }
   
     
   
   public static function logo( $path = 'logo.gif')
   {  
     
      $path = __logo . $path;
      return $path;
   }

   public static function info($key)
   {   
      self::__construct(); 
      return self::get($key);
   }
}
