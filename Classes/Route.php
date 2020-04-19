<?php
namespace Classes;

class Route
{
   public static function includeFile($path, $serverRoot = false)
   {
      $root = __ROOT;
      if (!$serverRoot) {
         $root = __ROOT_SCHOOL;
      }      
      return include $root . str_replace('/',DIRECTORY_SEPARATOR,$path);
   }

   public static function css($path, $serverRoot = false)
   {
      $root = '';
      if (!$serverRoot) {
         $root = __ROOT_URL;
      }
      echo '<link rel="stylesheet" href="' . $root . $path . '" />';
   }

   public static function js($path, $serverRoot = false)
   {
      $root = '';
      if (!$serverRoot) {
         $root = __ROOT_URL;
      } else {
         if (strpos($path, 'react-components'))
            Route::includeFile('/includes/react.php', true);
      }
      echo '<script src="' . $root . $path . '"></script>';
   }

   public static function file_exists($path)
   {
      if (file_exists(__ROOT_SCHOOL . $path)) {
         return true;
      } else {
         return false;
      }
   }

   public static function error(){
      http_response_code(404);
      
      include $_SERVER['DOCUMENT_ROOT']."/404.php"; 
      die();
   }

   public static function url($path){
      $newPath = __ROOT_URL.$path;
      return $newPath;
   }

   public static function redirect($path){
      header("Location: ".__ROOT_URL.$path);
   }

   public static function back(){
      header("Location: ".$_SERVER["REQUEST_URI"]);
   }

}
