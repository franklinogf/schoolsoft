<?php

namespace Classes;


class File
{
   public $file;

   public function __construct($file)
   {

      if (isset($_FILES[$file])) {
         $this->file = $_FILES[$file];
         return true;
      } else {
         return false;
      }
   }

   public  function upload($name, $path)
   {

      $fullPath = __ROOT . DIRECTORY_SEPARATOR . $path;
      if (!is_dir($fullPath)) {
         mkdir($fullPath, 0777, true);
      }
     if( move_uploaded_file($this->tmp(), $fullPath . $name)){
        return true;
     }else{
        return false;
     }
   }

   public function name()
   {
      return $this->file['name'];
   }

   public function tmp()
   {
      return $this->file['tmp_name'];
   }

   public function size()
   {
      return $this->file['size'];
   }
   public function type()
   {
      return $this->file['type'];
   }
   public function error()
   {
      return $this->file['error'];
   }
}
