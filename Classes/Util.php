<?php

namespace Classes;

class Util
{
   public static $attendanceCodes = [
      "1" => ['type' => 'A', 'description' => 'Situación en el hogar'],
      "2" => ['type' => 'A', 'description' => 'Determinación del hogar (viaje)'],
      "3" => ['type' => 'A', 'description' => 'Actividad con padres (open house)'],
      "4" => ['type' => 'A', 'description' => 'Enfermedad'],
      "5" => ['type' => 'A', 'description' => 'Cita'],
      "6" => ['type' => 'A', 'description' => 'Actividad educativa del colegio'],
      "7" => ['type' => 'A', 'description' => 'Sin excusa del hogar'],
      "8" => ['type' => 'T', 'description' => 'Sin excusa del hogar'],
      "9" => ['type' => 'T', 'description' => 'Situación en el hogar'],
      "10" => ['type' => 'T', 'description' => 'Problema en la transportación'],
      "11" => ['type' => 'T', 'description' => 'Enfermedad'],
      "12" => ['type' => 'T', 'description' => 'Cita']
   ];
   public static function studentProfilePicture($student)
   {
      if ($student->imagen != '') {
         $picturePath = __STUDENT_PROFILE_PICTURE_URL . $student->imagen;
      } else {
         if ($student->genero === 'F' || $student->genero === '1') {
            $picturePath = __NO_PROFILE_PICTURE_STUDENT_FEMALE;
         } else {
            $picturePath = __NO_PROFILE_PICTURE_STUDENT_MALE;
         }
      }

      return $picturePath;
   }

   public static function formatDate($date, $month = false, $largeMonth = false)
   {
      if ($date !== '0000-00-00') {
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
   }

   public static function date()
   {
      $date = \date('Y-m-d');
      return $date;
   }
   public static function time($format = false)
   {
      $date = \date('H:i:s');
      if ($format) $date = self::formatTime($date);

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
      if (strpos($time, '(') > -1) {
         return $time;
      }
      $newTime = \date('g:i:s A', strtotime($time));
      return $newTime;
   }

   public static function dump($var, $exit = true)
   {
      echo '<pre>';
      \print_r($var);
      echo '<pre/>';
      if ($exit) exit();
   }
   public static function toObject($obj)
   {
      return json_decode(json_encode($obj));
   }
   public static function toJson($obj)
   {
      return json_encode($obj);
   }

   public static function phoneCompanies()
   {
      return ["AT&T", "T-Movil", "Sprint", "Open M.", "Claro", "Verizon", "Suncom", "Boost"];
   }

   public static function getIp()
   {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
         $ipaddress = getenv('HTTP_CLIENT_IP');
      else if (getenv('HTTP_X_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if (getenv('HTTP_X_FORWARDED'))
         $ipaddress = getenv('HTTP_X_FORWARDED');
      else if (getenv('HTTP_FORWARDED_FOR'))
         $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if (getenv('HTTP_FORWARDED'))
         $ipaddress = getenv('HTTP_FORWARDED');
      else if (getenv('REMOTE_ADDR'))
         $ipaddress = getenv('REMOTE_ADDR');
      else
         $ipaddress = 'UNKNOWN';
      return $ipaddress;
   }
}
