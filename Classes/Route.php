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
      return include $root . str_replace('/', DIRECTORY_SEPARATOR, $path);
   }

   public static function css($path, $serverRoot = false)
   {
      $root = '';
      if (!$serverRoot) {
         $root = __SCHOOL_URL;
      }
      echo '<link rel="stylesheet" href="' . $root . $path . '" />';
   }

   public static function js($path, $serverRoot = false)
   {
      $root = '';
      if (!$serverRoot) {
         $root = __SCHOOL_URL;
      } else {
         if (strpos($path, 'react-components'))
            Route::includeFile('/includes/react.php', true);
      }
      echo '<script type="text/javascript" src="' . $root . $path . '"></script>';
   }

   public static function file_exists($path)
   {
      if (file_exists(__ROOT_SCHOOL . $path)) {
         return true;
      } else {
         return false;
      }
   }
   public static function selectPicker($type = 'css')
   {
      echo $type === 'css' ? '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">'
         : '<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>';
   }
   public static function jqUI()
   {
      echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>';
   }

   public static function fontawasome()
   {
      echo '<script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>';
   }

   public static function error()
   {
      http_response_code(404);

      include Server::get('DOCUMENT_ROOT') . "/404.php";
      die();
   }

   public static function url($path, $fullPath = false, $serverRoot = false)
   {
      $root = '';
      if (!$serverRoot) {
         $root = __SCHOOL_URL;
      }
      $newPath = $fullPath ? Server::get('HTTP_HOST') . __SCHOOL_URL .  $path : $root . $path;
      return $newPath;
   }

   public static function redirect($path = '/login.php', $rootSchool = true)
   {
      $newPath = "";
      if ($rootSchool) {
         $newPath = __SUB_ROOT_URL;
      }
      $newPath .= $path;
      header("Location: " . __SCHOOL_URL . $newPath);
   }

   public static function back()
   {

      return Server::get('HTTP_REFERER');
   }
}
