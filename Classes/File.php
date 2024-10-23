<?php

namespace Classes;

use Classes\Util;

class File
{
   public $amount = 0;
   public $files = [];
   public static $fileIcon = '<i class="far fa-file-alt"></i>';

   public function __construct($file = 'file')
   {
      if (isset($_FILES[$file]) && !empty($file)) {
         if ($this->isMultiArray($_FILES[$file])) {
            foreach ($_FILES[$file]['name'] as $i => $name) {
               if ($_FILES[$file]['name'][$i] !== '') {
                  $this->files[$i]['name'] = $_FILES[$file]['name'][$i];
                  $this->files[$i]['tmp_name'] = $_FILES[$file]['tmp_name'][$i];
                  $this->files[$i]['size'] = $_FILES[$file]['size'][$i];
                  $this->files[$i]['type'] = $_FILES[$file]['type'][$i];
                  $this->files[$i]['error'] = $_FILES[$file]['error'][$i];
               }
            }
            $this->files = Util::toObject($this->files);

            $this->amount = count($this->files);
         } else {
            if ($_FILES[$file]['name'] !== '') {
               $this->files = Util::toObject($_FILES[$file]);
               $this->amount = 1;
            }
         }

      }
   }

   public static function upload($file, $path, $name = null)
   {
      $uploadHost = $_SERVER['HTTP_ORIGIN'] . '/' . __SCHOOL_ACRONYM;

      $newName = $name ?: $file->name;
      $target_dir = __ROOT_SCHOOL . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
      if (!is_dir($target_dir)) {
         mkdir($target_dir, 0777, true);
      }

      $filePath = "{$target_dir}/{$newName}";
      if (move_uploaded_file($file->tmp_name, $filePath)) {
         return "{$uploadHost}/{$path}/{$name}";
      } else {
         return false;
      }
   }

   public static function delete($path, $fileName = null)
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

   public static function faIcon($extension, $size = false)
   {
      $size = $size ? "fa-{$size}" : '';
      $faIcons = [
         "png" => "<i class='far fa-file-image {$size}'></i>",
         "jpg" => "<i class='far fa-file-image {$size}'></i>",
         "jpeg" => "<i class='far fa-file-image {$size}'></i>",
         "gif" => "<i class='far fa-file-image {$size}'></i>",
         "pdf" => "<i class='far fa-file-pdf {$size}'></i>",
         "mp3" => "<i class='far fa-file-audio {$size}'></i>",
         "mp4" => "<i class='far fa-file-video {$size}'></i>",
         "xls" => "<i class='far fa-file-excel {$size}'></i>",
         "xlsx" => "<i class='far fa-file-excel {$size}'></i>",
         "ppt" => "<i class='far fa-file-powerpoint {$size}'></i>",
         "pptx" => "<i class='far fa-file-powerpoint {$size}'></i>",
         "doc" => "<i class='far fa-file-word {$size}'></i>",
         "docx" => "<i class='far fa-file-word {$size}'></i>",
         "zip" => "<i class='far fa-file-archive {$size}'></i>"
      ];

      $faIcon = self::$fileIcon;
      if (array_key_exists($extension, $faIcons)) {
         $faIcon = $faIcons[$extension];
      }
      return $faIcon;
   }


   private function isMultiArray($array)
   {
      $rv = array_filter($array, 'is_array');
      if (count($rv) > 0)
         return true;
      return false;
   }
}
