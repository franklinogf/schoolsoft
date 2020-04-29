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

         foreach ($_FILES[$file]['name'] as $i => $thisFile) {
            $this->files[$i]['name'] =  $_FILES[$file]['name'][$i];
            $this->files[$i]['tmp'] =  $_FILES[$file]['tmp_name'][$i];
            $this->files[$i]['size'] =  $_FILES[$file]['size'][$i];
            $this->files[$i]['type'] =  $_FILES[$file]['type'][$i];
            $this->files[$i]['error'] =  $_FILES[$file]['error'][$i];
         }
         $this->files = Util::toObject($this->files);

         $this->amount = count($this->files);
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
      if (move_uploaded_file($file->tmp,$filePath )) {
         return true;
      } else {
         return false;
      }
   }
}
