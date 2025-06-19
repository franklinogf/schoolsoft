<?php

namespace Classes;

class Util
{
    public static $attendanceCodes = [
        "1" => ['type' => 'A', 'description' => ['es' => 'Situacion en el hogar', 'en' => 'Situation at home']],
        "2" => ['type' => 'A', 'description' => ['es' => 'Determinacion del hogar (viaje)', 'en' => 'Determination of the home (travel)']],
        "3" => ['type' => 'A', 'description' => ['es' => 'Actividad con padres (open house)', 'en' => 'Activity with parents (open house)']],
        "4" => ['type' => 'A', 'description' => ['es' => 'Enfermedad', 'en' => 'Sickness']],
        "5" => ['type' => 'A', 'description' => ['es' => 'Cita', 'en' => 'Appointment']],
        "6" => ['type' => 'A', 'description' => ['es' => 'Actividad educativa del colegio', 'en' => 'School activity']],
        "7" => ['type' => 'A', 'description' => ['es' => 'Sin excusa del hogar', 'en' => 'No excuse from home']],
        "8" => ['type' => 'T', 'description' => ['es' => 'Sin excusa del hogar', 'en' => 'No excuse from home']],
        "9" => ['type' => 'T', 'description' => ['es' => 'Situacion en el hogar', 'en' => 'Situation at home']],
        "10" => ['type' => 'T', 'description' => ['es' => 'Problema en la transportacion', 'en' => 'Problem with transportation']],
        "11" => ['type' => 'T', 'description' => ['es' => 'Enfermedad', 'en' => 'Sickness']],
        "12" => ['type' => 'T', 'description' => ['es' => 'Cita', 'en' => 'Appointment']],
    ];


    public static function getAge($date)
    {
        list($year, $month, $day) = explode("-", $date);
        $yearDifference  = date("Y") - $year;
        $monthDifference = date("m") - $month;
        $dayDifference   = date("d") - $day;
        if ($dayDifference < 0 && $monthDifference <= 0 || date("m") < $month) {
            $yearDifference--;
        }
        return $yearDifference;
    }
    public static function ssLast4Digits($ss)
    {
        return substr($ss, -4);
    }
    public static function gender($gender, bool $fullGender = false): string
    {
        $thisGender = '';
        if ($gender === '2' || $gender === 'M') {
            $thisGender = $fullGender ? (__LANG === 'es' ? 'Masculino' : 'Male')  : 'M';
        } else if ($gender === '1' || $gender === 'F') {
            $thisGender = $fullGender ? (__LANG === 'es' ? 'Femenino' : 'Female')  : 'F';
        }
        return $thisGender;
    }


    public static function getNextYear($year)
    {
        list($year1, $year2) = explode('-', $year);
        $year1++;
        $year2++;
        return "$year1-$year2";
    }

    public static function getNextGrade($oldGrade, $alone = false)
    {
        list($g1, $g2) = explode('-', $oldGrade);

        if (preg_match('/[A-Za-z]/', $g1)) {
            if ($g1 === 'IN') {
                $grade =  "MA-$g2";
            } elseif ($g1 === 'MA' || $g1 === 'PP') {
                $grade =  "PK-$g2";
            } elseif ($g1 === 'PK') {
                $grade =  "KG-$g2";
            } elseif ($g1 === 'KG') {
                $grade = "01-$g2";
            }
        } else {
            $grade = (int) $g1;
            $grade++;
            if ($grade < 10) {
                $grade = '0' . $grade . '-' . $g2;
            } else {
                $grade = $grade . '-' . $g2;
            }
        }
        if ($alone) $grade = substr($grade, 0, 2);
        return $grade;
    }

    public static function numberToLetter($value)
    {
        if ($value == '') {
            return '';
        }
        if ($value >= 88) {
            return 'A';
        } elseif ($value >= 78 && $value <= 87) {
            return 'B';
        } elseif ($value >= 68 && $value <= 77) {
            return 'C';
        } elseif ($value >= 60 && $value <= 67) {
            return 'D';
        } elseif ($value < 60) {
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
        } elseif ($value >= 3 && $value <= 3.9) {
            return 'B';
        } elseif ($value >= 2 && $value <= 2.9) {
            return 'C';
        } elseif ($value >= 1 && $value <= 1.9) {
            return 'D';
        } elseif ($value < 1) {
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
        } elseif ($value >= 78 && $value <= 87) {
            return 3;
        } elseif ($value >= 68 && $value <= 77) {
            return 2;
        } elseif ($value >= 60 && $value <= 67) {
            return 1;
        } elseif ($value < 60) {
            return 0;
        }
    }
    public static function studentProfilePicture($student)
    {
        $picturePath = $student->imagen != ''
            ? __STUDENT_PROFILE_PICTURE_URL . $student->imagen
            : ($student->genero === 'F' || $student->genero === '1'
                ? __NO_PROFILE_PICTURE_STUDENT_FEMALE
                : __NO_PROFILE_PICTURE_STUDENT_MALE);

        return $picturePath;
    }

    public static function formatDate($date, $month = false, $largeMonth = false)
    {
        if ($date !== '0000-00-00') {
            $format = 'd-m-Y';
            if ($month) {
                if (is_bool($month)) {
                    $format = 'd M Y'; // format 
                    if ($largeMonth) {
                        $format = 'd F Y';
                    }
                } elseif (is_string($month)) {
                    $format = $month;
                } else {
                    throw new \Exception('Introducir "true" o un formato de fecha correcto como (d-m-Y)');
                }
            }
            \setlocale(LC_ALL, __LANG === 'es' ? 'es_ES' : 'en_EN');
            $newDate = date($format, strtotime($date));
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
        if ($format) {
            $date = self::formatTime($date);
        }

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
        if ($exit) {
            exit();
        }
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
    public static function phoneAddress($phone, $company)
    {
        $phoneAddress = preg_replace('/[^\d]/', '', $phone);
        if ($company == "AT&T") {
            $phoneAddress .= "@txt.att.net";
        }
        if ($company == "T-Movil") {
            $phoneAddress .= "@tmomail.net";
        }
        if ($company == "Sprint") {
            $phoneAddress .= "@messaging.sprintpcs.com";
        }
        if ($company == "Open M.") {
            $phoneAddress .= "@email.openmobilepr.com";
        }
        if ($company == "Claro") {
            $phoneAddress .= "@mms.claropr.com";
        }
        if ($company == "Verizon") {
            $phoneAddress .= "@vtext.com";
        }
        if ($company == "Suncom") {
            $phoneAddress .= "@tms.suncom.com";
        }
        if ($company == "Boost") {
            $phoneAddress .= "@myboostmobile.com";
        }
        return $phoneAddress;
    }

    public static function getIp()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}
