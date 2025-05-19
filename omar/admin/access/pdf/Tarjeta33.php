<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

Session::is_logged();

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

$conducta = [];
$promedio = [];
$promedioLetters = [];
$cant = [];

$promedio['01'] = 0;
$promedio['02'] = 0;
$promedio['03'] = 0;
$promedio['04'] = 0;
$promedio['05'] = 0;
$promedio['06'] = 0;
$promedio['07'] = 0;
$promedio['08'] = 0;

function getAge($date)
{
    if ($date !== '' && $date !== '0000-00-00') {
        list($year, $month, $day) = explode("-", $date);
        $yearDifference = date("Y") - $year;
        $monthDifference = date("m") - $month;
        $dayDifference = date("d") - $day;
        if ($dayDifference < 0 && $monthDifference <= 0 || date("m") < $month) {
            $yearDifference--;
        }
        return $yearDifference;
    } else {
        return '';
    }
}
function NumberToLetter($valor)
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
    } else if ($valor <= '59') {
        return 'F';
    }
}

function Year($grado, $ss)
{
    $row = DB::table('year')
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->orderBy('apellidos')->first();
    return $row->year ?? 'XX-XX';
}

function Maestro($grado, $ss)
{
    if (Year($grado, $ss)) {
        $row = DB::table('profesor')
            ->whereRaw("grado like '$grado%'")->orderBy('apellidos')->first();

        return $row->nombre;
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

function Curso($grado, $cursos, $ss)
{

    global $promedio;
    global $promedioLetters;
    global $cant;
    foreach ($cursos as $curso) {
        $row = DB::table('acumulativa')
            ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();
        $s1 = $row->sem1 ?? '';
        $s2 = $row->sem2 ?? '';
        if ($s1 != '' || $s2 != '') {
            if ($row->sem1 != '' || $row->sem2 != '') {
                if (!is_numeric($row->sem1) || !is_numeric($row->sem2)) {
                    return $row->sem1;
                } else {
                    if ($row->sem1 != '') {
                        $promedio[$grado] += $row->sem1;
                        $promedioLetters[$grado] += Con(NumberToletter(round($row->sem1)));
                        $cant[$grado]++;
                    }
                    if ($row->sem2 != '') {
                        $promedioLetters[$grado] += Con(NumberToletter(round($row->sem2)));
                        $promedio[$grado] += $row->sem2;
                        $cant[$grado]++;
                    }
                    $t1 = 0;
                    $t2 = 0;
                    $t3 = '';
                    if ($row->sem1 > 0) {
                        $t2 = $t2 + 1;
                        $t1 = $t1 + $row->sem1;
                    }
                    if ($row->sem2 > 0) {
                        $t2 = $t2 + 1;
                        $t1 = $t1 + $row->sem2;
                    }
                    if ($t1 > 0) {
                        $t3 = round($t1 / $t2, 0);
                    }
                    return $t3;
                }
            } else {
                return '';
            }
        }
    }
}

class nPDF extends PDF
{
    function Header()
    {
        parent::header();
    }

    function Footer()
    {
        $this->SetY(-50);

        $this->Cell(80, 5, '', 'B', 1);
        $this->Cell(80, 5, '', 0, 0, 'C');
        $this->Cell(70);
        $this->Cell(50, 5, 'SELLO', 0, 1);
        $this->Cell(80, 5, 'Registradora', 0, 1, 'C');


        $this->Cell(80, 5, '', 0, 1);
        $this->Cell(80, 5, '', 0, 1, 'C');
        $this->Cell(80, 5, 'Directora', 0, 0, 'C');
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
    $pdf->AddPage();

    $info1 = DB::table('year')->select("id, ss, dir1, grado, fecha")
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();

    $info2 = DB::table('madre')->select("encargado")
        ->whereRaw("id = '$info1->id'")->first();

    $pdf->Ln(8);
    $pdf->Cell(0, 5, 'TRANSCRIPCIÓN DE CREDITOS', 0, 1, 'C');
    $pdf->Cell(0, 5, 'ESCUELA ELEMENTAL', 0, 1, 'C');
    $pdf->Ln(8);

    $pdf->Cell(20, 5, 'Nombre:');
    $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');

    $pdf->Cell(13, 5, 'Edad:');
    $pdf->Cell(10, 5, getAge($info1->fecha), 'B', 1, 'C');

    $pdf->Ln(4);
    $pdf->Cell(40, 5, "SS: XXX-XX-" . substr($estu->ss, -4), 0, 1, 'L');
    $pdf->Ln(5);

    $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->get();
    $cursos = [
        'MATEMATICAS' => ['MAT', 'ALG', 'PREAL'],
        'INGLES' => ['ING'],
        'ESPAÑOL' => ['ESP'],
        'CIENCIA' => ['CIE', 'INTF'],
        'ESTUDIOS SOCIALES' => ['SOC'],
        'EDUCACION FISICA' => ['EF', 'EDF'],
        'EDUC. CRIST.' => ['EC', 'REL'],
        'BELLAS ARTES' => ['ART'],
        'TECNOLOGIA' => ['TEC', 'COM'],
        'STEM' => ['SCM', 'COA', 'STE'],

    ];
    #1
    $GRA = array(0, '01', '02', '03', '04', '05', '06', '07', '08');
    $pdf->Cell(44, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
    for ($i = 1; $i <= 8; $i++) {
        $pdf->Cell(18, 6, Year($GRA[$i], $estu->ss), 1, ($i == 8) ? 1 : 0, 'C', true);
    }

    #2
    $pdf->Cell(44, 6, '', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(18, 6, '01', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '02', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '03', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '04', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '05', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '06', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '07', 1, 0, 'C', true);
    $pdf->Cell(18, 6, '08', 1, 1, 'C', true);
    $pdf->Cell(44, 6, 'Asignaturas', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 0, 'C', true);
    $pdf->Cell(18, 6, 'Nota', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);

    foreach ($cursos as $nombre => $curso) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(44, 6, utf8_encode($nombre), 1, 0, 'L', true);
        $pdf->SetFont('Arial', '', 11);
        for ($i = 1; $i <= 8; $i++) {
            $grade = Curso($GRA[$i], $curso, $estu->ss);
            $pdf->Cell(18, 6, $grade, 1, ($i == 8) ? 1 : 0, 'C');
        }
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(44, 6, 'PROMEDIO', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);

    for ($i = 1; $i <= 8; $i++) {
        $prom = ($promedio[$GRA[$i]] == 0) ? 0 : round(($promedio[$GRA[$i]] / $cant[$GRA[$i]]), 0);
        $pdf->Cell(18, 6, $prom == 0 ? '' : round($prom) . ' ' . NumberToLetter(round($prom)), 1, ($i == 8) ? 1 : 0, 'C');
    }

    $pdf->SetFont('Arial', '', 10);
    $gpaProm = array_sum($promedio);
    $gpaCant = array_sum($cant);
    $gpaProm2 = array_sum($promedioLetters);
    $gpa = $gpaCant > 0 ? round($gpaProm / $gpaCant) : '';
    $gpa2 = $gpaCant > 0 ? number_format($gpaProm2 / $gpaCant, 2) : '';
    $gpa3 = $gpa . '.00';
    $nota1 = DB::table('tablas')
        ->whereRaw("valor = '$gpa3'")->first();

    $pdf->Ln(5);
    $pdf->Cell(0, 5, "GPA: $gpa / $nota1->punto", 0, 1);
    $pdf->Cell(27, 5, "Comentarios:", 0, 1);
    //    $pdf->Cell(0, 5, utf8_decode($observacion1), 'B', 1);
    //    $pdf->Cell(0, 5, utf8_decode($observacion2), 'B', 1);
    $pdf->Ln(5);

    $pdf->Cell(0, 5, "Expedido en P.R. hoy " . date('Y-m-d'), 0, 1);
    $pdf->Ln(5);

    $pdf->Cell(0, 5, 'MS - Muy Satisfactorio', 0, 1);
    $pdf->Cell(0, 5, 'P - Pendiente', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, utf8_encode("La información se obtuvo del récord acumulativo de la oficina del director(a). No tiene borrones ni tachaduras."), 0, 1);
    $pdf->Ln(5);
}
$pdf->Output();
