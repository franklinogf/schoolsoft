<?php
// Academia Cristiana Yarah

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
        $this->Cell(120);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, 'TARJETA DE NOTAS', 0, 0, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetY(-35);
        $this->Cell(200, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, 'ESCALA', 1, 1, 'C', true);
        $this->Cell(200, 5, '', 0, 0, 'C');
        $this->Cell(60, 20, '', 1, 1, 'C');
        $this->SetFont('Arial', 'B', 10);

        $this->SetY(-30);
        $this->Cell(200, 4, '', 0, 0, 'C');
        $this->Cell(50, 4, '100 - 90 = A    4.00 - 3.50 ', 0, 1, 'R');
        $this->Cell(200, 4, '', 0, 0, 'C');
        $this->Cell(50, 4, ' 89 - 80 = B    3.49 - 2.50 ', 0, 1, 'R');
        $this->Cell(200, 4, '', 0, 0, 'C');
        $this->Cell(50, 4, ' 79 - 70 = C    2.49 - 1.60 ', 0, 1, 'R');
        $this->Cell(200, 4, '', 0, 0, 'C');
        $this->Cell(50, 4, ' 69 - 60 = D    1.59 - 0.80 ', 0, 1, 'R');
        $this->Cell(200, 4, '', 0, 0, 'C');
        $this->Cell(50, 4, ' 59 -   0 = F    0.79 - 0.00 ', 0, 1, 'R');
        $this->SetY(-20);
        $this->Cell(60, 5, '_____________________________', 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, '_____________________________', 0, 1, 'C');
        $this->Cell(60, 5, 'MAESTRO(A)', 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, 'DIRECTORA', 0, 0, 'C');
        //	$this->Image('../logo/firma3.gif',85,180,60);
    }
}

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
//$pdf->Fill();

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
    //  $text1=$row11[3];
    //  $text2=$row11[4];
    $fi = 'YR';
    $f2 = 'AVG';
    $se1 = 'FIRST SEMESTER';
    $se2 = 'SECOND SEMESTER';
    $qq1 = '    Q-1        Q-2          AVER.';
    $qq2 = '    Q-3        Q-4          AVER.';
    $pq = 'AVERAGE.';
    $asi = 'ABSENCE AND LATE';
} else {
    $ye = 'AÑO ESCOLAR:';
    $no = 'Nombre: ';
    $gr = 'Grado: ';
    $de = 'DESCRIPCIÓN';
    $pr = 'PRO';
    $va = 'Valor Asignado';
    $fe = 'Fechas';
    $rr = 0;
    //  $text1=$row11[1];
    //  $text2=$row11[2];
    $fi = 'PRO';
    $f2 = 'AÑO';
    $se1 = 'PRIMER SEMESTRE';
    $se2 = 'SEGUNDO SEMESTRE';
    $qq1 = '   10-S        20-S         PROM.';
    $qq2 = '   30-S        40-S         PROM.';
    $pq = 'PROMEDIO';
    $asi = 'AUSENCIAS Y TARDANZAS';
}

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

$a = 0;
// $pdf->SetFillColor(224,235,255);

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

foreach ($students as $estu) {
    $pdf->Fill();
    $pdf->AddPage('L');
    $a = $a + 1;
    $gra = '';
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(1, 2, '', 0, 1, 'R');
    $pdf->Cell(8, 5, '', 0, 0, 'R');
    $pdf->Cell(10, 5, 'FECHA: ', 0, 0, 'R');
    $pdf->Cell(24, 5, date("m-d-Y"), 0, 0, 'R');
    $pdf->Cell(10, 5, '$ac', 0, 0, 'L');
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');

    $pdf->Cell(120, 5, '', 0, 0, '');
    $pdf->Cell(50, 5, utf8_encode('MAESTRO SALÓN HOGAR:'), 0, 0, '');
    $pdf->Cell(70, 5, $nom . ' ' . $ape, 0, 1, 'L');

    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(130, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', '', 11);
    list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
    $pdf->Cell(45, 5, 'S.S.: XXX-XX-' . $ss3, 1, 0, 'C', true);
    $pdf->Cell(45, 5, $gr . ' ' . $grade, 1, 0, 'C', true);
    $pdf->Cell(50, 5, utf8_encode('AÑO ESCOLAR: ') . $year, 1, 1, 'C', true);
    $pdf->Cell(60, 5, utf8_encode($de), 1, 0, 'C', true);
    $pdf->Cell(90, 5, $se1, 1, 0, 'C', true);
    $pdf->Cell(90, 5, $se2, 1, 0, 'C', true);
    $pdf->Cell(15, 5, $fi, 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'CR', 1, 1, 'C', true);

    $pdf->Cell(60, 5, '', 1, 0, 'R', true);

    $pdf->Cell(15, 5, '10-S', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'A/T', 1, 0, 'C', true);
    $pdf->Cell(15, 5, '20-S', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'A/T', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'PROM.', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'T. A/T', 1, 0, 'C', true);
    $pdf->Cell(15, 5, '30-S', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'A/T', 1, 0, 'C', true);
    $pdf->Cell(15, 5, '40-S', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'A/T', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'PROM.', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'T. A/T', 1, 0, 'C', true);

    $pdf->Cell(15, 5, utf8_encode($f2), 1, 0, 'C', true);
    $pdf->Cell(15, 5, '', 1, 1, 'C', true);
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

    $au = 0;
    $ta = 0;
    $au2 = 0;
    $ta2 = 0;
    $au3 = 0;
    $ta3 = 0;
    $au4 = 0;
    $ta4 = 0;

    $b = 0;
    foreach ($cursos as $row) {
        $V5 = 0;
        $V6 = 0;
        $tot1t = "";
        $v7 = 0;
        $v8 = 0;
        $tot1t1 = "";
        $au = 0;
        $ta = 0;
        $au2 = 0;
        $ta2 = 0;
        $au3 = 0;
        $ta3 = 0;
        $au4 = 0;
        $ta4 = 0;

        if ($row->credito > 0 and $row->nota1 > 0 or $row->credito > 0 and $row->nota1 == '0') {
            $cr = $cr + 1;
            $notas = $notas + $row->nota1;
        }

        if ($row->credito > 0 and $row->nota2 > 0 or $row->credito > 0 and $row->nota2 == '0') {
            $cr2 = $cr2 + 1;
            $notas2 = $notas2 + $row->nota2;
        }

        if ($row->credito > 0 and $row->nota3 > 0 or $row->credito > 0 and $row->nota3 == '0') {
            $cr3 = $cr3 + 1;
            $notas3 = $notas3 + $row->nota3;
        }

        if ($row->credito > 0 and $row->nota4 > 0 or $row->credito > 0 and $row->nota4 == '0') {
            $cr4 = $cr4 + 1;
            $notas4 = $notas4 + $row->nota4;
        }
        if ($row->credito > 0 and $row->sem1 > 0) {
            $cr5 = $cr5 + 1;
            $notas5 = $notas5 + $row->sem1;
        }
        if ($row->credito > 0 and $row->sem2 > 0) {
            $cr6 = $cr6 + 1;
            $notas6 = $notas6 + $row->sem2;
        }
        if ($row->credito > 0 and $row->final > 0) {
            $cr7 = $cr7 + 1;
            $notas7 = $notas7 + $row->final;
        }

        if ($row->aus1 > 0) {
            $au = $au + number_format($row->aus1, 0);
        }
        if ($row->tar1 > 0) {
            $ta = $ta + $row->tar1;
        }
        if ($row->aus2 > 0) {
            $au2 = $au2 + number_format($row->aus2, 0);
        }
        if ($row->tar2 > 0) {
            $ta2 = $ta2 + $row->tar2;
        }
        if ($row->aus3 > 0) {
            $au3 = $au3 + number_format($row->aus3, 0);
        }
        if ($row->tar3 > 0) {
            $ta3 = $ta3 + $row->tar3;
        }
        if ($row->aus4 > 0) {
            $au4 = $au4 + number_format($row->aus4, 0);
        }
        if ($row->tar4 > 0) {
            $ta4 = $ta4 + $row->tar4;
        }


        $pdf->SetFont('Times', '', 9);
        $b = $b + 1;
        $t = '';
        if ($b == 2) {
            $pdf->SetFillColor(224, 235, 255);
            $t = 'true';
            $b = 0;
        }
        if ($idi == 'Ingles') {
            $pdf->Cell(60, 5, $row->desc2, 1, 0, 'L', $t);
        } else {
            $pdf->Cell(60, 5, $row->descripcion, 1, 0, 'L', $t);
        }
        $pdf->Cell(15, 5, $row->nota1, 1, 0, 'C', $t);

        $pdf->Cell(15, 5, $au . '/' . $ta, 1, 0, 'C', $t);

        $pdf->Cell(15, 5, $row->nota2, 1, 0, 'C', $t);
        $pdf->Cell(15, 5, $au2 . '/' . $ta2, 1, 0, 'C', $t);

        if ($sem1 == 'Si') {
            $pdf->Cell(15, 5, $row->sem1, 1, 0, 'C', $t);
        } else {
            $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        }

        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);

        $pdf->Cell(15, 5, $row->nota3, 1, 0, 'C', $t);
        $pdf->Cell(15, 5, $au3 . '/' . $ta3, 1, 0, 'C', $t);

        $pdf->Cell(15, 5, $row->nota4, 1, 0, 'C', $t);
        $pdf->Cell(15, 5, $au4 . '/' . $ta4, 1, 0, 'C', $t);
        if ($sem2 == 'Si') {
            $pdf->Cell(15, 5, $row->sem2, 1, 0, 'C', $t);
        } else {
            $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        }
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);

        if ($prof == 'Si') {
            $pdf->Cell(15, 5, $row->final, 1, 0, 'C', $t);
        } else {
            $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        }

        $cr1 = '';
        if ($ccr == 'Si') {
            $cr1 = $row->credito;
        }
        $pdf->Cell(15, 5, $cr1, 1, 1, 'R', $t);
        $pdf->Cell(60, 5, '     ' . $row->profesor, 1, 0, 'L', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);

        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'C', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'L', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'L', $t);
        $pdf->Cell(15, 5, '', 1, 0, 'L', $t);
        $pdf->Cell(15, 5, '', 1, 1, 'L', $t);
    }
    $pdf->Fill();
    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(60, 5, $pq, 1, 0, 'R', true);
    if ($cr > 0) {
        $pdf->Cell(15, 5, round($notas / $cr, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }
    $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    if ($cr2 > 0) {
        $pdf->Cell(15, 5, round($notas2 / $cr2, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }
    $pdf->Cell(15, 5, '', 1, 0, 'C', true);

    $notas7 = 0;
    $cr7 = 0;
    if ($cr5 > 0 and $sem1 == 'Si') {
        $notas7 = $notas7 + round($notas5 / $cr5, 0);
        $cr7 = $cr7 + 1;
        $pdf->Cell(15, 5, round($notas5 / $cr5, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }
    $pdf->Cell(15, 5, '', 1, 0, 'C', true);

    if ($cr3 > 0) {
        $pdf->Cell(15, 5, round($notas3 / $cr3, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }
    $pdf->Cell(15, 5, '', 1, 0, 'C', true);

    if ($cr4 > 0) {
        $pdf->Cell(15, 5, round($notas4 / $cr4, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }
    $pdf->Cell(15, 5, '', 1, 0, 'C', true);

    if ($cr6 > 0 and $sem2 == 'Si') {
        $notas7 = $notas7 + round($notas6 / $cr6, 0);
        $cr7 = $cr7 + 1;
        $pdf->Cell(15, 5, round($notas6 / $cr6, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }

    $pdf->Cell(15, 5, '', 1, 0, 'C', true);

    if ($prof == 'Si' and $cr7 > 0) {

        $pdf->Cell(15, 5, round($notas7 / $cr7, 0), 1, 0, 'C', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
    }

    if ($cr == 'Si') {
        $pdf->Cell(15, 5, number_format($cr, 2, '.', ''), 1, 1, 'R', true);
    } else {
        $pdf->Cell(15, 5, '', 1, 1, 'R', true);
    }
    $pdf->SetFont('Times', '', 9);

    if ($colegio->asis == 'B') {

        $result7 = DB::table('asispp')->where([
            ['ss', $estu->ss],
            ['year', $year]
        ])->orderBy('fecha')->get();


        //    $au = 0;
        //    $ta = 0;
        //    $au2 = 0;
        //    $ta2 = 0;
        //    $au3 = 0;
        //    $ta3 = 0;
        //    $au4 = 0;
        //    $ta4 = 0;
        foreach ($result7 as $row7) {

            if ($row7->codigo < 8 and $row7->fecha >= $colegio->asis1 and $row7->fecha <= $colegio->asis2) {
                $au = $au + 1;
            }
            if ($row7->codigo > 7 and $row7->fecha >= $colegio->asis1 and $row7->fecha <= $colegio->asis2) {
                $ta = $ta + 1;
            }

            if ($row7->codigo < 8 and $row7->fecha >= $colegio->asis3 and $row7->fecha <= $colegio->asis4) {
                $au2 = $au2 + 1;
            }
            if ($row7->codigo > 7 and $row7->fecha >= $colegio->asis3 and $row7->fecha <= $colegio->asis4) {
                $au2 = $au2 + 1;
            }
            if ($row7->codigo < 8 and $row7->fecha >= $colegio->asis5 and $row7->fecha <= $colegio->asis6) {
                $au3 = $au3 + 1;
            }
            if ($row7->codigo > 7 and $row7->fecha >= $colegio->asis5 and $row7->fecha <= $colegio->asis6) {
                $ta3 = $ta3 + 1;
            }

            if ($row7->codigo < 8 and $row7->fecha >= $colegio->asis7 and $row7->fecha <= $colegio->asis8) {
                $au4 = $au4 + 1;
            }
            if ($row7->codigo > 7 and $row7->fecha >= $colegio->asis7 and $row7->fecha <= $colegio->asis8) {
                $ta4 = $ta4 + 1;
            }
        }
    }

    $pdf->Cell(1, 5, '', 0, 1, 'R');
}

$pdf->Output();
?>