<?php

namespace Classes;

class Directories
{
   public static $teacherProfilePicture;
   public static $studentProfilePicture;
   public static $noProfilePicture;
   public static $logo;
   
   public static function init() {
      self::$teacherProfilePicture = str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/teachers/');
      self::$studentProfilePicture = str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/pictures/students/');
      self::$noProfilePicture = str_replace('/',DIRECTORY_SEPARATOR,'/images/none.gif');
      self::$logo = str_replace('/',DIRECTORY_SEPARATOR,__ROOT_URL.'/logo/');
    }
}
