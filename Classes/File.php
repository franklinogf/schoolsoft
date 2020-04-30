<?php

namespace Classes;

use Classes\Util;

class File
{
   public $amount = 0;
   public $files = [];

   public function __construct($file)
   {
      
      if (isset($_FILES[$file]) && !empty($file)) {
         if($this->isMultiArray($_FILES[$file])){
            foreach ($_FILES[$file]['name'] as $i => $name) {
               $this->files[$i]['name'] =  $_FILES[$file]['name'][$i];
               $this->files[$i]['tmp_name'] =  $_FILES[$file]['tmp_name'][$i];
               $this->files[$i]['size'] =  $_FILES[$file]['size'][$i];
               $this->files[$i]['type'] =  $_FILES[$file]['type'][$i];
               $this->files[$i]['error'] =  $_FILES[$file]['error'][$i];
            }
            $this->files = Util::toObject($this->files);
   
            $this->amount = count($this->files);
            
         }else{
            $this->files = Util::toObject($_FILES[$file]);
            $this->amount = 1;
            
         }
         return true;
      } else {
         return false;
      }
   }

   public static function upload($file, $path, $name = false)
   {      
      $newName = (!$name) ? $file->name : $name;
      $fullPath = __ROOT_SCHOOL . DIRECTORY_SEPARATOR . str_replace('/',DIRECTORY_SEPARATOR,$path);

      if (!is_dir($fullPath)) {
         mkdir($fullPath, 0777, true);
      }
      
      $filePath = $fullPath.$newName;
      if (move_uploaded_file($file->tmp_name,$filePath )) {
         return true;
      } else {
         return false;
      }
   }

   public static function delete($path, $fileName)
   {           
      $fullPath = __ROOT_SCHOOL . DIRECTORY_SEPARATOR . str_replace('/',DIRECTORY_SEPARATOR,$path);
      
      $filePath = $fullPath.$fileName;

      if (file_exists($filePath)) {
         if(unlink($filePath)){
            return true;
         }
         return false;
      }
      
      return false;
   }


   private function isMultiArray($array)
  {
    $rv = array_filter($array, 'is_array');
    if (count($rv) > 0) return true;
    return false;
  }
}
