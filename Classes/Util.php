<?php

namespace Classes;

class Util
{
   public static $attendanceCodes = [
      "1" => ['type' => 'A', 'description' => ['es' => 'Situación en el hogar', 'en' => 'Situation at home']],
      "2" => ['type' => 'A', 'description' => ['es' => 'Determinación del hogar (viaje)', 'en' => 'Determination of the home (travel)']],
      "3" => ['type' => 'A', 'description' => ['es' => 'Actividad con padres (open house)', 'en' => 'Activity with parents (open house)']],
      "4" => ['type' => 'A', 'description' => ['es' => 'Enfermedad', 'en' => 'Disease']],
      "5" => ['type' => 'A', 'description' => ['es' => 'Cita', 'en' => 'Appointment']],
      "6" => ['type' => 'A', 'description' => ['es' => 'Actividad educativa del colegio', 'en' => 'School activity']],
      "7" => ['type' => 'A', 'description' => ['es' => 'Sin excusa del hogar', 'en' => 'No excuse from home']],
      "8" => ['type' => 'T', 'description' => ['es' => 'Sin excusa del hogar', 'en' => 'No excuse from home']],
      "9" => ['type' => 'T', 'description' => ['es' => 'Situación en el hogar', 'en' => 'Situation at home']],
      "10" => ['type' => 'T', 'description' => ['es' => 'Problema en la transportación', 'en' => 'Problem with transportation']],
      "11" => ['type' => 'T', 'description' => ['es' => 'Enfermedad', 'en' => 'Disease']],
      "12" => ['type' => 'T', 'description' => ['es' => 'Cita', 'en' => 'Appointment']],
   ];

   public static function numberToLetter($value)
   {
      if ($value == '') {
         return '';
      }
      if ($value >= 88) {
         return 'A';
      } else if ($value >= 78 && $value <= 87) {
         return 'B';
      } else if ($value >= 68 && $value <= 77) {
         return 'C';
      } else if ($value >= 60 && $value <= 67) {
         return 'D';
      } else  if ($value < 60) {
         return 'F';
      }
   }
   public static function smallNumberToLetter($value)
   {
      if ($value == '') {
         return '';
      }
      if ($value == 4) {
         return 'A';
      } else if ($value >= 3 && $value <= 3.9) {
         return 'B';
      } else if ($value >= 2 && $value <= 2.9) {
         return 'C';
      } else if ($value >= 1 && $value <= 1.9) {
         return 'D';
      } else  if ($value < 1) {
         return 'F';
      }
   }
   public static function letterToNumber($value)
   {
      if ($value == '') {
         return '';
      }
      if ($value == 'A') {
         return 4;
      } elseif ($value == 'B') {
         return 3;
      } elseif ($value == 'C') {
         return 2;
      } elseif ($value == 'D') {
         return 1;
      } elseif ($value == 'F') {
         return 0;
      }
   }
   public static function numberToSmallNumber($value)
   {
      if ($value == '') {
         return '';
      }
      if ($value >= 88) {
         return 4;
      } else if ($value >= 78 && $value <= 87) {
         return 3;
      } else if ($value >= 68 && $value <= 77) {
         return 2;
      } else if ($value >= 60 && $value <= 67) {
         return 1;
      } else  if ($value < 60) {
         return 0;
      }
   }
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
