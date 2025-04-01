<?php
// COLEGIO SAN ANTONIO ABAD
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Reporte de Notas', 'Grade Report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Año escolar:", "School year:"],
    ["DESCRIPCION", "DESCRIPTION"],
    ['PRIMER SEMESTRE', 'FIRST SEMESTER'],
    ['SEGUNDO SEMESTRE', 'SECOND SEMESTER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['AÑO', 'YEAR'],
]);

function NLetra($valor)
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
    } else  if ($valor <= '59' && $valor >= '1') {
        return 'F';
    } else  if ($valor != '') {
        return $valor;
    } else  if ($valor == '') {
        return '';
    }
}
function LValor($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor == 'A') {
        return 4.00;
    } else if ($valor == 'B') {
        return 3.00;
    } else if ($valor == 'C') {
        return 2.00;
    } else if ($valor == 'D') {
        return 1.00;
    } else  if ($valor == 'F') {
        return 0.00;
    } else  if ($valor != '') {
        return $valor;
    } else  if ($valor == '') {
        return '';
    }
}
function VPuntos($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor <= 4.00 && $valor >= 3.50) {
        return 'A';
    } else if ($valor <= 3.49 && $valor >= 2.50) {
        return 'B';
    } else if ($valor <= 2.49 && $valor >= 1.50) {
        return 'C';
    } else if ($valor <= 1.49 && $valor >= 0.50) {
        return 'D';
    } else  if ($valor <= 0.49 && $valor >= 0.01) {
        return 'F';
    } else  if ($valor != '') {
        return $valor;
    } else  if ($valor == '') {
        return '';
    }
}

class nPDF extends PDF
{
    function Header()
    {
        parent::header();
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, 'REPORT CARD', 0, 0, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-65);
        $this->Cell(30, 5, 'LEYENDA DE NOTAS', 0, 1, 'C');
        $this->Cell(25, 3, 'A = 4.00 - 3.50', 0, 1, 'R');
        $this->Cell(25, 3, 'B = 3.49 - 2.50', 0, 1, 'R');
        $this->Cell(25, 3, 'C = 2.49 - 1.50', 0, 1, 'R');
        $this->Cell(25, 3, 'D = 1.49 - 0.50', 0, 1, 'R');
        $this->Cell(25, 3, 'F = 0.49 - 0.00', 0, 1, 'R');
        $this->Cell(50, 5, '', 0, 1, 'C');

        $this->Cell(30, 5, 'LEYENDA', 0, 1, 'C');
        $this->Cell(40, 3, 'A  = AUSENCIAS', 0, 1, 'L');
        $this->Cell(40, 3, 'T  = TARDANZAS', 0, 1, 'L');
        $this->Cell(40, 3, '2T = 2DO TRIMESTRE', 0, 1, 'L');
        $this->Cell(40, 3, 'S-1 = 1ER SEMESTRE', 0, 1, 'L');
        $this->Cell(40, 3, '3T = 3ER TRIMESTRE', 0, 0, 'L');
        $this->Cell(95, 3, '', 0, 0, 'C');
        $this->Cell(50, 3, '_________________________________', 0, 1, 'C');
        $this->Cell(40, 3, '4T = 4TO TRIMESTRE', 0, 0, 'L');
        $this->Cell(95, 3, '', 0, 0, 'C');
        $this->Cell(50, 3, 'PRINCIPAL', 0, 1, 'C');
        $this->Cell(40, 3, 'S-2 = 2DO SEMESTRE', 0, 1, 'L');
        $this->Cell(40, 3, 'CR = CREDITOS', 0, 1, 'L');
        $this->SetY(-50);
        $this->Cell(70, 3, '', 0, 0, 'C');
        $this->Cell(50, 30, 'SELLO', 1, 0, 'C');
    }
}

//Creacin del objeto de la clase heredada
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Times', '', 11);

$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';

$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();


$idi = '';
if ($idi == 'Ingles') {
    $ye = 'SCHOOL YEAR:';
    $no = 'Name: ';
    $gr = 'Grade: ';
    $de = 'DESCRIPTION';
    $pr = 'AVG';
    $va = 'Assigned Value';
    $fe = 'Dates';
    $rr = 20;
    $fi = 'YR';
    $f2 = 'AVG';
    $se1 = 'FIRST SEMESTER';
    $se2 = 'SECOND SEMESTER';
    $qq1 = '   Q1     CO       Q2     CO';
    $qq2 = '   Q3     CO       Q4     CO';
    $pq = 'AVERAGE.';
    $asi = 'ABSENCE AND LATE';
} else {
    $ye = utf8_encode('AÑO ESCOLAR:');
    $no = 'Nombre: ';
    $gr = 'Grado: ';
    $de = 'DESCRIPCION';
    $pr = 'PRO';
    $va = 'Valor Asignado';
    $fe = 'Fechas';
    $rr = 0;
    $fi = 'PRO';
    $f2 = utf8_encode('AÑO');
    $se1 = 'PRIMER SEMESTRE';
    $se2 = 'SEGUNDO SEMESTRE';
    $qq1 = '  1T    A     T    2T     A    T     S-1';
    $qq2 = '  3T    A     T    4T     A    T     S-2';
    $pq = 'PROMEDIO';
    $asi = 'AUSENCIAS Y TARDANZAS';
}

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

foreach ($students as $estu) {
    $a = 0;
    $gra = '';
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(1, 5, '', 0, 0, 'R');
    $pdf->Cell(30, 5, $ye, 0, 0, 'L');
    $pdf->Cell(55, 5, $year, 0, 0, '');
    $pdf->Cell(7, 5, ' ', 0, 0, 'L');
    $pdf->Cell(1, 5, '', 0, 1, 'R');
    $pdf->Cell(8, 5, '', 0, 0, 'R');
    $pdf->Cell(10, 5, 'FECHA: ', 0, 0, 'R');
    $pdf->Cell(24, 5, date("m-d-Y"), 0, 0, 'R');
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');
    $pdf->Cell(32, 5, '', 0, 0, '');
    $pdf->Cell(52, 5, 'MAESTRO SALON HOGAR:', 0, 0, '');
    $pdf->Cell(67, 5, $nom . ' ' . $ape, 0, 1, 'L');
    $pdf->Cell(1, 5, ' ', 0, 0, 'C');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(125, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', '', 11);
    list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
    $pdf->Cell(37, 5, 'S.S.: XXX-XX-' . $ss3, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $gr . ' ' . $grade, 1, 1, 'C', true);

    $pdf->Cell(1, 5, ' ', 0, 0, 'C');
    $pdf->Cell(56, 5, $de, 1, 0, 'C', true);
    $pdf->Cell(56, 5, $se1, 1, 0, 'C', true);
    $pdf->Cell(56, 5, $se2, 1, 0, 'C', true);
    $pdf->Cell(14, 5, $fi, 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'CR', 1, 1, 'C', true);
    $pdf->Cell(1, 5, '  ', 0, 0, 'R');
    $pdf->Cell(56, 5, '', 1, 0, 'R', true);
    $pdf->Cell(56, 5, $qq1, 1, 0, 'L', true);
    $pdf->Cell(56, 5, $qq2, 1, 0, 'L', true);
    $pdf->Cell(14, 5, $f2, 1, 0, 'C', true);
    $pdf->Cell(10, 5, '', 1, 1, 'C', true);
    $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();

    $notas = 0;
    $cr = 0;
    $au = 0;
    $ta = 0;

    $notas2 = 0;
    $cr2 = 0;
    $au2 = 0;
    $ta2 = 0;

    $notas3 = 0;
    $cr3 = 0;
    $au3 = 0;
    $ta3 = 0;

    $notas4 = 0;
    $cr4 = 0;
    $au4 = 0;
    $ta4 = 0;
    $notas5 = 0;
    $cr5 = 0;
    $notas6 = 0;
    $cr6 = 0;
    $notas7 = 0;
    $cr7 = 0;

    $nfs1 = 0;
    $cfs1 = 0;
    $nfs2 = 0;
    $cfs2 = 0;

    $tcrs = 0;

    foreach ($cursos as $curso) {
        $a = $a + 1;
        if ($curso->aus1 > 0) {
            $au = $au + number_format($curso->aus1, 0);
        }
        if ($curso->tar1 > 0) {
            $ta = $ta + $curso->tar1;
        }
        if ($curso->aus2 > 0) {
            $au2 = $au2 + number_format($curso->aus2, 0);
        }
        if ($curso->tar2 > 0) {
            $ta2 = $ta2 + $curso->tar2;
        }
        if ($curso->aus3 > 0) {
            $au3 = $au3 + number_format($curso->aus3, 0);
        }
        if ($curso->tar3 > 0) {
            $ta3 = $ta3 + $curso->tar3;
        }
        if ($curso->aus4 > 0) {
            $au4 = $au4 + number_format($curso->aus4, 0);
        }
        if ($curso->tar4 > 0) {
            $ta4 = $ta4 + $curso->tar4;
        }
        $pdf->SetFont('Times', '', 9);
        $pdf->Cell(1, 5, '  ', 0, 0, 'R');
        $pdf->Cell(56, 5, $curso->descripcion, 1, 0, 'L');
        $not = 0;
        $not2 = 0;
        $not3 = 0;
        $not4 = 0;
        $not5 = 0;
        $not6 = 0;
        $tcrs = $tcrs + $curso->credito;
        if ($tri1 == 'Si' and !empty($curso->nota1)) {
            if ($curso->credito > 0) {
                $notas = $notas + (LValor(NLetra($curso->nota1)) * $curso->credito);
                $cr = $cr + $curso->credito;
            }
            $not2 = $not2 + 1;
            $pdf->Cell(9, 5, NLetra($curso->nota1), 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->aus1, 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->tar1, 1, 0, 'C');
            $not = $not + LValor(NLetra($curso->nota1));
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($tri2 == 'Si' and !empty($curso->nota2)) {
            if ($curso->credito > 0) {
                $notas2 = $notas2 + (LValor(NLetra($curso->nota2)) * $curso->credito);
                $cr2 = $cr2 + $curso->credito;
            }
            $not2 = $not2 + 1;
            $not5 = $not5 + 1;
            $pdf->Cell(9, 5, NLetra($curso->nota2), 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->aus2, 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->tar2, 1, 0, 'C');
            $not = $not + LValor(NLetra($curso->nota2));
            $nnf1 = VPuntos(round($not / $not2, 2));
            $not6 = $not6 + round($not / $not2, 2);

            if ($curso->credito > 0 and $not2 > 0) {
                $notas5 = $notas5 + LValor($nnf1) * $curso->credito;
                $cr5 = $cr5 + $curso->credito;
            }
            $pdf->Cell(10, 5, $nnf1, 1, 0, 'C', true);
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        }
        //****************************************************
        if ($tri3 == 'Si' and !empty($curso->nota3)) {
            if ($curso->credito > 0) {
                $notas3 = $notas3 + (LValor(NLetra($curso->nota3)) * $curso->credito);
                $cr3 = $cr3 + $curso->credito;
            }
            $not4 = $not4 + 1;
            $pdf->Cell(9, 5, NLetra($curso->nota3), 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->aus3, 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->tar3, 1, 0, 'C');
            $not3 = $not3 + LValor(NLetra($curso->nota3));
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($tri4 == 'Si' and !empty($curso->nota4)) {
            if ($curso->credito > 0) {
                $notas4 = $notas4 + (LValor(NLetra($curso->nota4)) * $curso->credito);
                $cr4 = $cr4 + $curso->credito;
            }
            $not4 = $not4 + 1;
            $not5 = $not5 + 1;
            $pdf->Cell(9, 5, NLetra($curso->nota4), 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->aus4, 1, 0, 'C');
            $pdf->Cell(7, 5, $curso->tar4, 1, 0, 'C');
            $not3 = $not3 + LValor(NLetra($curso->nota4));
            $nnf1 = VPuntos(round($not3 / $not4, 2));
            $not6 = $not6 + round($not3 / $not4, 2);
            if ($curso->credito > 0 and $not4 > 0) {
                $notas6 = $notas6 + LValor($nnf1) * $curso->credito;
                $cr6 = $cr6 + $curso->credito;
            }
            $pdf->Cell(10, 5, $nnf1, 1, 0, 'C', true);
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
            $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        }
        if ($prof == 'Si' and $not5 > 0) {
            $nnf1 = VPuntos(round($not6 / $not5, 2));
            $pdf->Cell(14, 5, $nnf1, 1, 0, 'C', true);
            $notas7 = $notas7 + LValor($nnf1) * $curso->credito;
            $cr7 = $cr7 + $curso->credito;
        } else {
            $pdf->Cell(14, 5, '', 1, 0, 'C', true);
        }
        $pdf->Cell(10, 5, $curso->credito, 1, 1, 'R');
        $pdf->Cell(1, 5, '  ', 0, 0, 'R');
        $pdf->Cell(56, 5, '    ' . $curso->profesor, 1, 0, 'L');
        $pdf->Cell(9, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(9, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(9, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(9, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(14, 5, '', 1, 0, 'C', true);
        $pdf->Cell(10, 5, '', 1, 1, 'R');
    }
    $pdf->Cell(1, 5, '  ', 0, 0, 'R');
    $pdf->Cell(56, 5, 'PROMEDIO', 1, 0, 'R', true);
    $nnf1 = '';
    if ($cr > 0) {
        $nnf1 = VPuntos(round($notas / $cr, 2));
    }
    $pdf->Cell(9, 5, $nnf1, 1, 0, 'C', true);
    $pdf->Cell(7, 5, '', 1, 0, 'C', true);
    $pdf->Cell(7, 5, '', 1, 0, 'C', true);
    $nnf1 = '';
    $nnf2 = '';
    if ($tri2 == 'Si' and $cr2 > 0) {
        $nnf1 = VPuntos(round($notas2 / $cr2, 2));
        $nnf2 = round($notas5 / $cr5, 2);
    }
    $pdf->Cell(9, 5, $nnf1, 1, 0, 'C', true);
    $pdf->Cell(14, 5, $nnf2, 1, 0, 'C', true);
    $nnf1 = '';
    if ($tri2 == 'Si' and $cr5 > 0) {
        $nnf1 = VPuntos(round($notas5 / $cr5, 2));
    }
    $pdf->Cell(10, 5, $nnf1, 1, 0, 'C', true);
    $nnf1 = '';
    if ($tri3 == 'Si' and $cr3 > 0) {
        $nnf1 = VPuntos(round($notas3 / $cr3, 2));
    }
    $pdf->Cell(9, 5, $nnf1, 1, 0, 'C', true);
    $pdf->Cell(7, 5, '', 1, 0, 'C', true);
    $pdf->Cell(7, 5, '', 1, 0, 'C', true);
    $nnf1 = '';
    $nnf2 = '';
    if ($tri4 == 'Si' and $cr4 > 0) {
        $nnf1 = VPuntos(round($notas4 / $cr4, 2));
        $nnf2 = round($notas4 / $cr4, 2);
    }
    $pdf->Cell(9, 5, $nnf1, 1, 0, 'C', true);
    $pdf->Cell(14, 5, $nnf2, 1, 0, 'C', true);
    $nnf1 = '';
    if ($tri4 == 'Si' and $cr6 > 0) {
        $nnf1 = VPuntos(round($notas6 / $cr6, 2));
    }
    $pdf->Cell(10, 5, $nnf1, 1, 0, 'C', true);
    $nnf1 = '';
    if ($tri4 == 'Si' and $cr7 > 0) {
        $nnf1 = VPuntos(round($notas7 / $cr7, 2));
    }
    $pdf->Cell(14, 5, $nnf1, 1, 0, 'C', true);
    $pdf->Cell(10, 5, $tcrs, 1, 1, 'R', true);
    $pdf->AddPage();
}

$pdf->Output();
