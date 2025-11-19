<?php
// colegio bautista de gurabo
list($gra, $sec) = explode("-", $_POST['grade'] . '-');

if ($gra == 'PK') {
    require('TarjetaNotas17b.php');
    exit;
}
if ($gra == 'KG') {
    require('TarjetaNotas17c.php');
    exit;
}
require_once __DIR__ . '/../../../../app.php';

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
        $this->Cell(30, 3, 'TARJETA DE NOTAS', 0, 0, 'C');
        $this->Ln(8);
    }

    function Footer()
    {

        $this->SetFont('Arial', 'B', 12);
        $this->SetY(-70);
        $this->Cell(60, 6, 'ESCALA', 1, 1, 'C',true);
        $this->Cell(60, 30, '', 1, 1, 'C');
        $this->SetFont('Arial', 'B', 10);

        $this->SetY(-61);
        $this->Cell(50, 5, '100 - 90 = A    4.00 - 3.50 ', 0, 1, 'R');
        $this->Cell(50, 5, ' 89 - 80 = B    3.49 - 2.50 ', 0, 1, 'R');
        $this->Cell(50, 5, ' 79 - 70 = C    2.49 - 1.50 ', 0, 1, 'R');
        $this->Cell(50, 5, ' 69 - 60 = D    1.49 - 1.00 ', 0, 1, 'R');
        $this->Cell(50, 5, ' 59 -   0 = F    0.99 - 0.00 ', 0, 1, 'R');
        $this->SetY(-20);
        $this->Cell(50, 5, '_____________________________', 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'C');
        $this->Cell(50, 5, '_____________________________', 0, 1, 'C');
        $this->Cell(50, 5, 'MAESTRO(A)', 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'C');
        $this->Cell(50, 5, 'PRINCIPAL', 0, 0, 'C');
    }
}

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$pdf->AliasNbPages();
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
$ccr = $_POST['cr'] ?? '';
$tri = $_POST['tri'] ?? '';
$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);


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
    $text1 = $row11[3];
    $text2 = $row11[4];
    $fi = 'YR';
    $f2 = 'AVG';
    $se1 = 'FIRST SEMESTER';
    $se2 = 'SECOND SEMESTER';
    $qq1 = '    Q-1        Q-2          AVER.';
    $qq2 = '    Q-3        Q-4          AVER.';
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
    $text1 = $mensaj->t1e ?? '';
    $text2 = $mensaj->t2e ?? '';
    $fi = 'PRO';
    $f2 = utf8_encode('AÑO');
    $se1 = 'PRIMER SEMESTRE';
    $se2 = 'SEGUNDO SEMESTRE';
    $qq1 = '    T-1        T-2          PROM.';
    $qq2 = '    T-3        T-4          PROM.';
    $pq = 'PROMEDIO';
    $asi = 'AUSENCIAS Y TARDANZAS';
}

$a = 0;
foreach ($students as $estu) {
    $pdf->AddPage();
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    //    $pdf->Cell(0, 5, $lang->translation("Reporte de Notas") . " $year", 0, 1, 'C');
    $pdf->Ln(10);
    $a = $a + 1;

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
    $pdf->Cell(10, 5, '', 0, 0, 'L');
    $pdf->Cell(22, 5, '', 0, 0, '');
    $pdf->Cell(52, 5, '', 0, 0, '');
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');
    $pdf->Cell(67, 5, $nom . ' ' . $ape, 0, 1, 'L');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(125, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', '', 11);
    list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
    $pdf->Cell(35, 5, 'S.S.: XXX-XX-' . $ss3, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $gr . ' ' . $grade, 1, 1, 'C', true);

    $pdf->Cell(60, 5, $de, 1, 0, 'C',true);
    $pdf->Cell(50, 5, $se1, 1, 0, 'C',true);
    $pdf->Cell(50, 5, $se2, 1, 0, 'C',true);
    $pdf->Cell(15, 5, $fi, 1, 0, 'C',true);
    $pdf->Cell(15, 5, 'CR', 1, 1, 'C',true);

    $pdf->Cell(60, 5, '', 1, 0, 'R',true);
    $pdf->Cell(50, 5, $qq1, 1, 0, 'L',true);
    $pdf->Cell(50, 5, $qq2, 1, 0, 'L',true);
    $pdf->Cell(15, 5, $f2, 1, 0, 'C',true);
    $pdf->Cell(15, 5, '', 1, 1, 'C',true);

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
    foreach ($cursos as $curso) {
        $V5 = 0;
        $V6 = 0;
        $tot1t = "";
        $v7 = 0;
        $v8 = 0;
        $tot1t1 = "";
        if ($curso->credito > 0 and $curso->nota1 > 0 or $curso->credito > 0 and $curso->nota1 == '0') {
            $cr = $cr + 1;
            $notas = $notas + $curso->nota1;
        }

        if ($curso->credito > 0 and $curso->nota2 > 0 or $curso->credito > 0 and $curso->nota2 == '0') {
            $cr2 = $cr2 + 1;
            $notas2 = $notas2 + $curso->nota2;
        }

        if ($curso->credito > 0 and $curso->nota3 > 0 or $curso->credito > 0 and $curso->nota3 == '0') {
            $cr3 = $cr3 + 1;
            $notas3 = $notas3 + $curso->nota3;
        }

        if ($curso->credito > 0 and $curso->nota4 > 0 or $curso->credito > 0 and $curso->nota4 == '0') {
            $cr4 = $cr4 + 1;
            $notas4 = $notas4 + $curso->nota4;
        }
        if ($curso->credito > 0 and $curso->sem1 > 0) {
            $cr5 = $cr5 + 1;
            $notas5 = $notas5 + $curso->sem1;
        }
        if ($curso->credito > 0 and $curso->sem2 > 0) {
            $cr6 = $cr6 + 1;
            $notas6 = $notas6 + $curso->sem2;
        }
        if ($curso->credito > 0 and $curso->final > 0) {
            $cr7 = $cr7 + 1;
            $notas7 = $notas7 + $curso->final;
        }

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
        if ($idi == 'Ingles') {
            $pdf->Cell(60, 5, $curso->descripcion, 1, 0);
        } else {
            $pdf->Cell(60, 5, $curso->descripcion, 1, 0);
        }
        $pdf->Cell(15, 5, $curso->nota1, 1, 0, 'C');
        $pdf->Cell(15, 5, $curso->nota2, 1, 0, 'C');

        if ($sem1 == 'Si') {
            $pdf->Cell(20, 5, $curso->sem1, 1, 0, 'C');
        } else {
            $pdf->Cell(20, 5, '', 1, 0, 'C');
        }


        $pdf->Cell(15, 5, $curso->nota3, 1, 0, 'C');
        $pdf->Cell(15, 5, $curso->nota4, 1, 0, 'C');
        if ($sem2 == 'Si') {
            $pdf->Cell(20, 5, $curso->sem2, 1, 0, 'C');
        } else {
            $pdf->Cell(20, 5, '', 1, 0, 'C');
        }
        if ($prof == 'Si') {
            $pdf->Cell(15, 5, $curso->final, 1, 0, 'C');
        } else {
            $pdf->Cell(15, 5, '', 1, 0, 'C');
        }

        $cr1 = '';
        if ($ccr == 'Si') {
            $cr1 = $curso->credito;
        }
        $pdf->Cell(15, 5, $cr1, 1, 1, 'R');

        $pdf->Cell(60, 5, '     ' . $curso->profesor, 1, 0);
        $pdf->Cell(15, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 0, 'C');

        $pdf->Cell(20, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 0, 'L');
        $pdf->Cell(20, 5, '', 1, 0, 'L');
        $pdf->Cell(15, 5, '', 1, 0, 'L');
        $pdf->Cell(15, 5, '', 1, 1, 'L');
    }
    $pdf->Cell(60, 5, $pq, 1, 0, 'R');
    if ($cr > 0) {
        $pdf->Cell(15, 5, round($notas / $cr, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C');
    }
    if ($cr2 > 0) {
        $pdf->Cell(15, 5, round($notas2 / $cr2, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C');
    }

    $pdf->Cell(20, 5, '', 1, 0, 'C');

    if ($cr3 > 0) {
        $pdf->Cell(15, 5, round($notas3 / $cr3, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C');
    }
    if ($cr4 > 0) {
        $pdf->Cell(15, 5, round($notas4 / $cr4, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C');
    }

    $pdf->Cell(20, 5, '', 1, 0, 'C');
    if ($prof == 'Si' and $cr7 > 0) {
        $pdf->Cell(15, 5, round($notas7 / $cr7, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C');
    }

    if ($ccr == 'Si') {
        $pdf->Cell(15, 5, number_format($cr, 2, '.', ''), 1, 1, 'R');
    } else {
        $pdf->Cell(15, 5, '', 1, 1, 'R');
    }

    $pdf->Cell(60, 5, $asi, 1, 0, 'R');
    $pdf->Cell(15, 5, $au . ' / ' . $ta, 1, 0, 'C');
    $pdf->Cell(15, 5, $au2 . ' / ' . $ta2, 1, 0, 'C');
    $pdf->Cell(20, 5, '', 1, 0, 'C');

    $pdf->Cell(15, 5, $au3 . ' / ' . $ta3, 1, 0, 'C');
    $pdf->Cell(15, 5, $au4 . ' / ' . $ta4, 1, 0, 'C');
    $pdf->Cell(20, 5, '', 1, 0, 'R');
    $pdf->Cell(15, 5, '', 1, 0, 'C');
    $pdf->Cell(15, 5, '', 1, 1, 'R');

    $pdf->Cell(1, 5, '', 0, 1, 'R');
    $pdf->Cell(190, 10, '', 1, 1, 'L');
    $pdf->Cell(1, -15, '', 0, 0, 'R');
    $pdf->Cell(190, -15, $text1, 0, 1, 'C');
    $pdf->Cell(1, 23, '', 0, 0, 'R');
    $pdf->Cell(190, 23, $text2, 0, 1, 'C');
}

$pdf->Output();
