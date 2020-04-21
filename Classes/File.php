<?php

namespace Classes;


class File
{
   public $file;

   public function __construct($file)
   {

      if (isset($_FILES[$file])) {
         $this->file = $_FILES[$file];
      } else {
         return false;
      }
   }

   public  function upload($name,$path){
     if(!is_dir($path)){
         mkdir($path,0777,true);
     }
     echo $this->tmp(),'<hr>',$path.$name;
     if(move_uploaded_file($this->tmp(),$path.$name)){
        echo 'se subio';
     }else{
        echo 'no se subio';
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
