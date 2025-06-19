<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();
$conducta  = [];
$promedio  = [];
$promedio['01'] = 0;
$promedio['02'] = 0;
$promedio['03'] = 0;
$promedio['04'] = 0;
$promedio['05'] = 0;
$promedio['06'] = 0;
$promedio['07'] = 0;
$promedio['08'] = 0;
$promedioLetters  = [];
$cant = [];
$cant['01'] = 0;
$cant['02'] = 0;
$cant['03'] = 0;
$cant['04'] = 0;
$cant['05'] = 0;
$cant['06'] = 0;
$cant['07'] = 0;
$cant['08'] = 0;


$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],

]);

$school = new School(Session::id());
class nPDF extends PDF
{

    function Header()
    {
        parent::header();
    }
    function Footer() {}
}
function getAge($date)
{
    if ($date !== '' && $date !== '0000-00-00') {
        list($year, $month, $day) = explode("-", $date);
        $yearDifference  = date("Y") - $year;
        $monthDifference = date("m") - $month;
        $dayDifference   = date("d") - $day;
        if ($dayDifference < 0 && $monthDifference <= 0 || date("m") < $month) {
            $yearDifference--;
        }
        return $yearDifference;
    } else {
        return '';
    }
}
function Grado($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor <= '100' && $valor >= '90') {
        return 'A';
    } else if ($valor <= '89' && $valor >= '80') {
        return 'B';
    } else if ($valor <= '79' && $valor >= '70') {
        return 'C';
    } else if ($valor <= '69' && $valor >= '60') {
        return 'D';
    } else  if ($valor <= '59') {
        return 'F';
    }
}
//*********************************************

function Year($grado, $ss)
{
    $row = DB::table('acumulativa')->select("year")
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->orderBy('orden')->first();
    return $row->year ?? '';
}

function Attendance($grado, $ss, $type)
{
    $year = Year($grado, $ss);
    if ($year != '') {
        $res = DB::table('asispp')->select("codigo")
            ->whereRaw("ss = '$ss' and grado like '$grado%' and year = '$year'")->orderBy('codigo')->get();
        $aus = 0;
        $tar = 0;
        foreach ($res as $row) {
            $code = (int) $row->codigo;
            if ($code > 0 && $code <= 7) {
                $aus++;
            } elseif ($code > 7) {
                $tar++;
            }
        }
        if ($type == "Tardiness") {
            return $tar;
        } else {
            return $aus;
        }
    } else {
        return '';
    }
}

function Maestro($grado, $ss)
{
    if (Year($grado, $ss)) {
        $row = DB::table('profesor')->select("nombre")
            ->whereRaw("grado like '$grado%'")->orderBy('nombre')->get();

        return $row->nombre;
    } else {
        return '';
    }
}
function NumberToLetter($valor)
{
    if ($valor != '') {
        if ($valor >= 90) {
            return 'A';
        } elseif ($valor >= 80) {
            return 'B';
        } elseif ($valor >= 70) {
            return 'C';
        } elseif ($valor >= 60) {
            return 'D';
        } else {
            return 'F';
        }
    } else {

        return '';
    }
}
function Con($valor)
{
    if ($valor == 'A') {
        return 4;
    } elseif ($valor == 'B') {
        return 3;
    } elseif ($valor == 'C') {
        return 2;
    } elseif ($valor == 'D') {
        return 1;
    } elseif ($valor == 'F') {
        return 0;
    } elseif ($valor == '') {
        return '';
    }
}
function Conducta($valor)
{
    if ($valor >= 3.5 && $valor <= 4) {
        return 'A';
    } elseif ($valor >= 2.5 && $valor <= 3.49) {
        return 'B';
    } elseif ($valor >= 1.5 && $valor <= 2.49) {
        return 'C';
    } elseif ($valor >= 0.8 && $valor <= 1.49) {
        return 'D';
    } elseif ($valor >= 0 && $valor <= 0.79) {
        return 'F';
    }
}

function  Curso($grado, $cursos, $ss)
{
    global $conducta;
    global $promedio;
    global $promedioLetters;
    global $cant;
    foreach ($cursos as $curso) {

        $row = DB::table('acumulativa')
            ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->orderBy('orden')->first();
        //   $t = count($row) ?? 0;
        if ($row->sem1 ?? 0 > 0 or $row->sem2 ?? 0 > 0) {
            $sem1 =  number_format($row->sem1, 0);
            $sem2 =  number_format($row->sem2, 0);
            $sem1 =  $row->sem1;
            $sem2 =  $row->sem2;
            $c = 0;
            if ($sem1 != '') {
                $c++;
            }
            if ($sem2 != '') {
                $c++;
            }
            if ($c == 0) {
                $c = 1;
            }
            $valor = ($sem1 + $sem2) / $c;

            if ($sem1 != '' || $sem2 != '') {
                $c = 0;
                $c2 = 0;
                if (Con($row->con2) != '') {
                    $c++;
                    $c2 = $c2 + is_numeric(Con($row->con2));
                }
                if (Con($row->con4) != '') {
                    $c++;
                    $c2 = $c2 + is_numeric(Con($row->con4));
                }
                if ((!is_numeric($sem1) || empty($sem1)) && (!is_numeric($sem2) || empty($sem2))) {
                    return round($valor);
                } else {
                    $promedio[$grado] += round($valor);
                    $cant[$grado]++;

                    return round($valor);
                }
            } else {
                return NULL;
            }
        }
    }
}


$pdf = new nPDF();
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 11);
if ($opcion == '2') {
    $students = DB::table('year')
        ->whereRaw("year = '$Year' and grado = '$grados' and activo = ''")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}
foreach ($students as $estu) {
    $promedio['01'] = 0;
    $promedio['02'] = 0;
    $promedio['03'] = 0;
    $promedio['04'] = 0;
    $promedio['05'] = 0;
    $promedio['06'] = 0;
    $promedio['07'] = 0;
    $promedio['08'] = 0;
    $cant['01'] = 0;
    $cant['02'] = 0;
    $cant['03'] = 0;
    $cant['04'] = 0;
    $cant['05'] = 0;
    $cant['06'] = 0;
    $cant['07'] = 0;
    $cant['08'] = 0;
    $pdf->AddPage();
    $info1 = DB::table('year')->select("DISTINCT id, ss, grado, fecha")
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();

    if (!empty($info1->id)) {
        //         $info2 = DB::table('madre')->select("encargado")
        //         ->whereRaw("id = $info1->id")->orderBy('id')->first();
    }

    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'Elementary School Credit Transcript', 0, 1, 'C');
    $pdf->Line($pdf->GetX(), $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(10);
    $pdf->Cell(15, 5, 'NAME: ');
    $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');
    $pdf->Cell(0, 5, "DATE: " . date('M/d/Y'), 0, 1, 'R');
    $pdf->Ln(5);

    $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->orderBy('apellidos')->first();

    $cursos = [
        'Spanish' => ['SPA'],
        'Social Studies' => ['SOC'],
        'Science' => ['CIEN'],
        'Physical Education' => ['EDFI'],
        'Math' => ['MATH'],
        'Sign language' => ['LSEN'],
        'English' => ['ENG'],
        'Music' => ['MUS'],
        'Health' => ['SAL'],
    ];
    #1
    $GRADOS = ['01', '02', '03', '04', '05', '06', '07', '08'];
    $WIDTH = 19;


    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'COURSE', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($WIDTH, 6, '1st', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '2nd', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '3rd', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '4th', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '5th', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '6th', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '7th', 1, 0, 'C', true);
    $pdf->Cell($WIDTH, 6, '8th', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($cursos as $nombre => $curso) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 6, utf8_decode($nombre), 1, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        foreach ($GRADOS as $i => $GRA) {
            $grade = Curso($GRA, $curso, $estu->ss);
            $pdf->Cell($WIDTH / 2, 6, $grade, 'LTB', 0, 'C');
            $pdf->Cell($WIDTH / 2, 6, NumberToLetter($grade), 'RTB', ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
        }
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Final Average', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);

    foreach ($GRADOS as $i => $GRA) {
        $prom = ($promedio[$GRA] == 0) ? 0 : round(($promedio[$GRA] / $cant[$GRA]));
        //    $prom = $promedio[$GRA];
        $pdf->Cell($WIDTH / 2, 6, $prom == 0 ? '' : $prom, 'LTB', 0, 'C', true);
        $pdf->Cell($WIDTH / 2, 6, ($cant[$GRA] == 0) ? '' : NumberToLetter($prom), 'RTB', ($i == count($GRADOS) - 1) ? 1 : 0, 'C', true);
    }
    $pdf->Cell(40, 6, '', 1);
    foreach ($GRADOS as $i => $GRA) {
        $pdf->Cell($WIDTH, 6,  '', 1, ($i == count($GRADOS) - 1), 'C');
    }
    //  $pdf->SetFillColor(220);
    $pdf->Cell(40, 6, 'Absences', 1, 0, 'L', true);
    foreach ($GRADOS as $i => $GRA) {
        $attendance = Attendance($GRA, $estu->ss, 'Absences');
        $pdf->Cell($WIDTH, 6,  $attendance, 1, ($i == count($GRADOS) - 1), 'C', true);
    }
    $pdf->Cell(40, 6, 'Tardiness', 1, 0, 'L', true);
    foreach ($GRADOS as $i => $GRA) {
        $attendance = Attendance($GRA, $estu->ss, 'Tardiness');
        $pdf->Cell($WIDTH, 6,  $attendance, 1, ($i == count($GRADOS) - 1), 'C', true);
    }
    $pdf->Cell(40, 6, '', 1);
    foreach ($GRADOS as $i => $GRA) {
        $pdf->Cell($WIDTH, 6,  '', 1, ($i == count($GRADOS) - 1), 'C');
    }

    $pdf->Cell(40, 6, 'School Period', 1, 0, 'L');
    foreach ($GRADOS as $i => $GRA) {
        $pdf->Cell($WIDTH, 6, Year($GRA, $estu->ss), 1, ($i == count($GRADOS) - 1) ? 1 : 0, 'C');
    }

    $pdf->Ln(10);
    $pdf->Cell(7, 5, 'Sign:', 0, 0, 'C');
    $pdf->Cell(60, 4, '', 'B', 1);
    $pdf->Ln(50);
    $pdf->Cell(0, 5, 'A-Trabajo Excelente; B-Bueno; C-Promedio; D-Deficiente; F-Fracaso');
}
$pdf->Output();
