<?php
// ACADEMIA LA MILAGROSA
require_once __DIR__ . '/../../../app.php';
$anc = 5;
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
$promedioLetters  = [];
$cant = [];


$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],

]);

function Grado($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor <= '120' && $valor >= '90') {
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

function Years($grado, $ss)
{
    $row = DB::table('acumulativa')->select("year")
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->first();

    return $row->year ?? '';
}

function Maestro($grado, $ss)
{
    if (Years($grado, $ss)) {

        $row = DB::table('profesor')->select("nombre")
            ->whereRaw("ss = '$ss' and grado like '$grado%'")->first();
        return $row->nombre ?? '';
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

function  Curso($grado, $curso, $ss)
{
    global $conducta;
    global $promedio;
    global $cantc;
    global $cant;

    $row = DB::table('acumulativa')
        ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();

    $b = 0;
    $c = 0;
    if ($row->sem1 ?? 0 > 0 or $row->sem2 ?? 0 > 0) {
        if ($row->sem1 != '') {
            $c++;
            $b = $b + $row->sem1;
        }
        if ($row->sem2 != '') {
            $c++;
            $b = $b + $row->sem2;
        }
        if ($c > 0) {
            $valor = $b / $c;
            $promedio[$grado] += $valor;
            $cant[$grado]++;
        }

        if ($valor <> "") {
            $b = 0;
            $c = 0;
            if (Con($row->con2) != '') {
                $c++;
                $b = $b + Con($row->con2);
            }
            if (Con($row->con4) != '') {
                $c++;
                $b = $b + Con($row->con4);
            }
            if ($c > 0) {
                $conducta[$grado] = $b / $c;
                $cantc[$grado]++;
            }
        }
        return ($valor == 0) ? '' : round($valor);
    } else {
        return NULL;
    }
}


$school = new School(Session::id());
class nPDF extends PDF
{
    function Header()
    {
        global $idioma;
        parent::header();
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        if ($idioma == 'A') {
            $this->Cell(30, 5, 'TARJETA ACUMULATIVA', 0, 0, 'C');
        } else {
            $this->Cell(30, 5, 'SCHOOL TRANSCRIPT', 0, 0, 'C');
        }
        $this->Ln(15);
    }

    function Footer() {}
}

$anc = 5;
$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 11);

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
if ($idioma == 'A') {
    $nom1 = 'NOMBRE';
    $fec = 'FECHA';
    $gra = 'GRADO';
    $ano = utf8_encode('AÑO');
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

$nm = 0;
$ll = 1;
$let6 = 0;
$cnt6 = 0;
if ($opcion == '2') {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("year = '$Year' and grado = '$grados'")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $estu) {
    $pdf->AddPage();
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(30, 5, $nom1, 1, 0, 'C', true);
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(100, 5, $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(30, 5, $fec, 1, 0, 'C', true);
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(30, 5, DATE('m-d-Y'), 1, 1, 'C', true);

    $nm = 0;
    $nm7 = 0;

    $rega = DB::table('acumulativa')
        ->whereRaw("ss = '$estu->ss' and grado like '%" . $gra1 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb = DB::table('acumulativa')
        ->whereRaw("ss = '$estu->ss' and grado like '%" . $gra2 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

    if ($num_resultados1 > $num_resultados2) {
        $nm = $num_resultados1;
    } else {
        $nm = $num_resultados2;
    }

    $nm2 = $nm;
    $pdf->SetFont('Arial', '', 11);

    if ($num_resultados1 + $num_resultados2 > 0) {
        $nm7 = 1;

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
        $let6 = 0;
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;
        $cnt4 = 0;
        $cnt5 = 0;
        $cnt6 = 0;

        $pfa1 = '';
        $pfa2 = '';
        $pfb1 = '';
        $pfb2 = '';
        $cursosS1 = array();
        $cursosS2 = array();
        $num = 1;
        foreach ($rega as $row1) {
            $cursosS1[$num] = $row1;
            $num++;
        }
        $num = 1;
        foreach ($regb as $row1) {
            $cursosS2[$num] = $row1;
            $num++;
        }
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(24, 5, $cursosS1[1]->grado ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(22, 5, $ano, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(25, 5, $cursosS1[1]->year ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(24, 5, $cursosS2[1]->grado ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(22, 5, $ano, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(25, 5, $cursosS2[1]->year ?? '', 1, 1, 'C', true);

        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 0, 'C', true);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 10);

        for ($i = 1; $i <= $nm; $i++) {
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            $pdf->SetFont('Arial', '', 9);
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $cursosS1[$i]->desc1 ?? '', $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $cursosS1[$i]->desc2 ?? '', $ll, 0, 'L');
            }
            $pdf->SetFont('Arial', '', 10);
            if ($tnot=='A')
               {
               $pdf->Cell(14, $anc, Grado($cursosS1[$i]->sem1 ?? ''), $ll, 0, 'C');
               $pdf->Cell(14, $anc, Grado($cursosS1[$i]->sem2 ?? ''), $ll, 0, 'C');
               }
            if ($tnot=='B')
               {
               $pdf->Cell(14, $anc, $cursosS1[$i]->sem1 ?? '', $ll, 0, 'C');
               $pdf->Cell(14, $anc, $cursosS1[$i]->sem2 ?? '', $ll, 0, 'C');
               }

            $pdf->Cell(14, $anc, $cursosS1[$i]->credito ?? '', $ll, 0, 'R');
            if ($pf > 0 and $row1[13] > 0) {
                $pfa1 = $pfa1 + $pf;
                $pfa2 = $pfa2 + 1;
            }
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $cursosS2[$i]->desc1 ?? '', $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $cursosS2[$i]->desc2 ?? '', $ll, 0, 'L');
            }

            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            if ($tnot=='A')
               {
               $pdf->Cell(14, $anc, Grado($cursosS2[$i]->sem1 ?? ''), $ll, 0, 'C');
               $pdf->Cell(14, $anc, Grado($cursosS2[$i]->sem2 ?? ''), $ll, 0, 'C');
               }
            if ($tnot=='B')
               {
               $pdf->Cell(14, $anc, $cursosS2[$i]->sem1 ?? '', $ll, 0, 'C');
               $pdf->Cell(14, $anc, $cursosS2[$i]->sem2 ?? '', $ll, 0, 'C');
               }

            $pdf->Cell(14, $anc, $cursosS2[$i]->credito ?? '', $ll, 1, 'R');

            if ($cursosS1[$i]->sem1 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + (Con(Grado($cursosS1[$i]->sem1)) * $cursosS1[$i]->credito);
            }
            if ($cursosS1[$i]->sem2 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + (Con(Grado($cursosS1[$i]->sem2)) * $cursosS1[$i]->credito);
            }


            if ($cursosS1[$i]->sem1 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + $cursosS1[$i]->sem1 * $cursosS1[$i]->credito;
            }
            if ($cursosS1[$i]->sem2 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + $cursosS1[$i]->sem2 * $cursosS1[$i]->credito;
            }


            if ($cursosS2[$i]->sem1 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + (Con(Grado($cursosS2[$i]->sem1)) * $cursosS2[$i]->credito);
            }
            if ($cursosS2[$i]->sem2 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + (Con(Grado($cursosS2[$i]->sem2)) * $cursosS2[$i]->credito);
            }
            if ($cursosS2[$i]->sem1 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + $cursosS2[$i]->sem1 * $cursosS2[$i]->credito;
            }
            if ($cursosS2[$i]->sem2 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + $cursosS2[$i]->sem2 * $cursosS2[$i]->credito;
            }

            if ($cursosS1[$i]->credito ?? 0 > 0) {
                $cre5 = $cre5 + $cursosS1[$i]->credito;
            }
            if ($cursosS2[$i]->credito ?? 0 > 0) {
                $cre6 = $cre6 + $cursosS2[$i]->credito;
            }
        }


        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($cre1 > 0) {
            if ($tnot == 'A') {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2) . ' / ' . $not1 . ' / ' . $cre1, $ll, 0, 'C', true);
                $let6 = $let6 + round($not1 / $cre1, 2);
                $cnt6 = $cnt6 + 1;
            } else
        if ($tnot == 'C') {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2) . ' / ' . number_format($let1 / $cnt1, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2), $ll, 0, 'C', true);
                $let6 = $let6 + round($not1 / $cre1, 2);
                $cnt6 = $cnt6 + 1;
            }
        } else {
            $pdf->Cell(28, $anc, 'XXXX', $ll, 0, 'C', true);
        }
        if ($cre5 > 0) {
            $pdf->Cell(14, $anc, number_format($cre5, 2), $ll, 0, 'R', true);
        } else {
            $pdf->Cell(14, $anc, 'XXXX', $ll, 0, 'R', true);
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($cre2 > 0) {
            if ($tnot == 'A') {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2) . ' / ' . $not2 . ' / ' . $cre2, $ll, 0, 'C', true);
                $let6 = $let6 + round($not2 / $cre2, 2);
                $cnt6 = $cnt6 + 1;
            } else
        if ($tnot == 'C') {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2) . ' / ' . number_format($let2 / $cnt2, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2), $ll, 0, 'C', true);
                $let6 = $let6 + round($not2 / $cre2, 2);
                $cnt6 = $cnt6 + 1;
            }
        } else {
            $pdf->Cell(28, $anc, 'XXXX', $ll, 0, 'C', true);
        }
        if ($cre6 > 0) {
            $pdf->Cell(14, $anc, number_format($cre6, 2), $ll, 1, 'R', true);
        } else {
            $pdf->Cell(14, $anc, 'XXXX', $ll, 1, 'R', true);
        }
    }

    //************************************************************************************************************************
    $nm = 0;

    $rega = DB::table('acumulativa')
        ->whereRaw("ss = '$estu->ss' and grado like '%" . $gra3 . "%'")->orderBy('orden')->get();
    $num_resultados1 = count($rega);

    $regb = DB::table('acumulativa')
        ->whereRaw("ss = '$estu->ss' and grado like '%" . $gra4 . "%'")->orderBy('orden')->get();
    $num_resultados2 = count($regb);

    if ($num_resultados1 > $num_resultados2) {
        $nm = $num_resultados1;
    } else {
        $nm = $num_resultados2;
    }

    $nm2 = $nm;
    $pdf->SetFont('Arial', '', 11);

    if ($num_resultados1 + $num_resultados2 > 0) {
        $nm7 = 1;

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

        $pfa1 = '';
        $pfa2 = '';
        $pfb1 = '';
        $pfb2 = '';
        $cursosS1 = array();
        $cursosS2 = array();
        $num = 1;
        foreach ($rega as $row1) {
            $cursosS1[$num] = $row1;
            $num++;
        }
        $num = 1;
        foreach ($regb as $row1) {
            $cursosS2[$num] = $row1;
            $num++;
        }
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(24, 5, $cursosS1[1]->grado ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(22, 5, $ano, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(25, 5, $cursosS1[1]->year ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(24, 5, $gra, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(24, 5, $cursosS2[1]->grado ?? '', 1, 0, 'C', true);
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(22, 5, $ano, 1, 0, 'C', true);
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(25, 5, $cursosS2[1]->year ?? '', 1, 1, 'C', true);

        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 0, 'C', true);
        $pdf->Cell(53, 5, $des, 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-1', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'SEM-2', 1, 0, 'C', true);
        $pdf->Cell(14, 5, 'CRE.', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 9);

        for ($i = 1; $i <= $nm; $i++) {
            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';
            $pdf->SetFont('Arial', '', 9);
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $cursosS1[$i]->desc1 ?? '', $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $cursosS1[$i]->desc2 ?? '', $ll, 0, 'L');
            }
            $pdf->SetFont('Arial', '', 10);
            if ($tnot=='A')
               {
               $pdf->Cell(14, $anc, Grado($cursosS1[$i]->sem1 ?? ''), $ll, 0, 'C');
               $pdf->Cell(14, $anc, Grado($cursosS1[$i]->sem2 ?? ''), $ll, 0, 'C');
               }
            if ($tnot=='B')
               {
               $pdf->Cell(14, $anc, $cursosS1[$i]->sem1 ?? '', $ll, 0, 'C');
               $pdf->Cell(14, $anc, $cursosS1[$i]->sem2 ?? '', $ll, 0, 'C');
               }

            $pdf->Cell(14, $anc, $cursosS1[$i]->credito ?? '', $ll, 0, 'R');
            if ($pf > 0 and $row1[13] > 0) {
                $pfa1 = $pfa1 + $pf;
                $pfa2 = $pfa2 + 1;
            }
            if ($idioma == 'A') {
                $pdf->Cell(53, $anc, $cursosS2[$i]->desc1 ?? '', $ll, 0, 'L');
            } else {
                $pdf->Cell(53, $anc, $cursosS2[$i]->desc2 ?? '', $ll, 0, 'L');
            }

            $s1 = '';
            $s2 = '';
            $pf = '';
            $pf1 = '';
            $pf2 = '';

            if ($tnot=='A')
               {
               $pdf->Cell(14, $anc, Grado($cursosS2[$i]->sem1 ?? ''), $ll, 0, 'C');
               $pdf->Cell(14, $anc, Grado($cursosS2[$i]->sem2 ?? ''), $ll, 0, 'C');
               }
            if ($tnot=='B')
               {
               $pdf->Cell(14, $anc, $cursosS2[$i]->sem1 ?? '', $ll, 0, 'C');
               $pdf->Cell(14, $anc, $cursosS2[$i]->sem2 ?? '', $ll, 0, 'C');
               }

            $pdf->Cell(14, $anc, $cursosS2[$i]->credito ?? '', $ll, 1, 'R');

            if ($cursosS1[$i]->sem1 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + (Con(Grado($cursosS1[$i]->sem1)) * $cursosS1[$i]->credito);
            }
            if ($cursosS1[$i]->sem2 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + (Con(Grado($cursosS1[$i]->sem2)) * $cursosS1[$i]->credito);
            }


            if ($cursosS1[$i]->sem1 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + $cursosS1[$i]->sem1 * $cursosS1[$i]->credito;
            }
            if ($cursosS1[$i]->sem2 ?? 0 > 0 and $cursosS1[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre1 = $cre1 + $cursosS1[$i]->credito;
                $not1 = $not1 + $cursosS1[$i]->sem2 * $cursosS1[$i]->credito;
            }


            if ($cursosS2[$i]->sem1 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + (Con(Grado($cursosS2[$i]->sem1)) * $cursosS2[$i]->credito);
            }
            if ($cursosS2[$i]->sem2 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='A') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + (Con(Grado($cursosS2[$i]->sem2)) * $cursosS2[$i]->credito);
            }
            if ($cursosS2[$i]->sem1 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + $cursosS2[$i]->sem1 * $cursosS2[$i]->credito;
            }
            if ($cursosS2[$i]->sem2 ?? 0 > 0 and $cursosS2[$i]->credito ?? 0 > 0 and $tnot=='B') {
                $cre2 = $cre2 + $cursosS2[$i]->credito;
                $not2 = $not2 + $cursosS2[$i]->sem2 * $cursosS2[$i]->credito;
            }

            if ($cursosS1[$i]->credito ?? 0 > 0) {
                $cre5 = $cre5 + $cursosS1[$i]->credito;
            }
            if ($cursosS2[$i]->credito ?? 0 > 0) {
                $cre6 = $cre6 + $cursosS2[$i]->credito;
            }
        }

        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($cre1 > 0) {
            if ($tnot == 'A') {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2) . ' / ' . $not1 . ' / ' . $cre1, $ll, 0, 'C', true);
                $let6 = $let6 + round($not1 / $cre1, 2);
                $cnt6 = $cnt6 + 1;
            } else
        if ($tnot == 'C') {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2) . ' / ' . number_format($let1 / $cnt1, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, number_format($not1 / $cre1, 2), $ll, 0, 'C', true);
                $let6 = $let6 + round($not1 / $cre1, 2);
                $cnt6 = $cnt6 + 1;
            }
        } else {
            $pdf->Cell(28, $anc, 'XXXX', $ll, 0, 'C', true);
        }
        if ($cre5 > 0) {
            $pdf->Cell(14, $anc, number_format($cre5, 2), $ll, 0, 'R', true);
        } else {
            $pdf->Cell(14, $anc, 'XXXX', $ll, 0, 'R', true);
        }
        $pdf->Cell(53, $anc, $pr, $ll, 0, 'R', true);
        if ($cre2 > 0) {
            if ($tnot == 'A') {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2) . ' / ' . $not2 . ' / ' . $cre2, $ll, 0, 'C', true);
                $let6 = $let6 + round($not2 / $cre2, 2);
                $cnt6 = $cnt6 + 1;
            } else
        if ($tnot == 'C') {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2) . ' / ' . number_format($let2 / $cnt2, 2), $ll, 0, 'C', true);
            } else {
                $pdf->Cell(28, $anc, number_format($not2 / $cre2, 2), $ll, 0, 'C', true);
                $let6 = $let6 + round($not2 / $cre2, 2);
                $cnt6 = $cnt6 + 1;
            }
        } else {
            $pdf->Cell(28, $anc, 'XXXX', $ll, 0, 'C', true);
        }
        if ($cre6 > 0) {
            $pdf->Cell(14, $anc, number_format($cre6, 2), $ll, 1, 'R', true);
        } else {
            $pdf->Cell(14, $anc, 'XXXX', $ll, 1, 'R', true);
        }
    }

    //*************************
    $anc = 5;
    if ($idioma == 'A') {
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(53, 5, 'PROMEDIO ACUMULATIVO', 1, 0, 'L', true);
        if ($cnt6 > 0) {
            $pdf->Cell(14, 5, number_format($let6 / $cnt6, 2), 1, 1, 'R');
        } else {
            $pdf->Cell(14, 5, '', 1, 1, 'R');
        }
        $pdf->Cell(40, 15, '', 0, 1);
        $pdf->Cell(70, 5, 'Firma Autorizada', 'T', 0, 'C');
        $pdf->Cell(70, 5, '', 0, 0);
        $pdf->Cell(40, 5, 'Sello Oficial', 0, 1);
        $pdf->Cell(70, 5, date('m/d/Y'), 0, 1, 'C');
    } else {
        $pdf->Cell(40, 5, '', 0, 1);
        $pdf->Cell(53, $anc, 'ACUMULATIVE AVERAGE', 1, 0, 'L', true);
        if ($cnt6 > 0) {
            $pdf->Cell(14, 5, number_format($let6 / $cnt6, 2), 1, 1, 'R');
        } else {
            $pdf->Cell(14, $anc, '', 1, 1, 'R');
        }
        $x1 = '  ';
        $x2 = 'X';
        if ($nhc == 'true') {
            $x1 = 'X';
            $x2 = '  ';
        }
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 10, '', 0, 1);
        $pdf->Cell(40, 5, 'The student mentioned above ( ' . $x2 . ' ) Has / ( ' . $x1 . ' ) Has Not complete all the requirement to obtain a HIGH SCHOOL DIPLOMA.', 0, 1);
        $pdf->Cell(40, 5, "Courses marked with * weren't taken at Academia la Milagrosa de Cayey.", 0, 1);
        $pdf->Cell(40, 5, 'NOTE: THERE ARE NO MARKS OR FRASED INFORMATION.', 0, 1);
        $pdf->Cell(40, 15, '', 0, 1);
        $pdf->Cell(70, 5, 'Autorized Signature', 'T', 0, 'C');
        $pdf->Cell(70, 5, '', 0, 0);
        $pdf->Cell(40, 5, 'Oficial Stamp', 0, 1);
        $pdf->Cell(70, 5, date('m/d/Y'), 0, 1, 'C');
    }
}
$pdf->Output();
?>