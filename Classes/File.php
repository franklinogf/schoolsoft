<?php

namespace Classes;

use Classes\Util;

class File
{
   public $amount = 0;
   public $files = [];
   public static $faIcons = [
      "png" => '<i class="far fa-file-image"></i>',
      "jpg" => '<i class="far fa-file-image"></i>',
      "jpeg" => '<i class="far fa-file-image"></i>',
      "gif" => '<i class="far fa-file-image"></i>',
      "pdf" => '<i class="far fa-file-pdf"></i>',
      "mp3" => '<i class="far fa-file-audio"></i>',
      "mp4" => '<i class="far fa-file-video"></i>',
      "xls" => '<i class="far fa-file-excel"></i>',
      "xlsx" => '<i class="far fa-file-excel"></i>',
      "ppt" => '<i class="far fa-file-powerpoint"></i>',
      "pptx" => '<i class="far fa-file-powerpoint"></i>',
      "doc" => '<i class="far fa-file-word"></i>',
      "docx" => '<i class="far fa-file-word"></i>',
      "zip" => '<i class="far fa-file-archive"></i>'
   ];
   public static $fileIcon = '<i class="far fa-file-alt"></i>';

   public function __construct($file = 'file')
   {

      if (isset($_FILES[$file]) && !empty($file)) {
         if ($this->isMultiArray($_FILES[$file])) {
            foreach ($_FILES[$file]['name'] as $i => $name) {
               if ($_FILES[$file]['name'][$i] !== '') {
                  $this->files[$i]['name'] =  $_FILES[$file]['name'][$i];
                  $this->files[$i]['tmp_name'] =  $_FILES[$file]['tmp_name'][$i];
                  $this->files[$i]['size'] =  $_FILES[$file]['size'][$i];
                  $this->files[$i]['type'] =  $_FILES[$file]['type'][$i];
                  $this->files[$i]['error'] =  $_FILES[$file]['error'][$i];
               }
            }
            $this->files = Util::toObject($this->files);

            $this->amount = count($this->files);
         } else {
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
      $fullPath = __ROOT_SCHOOL . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);

      if (!is_dir($fullPath)) {
         mkdir($fullPath, 0777, true);
      }

      $filePath = $fullPath . $newName;
      if (move_uploaded_file($file->tmp_name, $filePath)) {
         return true;
      } else {
         return false;
      }
   }

   public static function delete($path, $fileName)
   {
      $fullPath = __ROOT_SCHOOL . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);

      $filePath = $fullPath . $fileName;

      if (file_exists($filePath)) {
         if (unlink($filePath)) {
            return true;
         }
         return false;
      }

      return false;
   }

   public static function extension($fileName)
   {
      $fileInfo = pathinfo($fileName);
      return strtolower($fileInfo['extension']);
   }

   public static function name($fileName, $baseName = false)
   {
      $fileInfo = pathinfo($fileName);
      $fileName = $fileInfo['filename'];
      if ($baseName) {
         $fileName = substr($fileName, strpos($fileName, ")") + 1);
      }
      return trim($fileName);
   }

   public static function faIcon($extension)
   {
      $faIcon = self::$fileIcon;
      if (array_key_exists($extension, self::$faIcons)) {
         $faIcon = self::$faIcons[$extension];
      }
      return $faIcon;
   }


   private function isMultiArray($array)
   {
      $rv = array_filter($array, 'is_array');
      if (count($rv) > 0) return true;
      return false;
   }
}
