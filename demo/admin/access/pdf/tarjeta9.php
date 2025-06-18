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
$year = $school->info('year2');

//************************************************************
// COLEGIO

class nPDF extends PDF
{

    //Cabecera de pagina
    function Header()
    {
        global $idioma;
        global $grado;
        parent::header();
        $this->Ln(-5);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);

        if ($grado == 'A') {
            $esc = ' ELEMENTAL';
        }
        if ($grado == 'B' or $grados == 'E' or $grados == 'F') {
            $esc = ' INTERMEDIA';
        }
        if ($grado == 'C' or $grados == 'D') {
            $esc = ' SECUNDARIA';
        }


        if ($idioma == 'A') {
            $this->Cell(30, 5, utf8_encode('TRANSCRIPCIÓN DE CRÉDITOS ESCUELA') . $esc, 0, 0, 'C');
        } else {
            $this->Cell(30, 5, 'TRANSCRIPTION', 0, 0, 'C');
        }
        $this->Ln(8);
        $this->SetFont('Arial', 'B', 10);
    }
    function foo($aa, $bb, $cc, $dd, $fec, $ee, $idioma, $grados, $cr, $cep, $nhc)
    {
        global $idioma;
        global $grado;
        $cep1 = ' ';
        $cep2 = ' ';
        if ($nhc == 'true') {
            $cep1 = 'X';
        } else {
            $cep2 = 'X';
        }
        $this->SetY(-50);
        //    $this->Cell(190,5,'_______________________________________________________________________________________________',0,1,'L');
        $this->SetFont('courier', 'B', 9);

        if ($idioma == 'A') {
            if ($grado == 'A') {
                $esc = ' ELEMENTAL.';
            }
            if ($grado == 'B' or $grado == 'E' or $grado == 'F') {
                $esc = ' INTERMEDIA.';
            }
            if ($grado == 'C' or $grado == 'D') {
                $esc = ' SUPERIOR.';
            }
            $d1 = 'Total Créditos Aprobados';
            if ($cep == 'true') {
                $l = 7;
                $l2 = 15;
                $d2 = 'Total Promedio Parcial Anual Acumulado';
            } else {
                $l = 27;
                $l2 = 1;
                $d2 = 'Total Promedio Anual Acumulado';
            }
            $d3 = 'Total Créditos en Progreso';
            $d4 = 'EL ALUMNO HA(' . $cep2 . ') / (' . $cep1 . ')NO HA COMPLETADO LOS REQUISITOS PARA OBTENER EL';
            $d5 = 'DIPLOMA DE LA ESCUELA';
            $d6 = 'FECHA DE GRADUACIÓN: ';
            $d7 = 'Firma del Director(a)';
            $d8 = 'Sello';
            $d9 = 'Fecha ';
        } else {
            if ($grado == 'A') {
                $esc = ' ELEMENTARY.';
            }
            if ($grado == 'B' or $grado == 'E' or $grado == 'F') {
                $esc = ' ELEMENTARY.';
            }
            if ($grado == 'C') {
                $esc = ' HIGH.';
            }
            $d1 = 'Total Approved Credits';
            if ($cep == 'true') {
                $l = 5;
                $l2 = 18;
                $d2 = 'Total Accumulated Annual Partial Average';
            } else {
                $l = 27;
                $l2 = 1;
                $d2 = 'Total Cumulative Annual Average';
            }
            $d3 = 'Total Credits in Progress';
            $d4 = 'STUDENT HA (' . $cep2 . ') / (' . $cep1 . ') HAS NOT COMPLETED THE REQUIREMENTS TO OBTAIN THE';
            $d5 = 'SCHOOL DIPLOMA';
            $d6 = 'DATE OF GRADUATION :';
            $d7 = 'Signature of the Director';
            $d8 = 'Seal';
            $d9 = 'Date ';
        }


        $this->Cell(60, 4, utf8_encode($d1), 0, 0, 'L');
        $this->Cell(15, 4, number_format($cr, 2), 0, 0, 'R');
        $this->Cell($l, 4, '', 0, 0, 'C');
        $this->Cell(60 + $l2, 4, $d2, 0, 0, 'L');
        if ($bb > 0) {
            $this->Cell(25, 4, number_format($aa / $bb, 2) . ' / ' . number_format($ee / $bb, 2), 0, 1, 'R');
        } else {
            $this->Cell(15, 4, '', 0, 1, 'R');
        }
        $this->Cell(60, 4, utf8_encode($d3), 0, 0, 'L');
        $this->Cell(15, 4, number_format($dd, 2), 0, 1, 'R');
        $this->Cell(15, 3, '', 0, 1, 'C');

        $this->Cell(70, 4, $d4, 0, 1, 'L');
        $this->Cell(70, 4, $d5 . $esc, 0, 1, 'L');
        $y2 = '';
        if (!empty($fec)) {
            $y2 = $fec;
        }
        list($d, $m, $y) = explode("-", $fec);
        if ($y > 0) {
            $y2 = $m . '/' . $d . '/' . $y;
        }


        $this->Cell(50, 4, utf8_encode($d6) . ' ' . $fec, 0, 1, 'L');
        $this->Cell(70, 3, $d9 . date('m/d/Y'), 0, 0, 'L');
        $this->Cell(50, 4, $d7, 'T', 1, 'C');
        //    $this->Cell(15,4,'',0,0,'C');
        //    $this->Cell(80,4,'',0,0,'C');
        //    $this->Cell(50,4,'Firma del Registrador(a)',0,1,'C');
        //    $this->Cell(140,4,$d9.date('m/d/Y'),0,1,'L');
        $this->Cell(50, -20, '', 0, 1, 'C');
        $this->Cell(140, 4, '', 0, 0, 'L');
        $this->Cell(40, 18, $d8, 1, 1, 'C');
    }
    function Footer()
    {
    }
}

$pdf = new nPDF();
//$pdf->SetTitle('INFORME PROMEDIO POR CLASE ACUMULADO');
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);



$pdf->SetFont('Times', '', 10);
$row = DB::table('colegio')
    ->whereRaw("usuario = 'administrador'")->first();


if ($grado == 'A') {
    $gra1 = 'KG-';
    $gra2 = '01-';
    $gra3 = '02-';
    $gra4 = '03-';
    $gra5 = '04';
    $gra6 = '05';
    $gra7 = '06';
    $gra10 = '88';
}
if ($grado == 'B') {
    $gra1 = '07';
    $gra2 = '08';
    $gra3 = '09';
    $gra4 = '88';
    $gra5 = '88';
    $gra6 = '88';
    $gra7 = '88';
}
if ($grado == 'C') {
    $gra1 = '10';
    $gra2 = '11';
    $gra3 = '12';
    $gra4 = '88';
    $gra5 = '88';
    $gra6 = '88';
    $gra7 = '88';
    $gra10 = '12';
}
if ($grado == 'D') {
    $gra1 = '09';
    $gra2 = '10';
    $gra3 = '11';
    $gra4 = '12';
    $gra5 = '88';
    $gra6 = '88';
    $gra7 = '88';
    $gra10 = '12';
}
if ($grado == 'E') {
    $gra1 = '06';
    $gra2 = '07';
    $gra3 = '08';
    $gra4 = '88';
    $gra5 = '88';
    $gra6 = '88';
    $gra7 = '88';
    $gra10 = '08';
}
if ($grado == 'F') {
    $gra1 = '07';
    $gra2 = '08';
    $gra3 = '88';
    $gra4 = '88';
    $gra5 = '88';
    $gra6 = '88';
    $gra7 = '88';
    $gra10 = '08';
}
list($ape, $nom) = explode(", ", $nombre);

$nm = 0;

if ($opcion == '2') {
    $q7 = "select * from year where grado = '$_POST[grado7]' AND year = '$_POST[ano7]' ORDER BY apellidos ASC";
    $students = DB::table('year')
        ->whereRaw("year = '$Year' and grado = '$grados' and activo = ''")->orderBy('apellidos')->get();
} else {
    $q7 = "select DISTINCT ss, ss, ss, nombre, apellidos from acumulativa where ss = '$nombre' ORDER BY orden";
    $students = DB::table('year')->select("DISTINCT ss, ss, ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $student) {
    $pdf->AddPage();

    $ape = $row7[4];
    $nom = $row7[3];
    $nm = 0;

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra1 . "%' ORDER BY orden";
    $rega1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra1 . "%'")->orderBy('orden')->first();
    $rega = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra1 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra2 . "%' ORDER BY orden";
    $regb1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra2 . "%'")->orderBy('orden')->first();
    $regb = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra2 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra3 . "%' ORDER BY orden";
    $regc1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra3 . "%'")->orderBy('orden')->first();
    $regc = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra3 . "%'")->orderBy('orden')->get();
    $num_resultados3 = count($regc);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra4 . "%' ORDER BY orden";
    $regd1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra4 . "%'")->orderBy('orden')->first();
    $regd = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra4 . "%'")->orderBy('orden')->get();
    $num_resultados4 = count($regd);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra5 . "%' ORDER BY orden";
    $rege1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra5 . "%'")->orderBy('orden')->first();
    $rege = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra5 . "%'")->orderBy('orden')->get();
    $num_resultados5 = count($rege);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra6 . "%' ORDER BY orden";
    $regf1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra6 . "%'")->orderBy('orden')->first();
    $regf = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra6 . "%'")->orderBy('orden')->get();
    $num_resultados6 = count($regf);

    $q = "select * from acumulativa where ss='$nombre' AND grado like '%" . $gra7 . "%' ORDER BY orden";
    $regg1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra7 . "%'")->orderBy('orden')->first();
    $regg = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra7 . "%'")->orderBy('orden')->get();
    $num_resultados7 = count($regg);

    if ($idioma == 'A') {
        $dnom = 'Nombre Estudiante: ';
        $dndc = 'Número de Est: ';
        $d1 = 'DESCRIPCION';
        $d2 = 'VERANO';
        $d3 = 'CREDITOS';
        $d4 = 'GRADO:  ';
        $d5 = 'AÑO ESCOLAR:  ';
        $d6 = 'PROMEDIO: ';
        $d7 = '';
        $d8 = '';
        $d9 = '';
    } else {
        $dnom = 'Student Name: ';
        $dndc = 'Student Nomber: ';
        $d1 = 'DESCRIPTION';
        $d2 = 'SUMMER';
        $d3 = 'CREDITS';
        $d4 = 'GRADE:  ';
        $d5 = 'SCHOOL YEAR:  ';
        $d6 = 'AVERAGE: ';
        $d7 = '';
        $d8 = '';
        $d9 = '';
    }

    list($ss1, $ss2, $ss3) = explode("-", $student->ss);
    $pdf->Cell(50, 2, '', 0, 1, 'L');
    $pdf->Cell(70, 5, $dnom . $student->apellidos . ' ' . $student->nombre, 0, 0, 'L');
    $pdf->Cell(68, 5, '', 0, 0, 'L');
    $pdf->Cell(30, 5, utf8_encode($dndc) . 'XXX-XX-' . $ss3, 0, 1, 'L');

    $pdf->Cell(50, 2, '', 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(240);
    $pdf->Cell(113, 5, $d1, 1, 0, 'C', true);
    $pdf->Cell(25, 5, 'NOTAS', 1, 0, 'C', true);
    $pdf->Cell(25, 5, $d2, 1, 0, 'C', true);
    $pdf->Cell(25, 5, $d3, 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $a = 0;
    $nt1 = 0;
    $nt2 = 0;
    $nt3 = 0;
    $nt4 = 0;
    $nt5 = 0;
    $nt6 = 0;
    $cr = 0;
    $cr2 = 0;
    $cr3 = 0;
    $cr4 = 0;
    $cr5 = 0;
    $cep1 = 0;
    $ae = '';

    foreach ($rega as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $rega1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $rega1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $rega1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $rega1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }

    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($regb as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $regb1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $regb1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $regb1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $regb1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }

    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($regc as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $regc1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $regc1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $regc1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $regc1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }

    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($regd as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $regd1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $regd1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $regd1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $regd1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }


    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($rege as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $rege1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $rege1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $rege1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $rege1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }


    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($regf as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $regf1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $regf1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $regf1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $regf1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }


    // *******************************************************

    $nt1 = 0;
    $nt2 = 0;
    $cr = 0;
    $cr2 = 0;
    foreach ($regg as $row1) {
        $ae = $row1->year;
        if ($idioma == 'A') {
            $pdf->Cell(113, 4, $row1->desc1, $ll, 0, 'L');
        } else {
            $pdf->Cell(113, 4, $row1->desc2, $ll, 0, 'L');
        }

        $not = $row1->sem1;
        $not2 = '';
        if ($cep == 'true' and $regg1->year == $row->year2) {
            $cep1 = $cep1 + $row1->credito;
            if ($not > 89) {
                $not2 = 'A';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 4;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 79) {
                $not2 = 'B';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 3;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 69) {
                $not2 = 'C';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 2;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 59) {
                $not2 = 'D';
                $cr2 = $cr2 + 1;
                $nt1 = $nt1 + 1;
                $nt2 = $nt2 + $row1->sem1;
            } else
         if ($not > 0) {
                $not2 = 'F';
                $cr2 = $cr2 + 1;
                $nt2 = $nt2 + $row1->sem1;
            }
        }
        $not = $row1->sem2;
        if ($not > 89) {
            $not2 = 'A';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 4;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 79) {
            $not2 = 'B';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 3;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 69) {
            $not2 = 'C';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 2;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 59) {
            $not2 = 'D';
            $cr2 = $cr2 + 1;
            $nt1 = $nt1 + 1;
            $nt2 = $nt2 + $row1->sem2;
        } else
         if ($not > 0) {
            $not2 = 'F';
            $cr2 = $cr2 + 1;
            $nt2 = $nt2 + $row1->sem2;
        }
        if ($cep == 'true' and $regg1->year == $row->year2) {
            $pdf->Cell(17, 4, $row1->sem1, $ll, 0, 'C');
        } else {
            $pdf->Cell(17, 4, $row1->sem2, $ll, 0, 'C');
        }
        $pdf->Cell(8, 4, $not2, $ll, 0, 'C');
        $pdf->Cell(25, 4, '', $ll, 0, 'C');

        if ($row1->sem2 > 59) {
            $pdf->Cell(25, 4, number_format($row1->credito, 2), $ll, 1, 'C');
            $cr = $cr + $row1->credito;
        } else {
            $pdf->Cell(25, 4, '0.00', $ll, 1, 'C');
        }
        $cr3 = $cr3 + $row1->credito;
    }
    if ($cr2 > 0) {
        $nt3 = $nt3 + $nt1;
        $nt4 = $nt4 + $nt2;
        $cr4 = $cr4 + $cr2;
        $cr5 = $cr5 + $cr;
        $pdf->Cell(43, 4, $d4 . $regg1->grado, 1, 0, 'C', true);
        $pdf->Cell(45, 4, utf8_encode($d5) . $regg1->year, 1, 0, 'C', true);
        $pdf->Cell(50, 4, $d6 . number_format($nt2 / $cr2, 2) . ' / ' . number_format($nt1 / $cr2, 2), 1, 0, 'C', true);
        $pdf->Cell(50, 4, 'TOTAL: ' . number_format($cr, 2), 1, 1, 'C', true);
    }


    $consult1 = "select * from year where ss='$nombre' and grado like '%" . $gra10 . "%'";
    $row22 = DB::table('year')
        ->whereRaw("ss='$student->ss' and grado like '%" . $gra10 . "%'")->first();
    if ($row22->fechagra == '0000-00-00') {
        $fecg = '';
    } else {
        $fecg = $row22->fechagra;
    }
    //         $nt3=$nt3+$nt1;
    //         $nt4=$nt4+$nt2;
    //         $cr4=$cr4+$cr2;

    //echo $fecg.$consult1;
    //$pdf->InFooter = true;
    $pdf->foo($nt4, $cr4, $cr3, $cep1, $fecg, $nt3, $idioma, $grados, $cr5, $cep, $nhc);
    //$pdf->InFooter = false;

    //echo $cep1.$row[116].$regd[3].$_POST['cep'];
}

$pdf->Output();
