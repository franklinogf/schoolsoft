<?php

namespace Classes;

class Util
{
   public static function formatDate($date, $month = false, $largeMonth = false)
   {
      $format = '%d-%m-%Y';
      if ($month) {
         if (is_bool($month)) {
            $format = '%d %b %Y';
            if ($largeMonth) {
               $format = '%d %B %Y';
            }
         } else if (is_string($month)) {
            $format = $month;
         } else {
            throw new \Exception('Introducir "true" o un formato de fecha correcto como (%d-%m-%Y)');
         }
      }
      \setlocale(LC_ALL, 'es_ES');
      $newDate = strftime($format, strtotime($date));
      return $newDate;
   }

   public static function date()
   {
      $date = \date('Y-m-d');
      return $date;
   }
   public static function time()
   {
      $date = \date('H:i:s');
      return $date;
   }

   public static function dateTime()
   {
      $date = \date('Y-m-d H:i:s');
      return $date;
   }

   public static function daysBefore($numberOfDays = 1)
   {
      $date = date('Y-m-d', strtotime("-{$numberOfDays} days", strtotime(date('Y-m-d'))));
      return $date;
   }

   public static function daysAfter($numberOfDays = 1)
   {
      $date = date('Y-m-d', strtotime("+{$numberOfDays} days", strtotime(date('Y-m-d'))));
      return $date;
   }
  

   public static function formatTime($time)
   {

      $newTime = \date('g:i:s A', strtotime($time));
      return $newTime;
   }

   public static function dump($var)
   {
      echo '<pre>';
      \print_r($var);
      echo '<pre/>';
      exit();
   }
   public static function toObject($obj)
   {
      return json_decode(json_encode($obj));
   }  
   public static function toJson($obj)
   {
      return json_encode($obj);
   }  
}
