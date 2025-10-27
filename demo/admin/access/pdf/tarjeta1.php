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
$usua = $school->info('usuario');

//************************************************************
class nPDF extends PDF
{
    function Header()
    {
        parent::header();
        global $idioma;
        //	$this->Ln(2);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        if ($idioma == 'A') {
            $this->Cell(30, 10, 'TRANSCRIPCION DE CREDITOS', 0, 0, 'C');
        } else {
            $this->Cell(30, 10, 'CUMULATIVE CARD', 0, 0, 'C');
        }
        $this->Ln(5);
        $this->Cell(60);

        $this->SetFont('Arial', 'B', 10);
        $this->Ln(10);
    }

    function Not($se1, $se2, $cr, $cur)
    {
        global $anc;
        global $ll;
        global $let1;
        global $cnt1;
        global $let3;
        global $cnt3;
        global $num1;
        global $num3;
        global $tnot;
        global $tc;
        $a = 0;
        $s1 = '';
        $s2 = '';
        if ($se1 > 0 or $se2 > 0) {
            $tc = $tc + $cr;
        }
        if ($se1 > 89) {
            $s1 = 'A';
            $let1 = $let1 + (4 * $cr);
            $cnt1 = $cnt1 + $cr;
            $num1 = $num1 + ($se1 * $cr);
        } elseif ($se1 > 79) {
            $s1 = 'B';
            $let1 = $let1 + (3 * $cr);
            $cnt1 = $cnt1 + $cr;
            $num1 = $num1 + ($se1 * $cr);
        } elseif ($se1 > 69) {
            $s1 = 'C';
            $let1 = $let1 + (2 * $cr);
            $cnt1 = $cnt1 + $cr;
            $num1 = $num1 + ($se1 * $cr);
        } elseif ($se1 > 59) {
            $s1 = 'D';
            $let1 = $let1 + (1 * $cr);
            $cnt1 = $cnt1 + $cr;
            $num1 = $num1 + ($se1 * $cr);
        } elseif ($se1 > 0) {
            $s1 = 'F';
            $cnt1 = $cnt1 + $cr;
            $num1 = $num1 + ($se1 * $cr);
        } elseif ($se1 == '') {
            $s1 = '';
        }

        if ($se2 > 89) {
            $s2 = 'A';
            $let3 = $let3 + (4 * $cr);
            $cnt3 = $cnt3 + $cr;
            $num3 = $num3 + ($se2 * $cr);
        } elseif ($se2 > 79) {
            $s2 = 'B';
            $let3 = $let3 + (3 * $cr);
            $cnt3 = $cnt3 + $cr;
            $num3 = $num3 + ($se2 * $cr);
        } elseif ($se2 > 69) {
            $s2 = 'C';
            $let3 = $let3 + (2 * $cr);
            $cnt3 = $cnt3 + $cr;
            $num3 = $num3 + ($se2 * $cr);
        } elseif ($se2 > 59) {
            $s2 = 'D';
            $let3 = $let3 + (1 * $cr);
            $cnt3 = $cnt3 + $cr;
            $num3 = $num3 + ($se2 * $cr);
        } elseif ($se2 > 0) {
            $s2 = 'F';
            $cnt3 = $cnt3 + $cr;
            $num3 = $num3 + ($se2 * $cr);
        } elseif ($se2 == '') {
            $s2 = '';
        }
        if ($tnot == 'B') {
            $s1 = $se1;
            $s2 = $se2;
        }
        if (substr($cur, 0, 4) == 'SERC' or substr($cur, 0, 4) == 'SERV') {
            $s1 = $se1;
            $s2 = $se2;
        }
        if ($tnot == 'C') {
            $this->Cell(9, $anc, $se1, $ll, 0, 'R');
            $this->Cell(5, $anc, $s1, $ll, 0, 'C');
            $this->Cell(9, $anc, $se2, $ll, 0, 'R');
            $this->Cell(5, $anc, $s2, $ll, 0, 'C');
        } else {
            $this->Cell(14, $anc, $s1, $ll, 0, 'C');
            $this->Cell(14, $anc, $s2, $ll, 0, 'C');
        }
        $this->Cell(14, $anc, number_format($cr, 2), $ll, 1, 'R');
    }
    function Footer() {}
}

$pdf = new nPDF();
$pdf->SetTitle('CUMULATIVE CARD');
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);

$row = DB::table('colegio')
    ->whereRaw("usuario = 'administrador'")->first();

$fecg = '';
if ($grado == 'A') {
    $gra1 = '01-';
    $gra2 = '02-';
    $gra3 = '03-';
    $gra4 = '04';
}
if ($grado == 'B') {
    $gra1 = '05';
    $gra2 = '06';
    $gra3 = '07';
    $gra4 = '08';
}
if ($grado == 'C') {
    $gra1 = '09';
    $gra2 = '10';
    $gra3 = '11';
    $gra4 = '12';
}
$prom = 0;
$ye = 0;
$nm = 0;
$ll = 1;
$anc = 5;
$num1 = 0;
$num3 = 0;
$tc = 0;


if ($opcion == '2') {
    $students = DB::table('year')
        ->whereRaw("year = '$Year' and grado = '$grados' and activo = ''")->orderBy('apellidos')->get();
} else {
    $students = DB::table('year')->select("DISTINCT ss, ss, ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $student) {
    $pdf->AddPage();
    //        $ape=$row7[4];
    //        $nom=$row7[3];
    $nm = 0;
    $nm7 = 0;

    $rega1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra1 . "%'")->orderBy('orden')->first();
    $rega = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra1 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra2 . "%'")->orderBy('orden')->first();
    $regb = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra2 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

    if ($num_resultados1 > $num_resultados2) {
        $nm = $num_resultados1;
    } else {
        $nm = $num_resultados2;
    }

    $nm2 = $nm;
    $pdf->SetFont('Arial', '', 11);

    if ($idioma == 'A') {
        $nom1 = 'NOMBRE';
        $fec = 'FECHA';
        $gra = 'GRADO';
        $ano = 'A&#65533;O';
        $des = 'DESCRIPCION';
        $pro = 'PROMEDIO GEN.';
        $proa = 'PROMEDIO ACU.';
        $pr = 'PROMEDIO';
    } else {
        $nom1 = 'NAME';
        $fec = 'DATE';
        $gra = 'GRADE';
        $ano = 'YEAR';
        $des = 'DESCRIPTION';
        $pro = 'GENERAL AVE.';
        $proa = 'CUMULATIVE AVE.';
        $pr = 'AVERAGE';
    }


    if ($num_resultados1 + $num_resultados2 > 0) {
        $nm7 = 1;

        if ($num_resultados1 > $num_resultados2) {
            $pdf->Cell(30, 5, $nom1, 1, 0, 'C', true);
            $pdf->Cell(100, 5, $rega1->apellidos . ' ' . $rega1->nombre, 1, 0, 'L', true);
            $pdf->Cell(30, 5, $fec, 1, 0, 'C', true);
            $pdf->Cell(30, 5, DATE('m-d-Y'), 1, 1, 'C', true);
        } else {
            $pdf->Cell(30, 5, $nom1, 1, 0, 'C', true);
            $pdf->Cell(100, 5, $regb1->apellidos . ' ' . $regb1->nombre, 1, 0, 'L', true);
            $pdf->Cell(30, 5, $fec, 1, 0, 'C', true);
            $pdf->Cell(30, 5, DATE('m-d-Y'), 1, 1, 'C', true);
        }
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->Cell(24, 5, $rega1->grado ?? '', 1, 0, 'C', true);
        $pdf->Cell(22, 5, utf8_encode($ano), 1, 0, 'C', true);
        $pdf->Cell(25, 5, $rega1->year ?? '', 1, 0, 'C', true);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->Cell(24, 5, $regb1->grado ?? '', 1, 0, 'C', true);
        $pdf->Cell(22, 5, utf8_encode($ano), 1, 0, 'C', true);
        $pdf->Cell(25, 5, $regb1->year ?? '', 1, 1, 'C', true);

        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 0, 'C', true);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 10);

        $cre1 = 0;
        $cre2 = 0;
        $cre3 = 0;
        $cre4 = 0;
        $cre5 = 0;
        $cre6 = 0;
        $not1 = 0;
        $not2 = 0;
        $not3 = 0;
        $not4 = 0;
        $let1 = 0;
        $let2 = 0;
        $let3 = 0;
        $let4 = 0;
        $let5 = 0;
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;
        $cnt4 = 0;
        $cnt5 = 0;

        if ($num_resultados1 > $num_resultados2) {
            $result3 = $result ?? 0;
            $result4 = $result2 ?? 0;
        } else {
            $result3 = $result2 ?? 0;
            $result4 = $result ?? 0;
        }

        $pfa1 = '';
        $pfa2 = '';
        $pfb1 = '';
        $pfb2 = '';
        $t1 = 12;
        $t2 = 0;
        $y = $pdf->GetY();
        $let1 = 0;
        $let3 = 0;
        $cnt1 = 0;
        $cnt3 = 0;
        $num1 = 0;
        $num3 = 0;
        $tc = 0;

        foreach ($rega as $row1) {
            $t2 = $t2 + 1;
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $row1->desc1, $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $row1->desc2, $ll, 0, 'L');
            }
            if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
                $pdf->Not($row1->sem1, $row1->sem2, $row1->credito, $row1->curso);
            }
        }
        for ($t2 = $t2; $t2 <= $t1; $t2++) {
            $pdf->Cell(53, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 1, 'L');
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
            $cnt9 = 0;
            $let9 = 0;
            if ($tnot == 'B' or $tnot == 'C') {
                $let1 = $num1;
                $let3 = $num3;
            }
            if ($cnt1 > 0) {
                $let9 = $let9 + number_format($let1 / $cnt1, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt3 > 0) {
                $let9 = $let9 + number_format($let3 / $cnt3, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt9 > 0) {
                $prom = $prom + round($let9 / $cnt9, 2);
                $ye = $ye + 1;
                $pdf->Cell(28, $anc, number_format($let9 / $cnt9, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, 'XXX', $ll, 0, 'C', true);
            }
            $pdf->Cell(14, $anc, number_format($tc, 2), $ll, 1, 'R', true);
        }


        $pdf->SetLeftMargin(105);
        $pdf->SetY($y);
        $let1 = 0;
        $let3 = 0;
        $cnt1 = 0;
        $cnt3 = 0;
        $num1 = 0;
        $num3 = 0;
        $t2 = 0;
        $tc = 0;
        foreach ($regb as $row1) {
            $t2 = $t2 + 1;
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $row1->desc1, $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $row1->desc2, $ll, 0, 'L');
            }
            if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
                $pdf->Not($row1->sem1, $row1->sem2, $row1->credito, $row1->curso);
            }
        }
        for ($t2 = $t2; $t2 <= $t1; $t2++) {
            $pdf->Cell(53, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 1, 'L');
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
            $cnt9 = 0;
            $let9 = 0;
            if ($tnot == 'B' or $tnot == 'C') {
                $let1 = $num1;
                $let3 = $num3;
            }
            if ($cnt1 > 0) {
                $let9 = $let9 + number_format($let1 / $cnt1, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt3 > 0) {
                $let9 = $let9 + number_format($let3 / $cnt3, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt9 > 0) {
                $prom = $prom + round($let9 / $cnt9, 2);
                $ye = $ye + 1;
                $pdf->Cell(28, $anc, number_format($let9 / $cnt9, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, 'XXX', $ll, 0, 'C', true);
            }
            $pdf->SetLeftMargin(10);
            $pdf->Cell(14, $anc, number_format($tc, 2), $ll, 1, 'R', true);
        }
    }
    //        $pdf->SetLeftMargin(10);
    //****************************************************


    $rega1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra3 . "%'")->orderBy('orden')->first();
    $rega = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra3 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb1 = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra4 . "%'")->orderBy('orden')->first();
    $regb = DB::table('acumulativa')
        ->whereRaw("curso NOT LIKE '%D-%' and ss='$student->ss' AND grado like '%" . $gra4 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

    if ($num_resultados1 > $num_resultados2) {
        $nm = $num_resultados1;
    } else {
        $nm = $num_resultados2;
    }

    if ($num_resultados1 + $num_resultados2 > 0) {
        if ($nm7 == 0) {
            if ($num_resultados1 > $num_resultados2) {
                $pdf->Cell(30, 5, $nom1, 1, 0, 'C', true);
                $pdf->Cell(100, 5, $rega1->apellidos . ' ' . $rega1->nombre, 1, 0, 'L', true);
                $pdf->Cell(30, 5, $fec, 1, 0, 'C', true);
                $pdf->Cell(30, 5, DATE('m-d-Y'), 1, 1, 'C', true);
            } else {
                $pdf->Cell(30, 5, $nom1, 1, 0, 'C', true);
                $pdf->Cell(100, 5, $regb1->apellidos . ' ' . $regb1->nombre, 1, 0, 'L', true);
                $pdf->Cell(30, 5, $fec, 1, 0, 'C', true);
                $pdf->Cell(30, 5, DATE('m-d-Y'), 1, 1, 'C', true);
            }
        }


        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->Cell(24, 5, $rega1->grado, 1, 0, 'C', true);
        $pdf->Cell(22, 5, utf8_encode($ano), 1, 0, 'C', true);
        $pdf->Cell(25, 5, $rega1->year, 1, 0, 'C', true);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->Cell(24, 5, $regb1->grado, 1, 0, 'C', true);
        $pdf->Cell(22, 5, utf8_encode($ano), 1, 0, 'C', true);
        $pdf->Cell(25, 5, $regb1->year, 1, 1, 'C', true);

        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 0, 'C', true);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 10);

        $t1 = 12;
        $t2 = 0;
        $y = $pdf->GetY();
        $let1 = 0;
        $let3 = 0;
        $cnt1 = 0;
        $cnt3 = 0;
        $num1 = 0;
        $num3 = 0;
        $tc = 0;

        foreach ($rega as $row1) {
            $t2 = $t2 + 1;
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $row1->desc1, $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $row1->desc2, $ll, 0, 'L');
            }
            if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
                $pdf->Not($row1->sem1, $row1->sem2, $row1->credito, $row1->curso);
            }
        }
        for ($t2 = $t2; $t2 <= $t1; $t2++) {
            $pdf->Cell(53, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 1, 'L');
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
            $cnt9 = 0;
            $let9 = 0;
            if ($tnot == 'B' or $tnot == 'C') {
                $let1 = $num1;
                $let3 = $num3;
            }
            if ($cnt1 > 0) {
                $let9 = $let9 + number_format($let1 / $cnt1, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt3 > 0) {
                $let9 = $let9 + number_format($let3 / $cnt3, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt9 > 0) {
                $prom = $prom + round($let9 / $cnt9, 2);
                $ye = $ye + 1;
                $pdf->Cell(28, $anc, number_format($let9 / $cnt9, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, 'XXX', $ll, 0, 'C', true);
            }
            $pdf->Cell(14, $anc, number_format($tc, 2), $ll, 1, 'R', true);
        }

        $pdf->SetLeftMargin(105);
        $pdf->SetY($y);
        $let1 = 0;
        $let3 = 0;
        $cnt1 = 0;
        $cnt3 = 0;
        $num1 = 0;
        $num3 = 0;
        $t2 = 0;
        $tc = 0;
        foreach ($regb as $row1) {
            $t2 = $t2 + 1;
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $row1->desc1, $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $row1->desc2, $ll, 0, 'L');
            }
            if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
                $pdf->Not($row1->sem1, $row1->sem2, $row1->credito, $row1->curso);
            }
        }
        for ($t2 = $t2; $t2 <= $t1; $t2++) {
            $pdf->Cell(53, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 0, 'L');
            $pdf->Cell(14, $anc, '', $ll, 1, 'L');
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($tnot == 'A' or $tnot == 'B' or $tnot == 'C') {
            $cnt9 = 0;
            $let9 = 0;
            if ($tnot == 'B' or $tnot == 'C') {
                $let1 = $num1;
                $let3 = $num3;
            }
            if ($cnt1 > 0) {
                $let9 = $let9 + number_format($let1 / $cnt1, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt3 > 0) {
                $let9 = $let9 + number_format($let3 / $cnt3, 2);
                $cnt9 = $cnt9 + 1;
            }
            if ($cnt9 > 0) {
                $prom = $prom + round($let9 / $cnt9, 2);
                $ye = $ye + 1;
                $pdf->Cell(28, $anc, number_format($let9 / $cnt9, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, 'XXX', $ll, 0, 'C', true);
            }
            $pdf->SetLeftMargin(10);
            $pdf->Cell(14, $anc, number_format($tc, 2), $ll, 1, 'R', true);
        }
    }

    //************************************************************************************************************************

    $inicio = '';
    $fecg = $student->fechagra ?? '';
    if ($idioma == 'A') {
        setlocale(LC_TIME, 'spanish');
        if ($fecg != '0000-00-00') {
            $fechaComite = date($fecg);
            $inicio = strftime("%B %d, %Y", strtotime($fechaComite));
        }
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(60, $anc, 'PROMEDIO ACUMULATIVO', 1, 0, 'L', true);
        if ($ye > 0) {
            $pdf->Cell(35, $anc, number_format($prom / $ye, 2), 1, 1, 'R');
        } else {
            $pdf->Cell(35, $anc, number_format($prom, 2), 1, 1, 'R');
        }
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(60, $anc, 'FECHA DE GRADUACION', 1, 0, 'L', true);
        $pdf->Cell(35, $anc, $inicio, 1, 1, 'R');

        $Y = $pdf->GetY();

        $pdf->Cell(40, 10, '', 0, 1);
        $pdf->Cell(70, 5, 'Firma Autorizada', 'T', 0, 'C');
        if ($cofi=='true' and $usua=='administrador')
           {
           $pdf->Image('../../../logo/firma.gif', 18, $Y - 3, 45);
           }
        
        $pdf->Cell(70, 5, '', 0, 0);
        $pdf->Cell(40, 5, 'Sello Oficial', 0, 1);
        $pdf->Cell(70, 5, date('m/d/Y'), 0, 1, 'C');
    } else {
        setlocale(LC_TIME, 'english');
        if ($fecg != '0000-00-00') {
            $fechaComite = date($fecg);
            $inicio = strftime("%B %d, %Y", strtotime($fechaComite));
        }
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(60, $anc, 'ACUMULATIVE AVERAGE', 1, 0, 'L', true);
        $pdf->Cell(35, $anc, number_format($prom / $ye, 2), 1, 1, 'R');
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(60, $anc, 'DATE OF GRADUATION', 1, 0, 'L', true);
        $pdf->Cell(35, $anc, $inicio, 1, 1, 'R');
        $pdf->Cell(40, 10, '', 0, 1);
        $pdf->Cell(70, 5, 'Authorized Signature', 'T', 0, 'C');
        $pdf->Cell(70, 5, '', 0, 0);
        $pdf->Cell(40, 5, 'Official Stamp', 0, 1);
        $pdf->Cell(70, 5, date('m/d/Y'), 0, 1, 'C');
    }
}
$pdf->Output();
