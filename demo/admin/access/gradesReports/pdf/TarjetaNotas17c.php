<?php
// colegio bautista de gurabo
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


class nPDF extends PDF
{
    function Header()
    {
        parent::header();
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, 'TARJETA DE PROGRESO', 0, 0, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-70);
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
    $pdf->Ln(10);
    $a = $a + 1;
    $pdf->Cell(1, 1, '', 0, 1, 'L');
    $gra = '';
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(23, 5, 'Nombre:', 0, 0, 'L');
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(50, 5, $estu->apellidos . ' ' . $estu->nombre, 0, 0, 'L');
    $pdf->Cell(50, 5, ' ', 0, 0, 'L');
    $pdf->Cell(85, 5, '', 0, 0, 'L');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(15, 5, 'Fecha:', 0, 0, 'L');
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(20, 5, date('m/d/Y'), 0, 1, 'L');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(23, 5, 'Maestro(a):', 0, 0, 'L');
    $pdf->SetFont('Times', '', 12);
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');
    $pdf->Cell(50, 5, $nom . ' ' . $ape, 0, 1, 'L');
    $pdf->Cell(85, 5, '', 0, 0, 'C');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(15, 5, 'Grado:', 0, 0, 'L');
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(10, 5, $grade, 0, 1, 'L');


    //     $pdf->SetFillColor(230);

    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(63, 4, utf8_encode('DESARROLLO DE EVALUACIÓN'), 1, 0, 'C', true);
    $pdf->Cell(16, 4, 'Dic', 1, 0, 'C', true);
    $pdf->Cell(16, 4, 'May', 1, 1, 'C', true);

    $b1 = 63;
    $b2 = 15;
    $b3 = 20;
    $c2 = 0;
    $au2 = 0;
    $ta2 = 0;
    $au4 = 0;
    $ta4 = 0;

    $a = $a + 1;
    $cant = 0;

    $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();


    foreach ($cursos as $curso) {
        list($cur1, $cur2) = explode("-", $curso->curso . '-');

        if (substr($cur1, -2) < 73) {
            $cant++;
            if ($c2 == 1) {
                $pdf->Cell(97, 5, '', 0, 0);
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


            if ($cur1 == 'KG39') {
                $c2 = 1;
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 21);
                $pdf->Cell(63, 4, '', 1, 0, 'L', true);
                $pdf->Cell(16, 4, 'Dic', 1, 0, 'C', true);
                $pdf->Cell(16, 4, 'May', 1, 1, 'C', true);
                $pdf->Cell(97, 4, '', 0, 0);
            }



            $pdf->SetFont('Times', '', 9);

            if ($cur1 === 0) {
                $y = $pdf->GetY();

                $pdf->MultiCell($b1, 3, $curso->descripcion, 1);
                $pdf->SetXY(170, $y);
            } else {
                $pdf->Cell($b1, 4, $curso->descripcion, 1, 0);
            }

            if ($cur1 === 0) {
                $pdf->Cell(8, 6, $curso->not11, 1, 0, 'C');
                $pdf->Cell(8, 6, $curso->not12, 1, 0, 'C');
                $pdf->Cell(8, 6, $curso->not31, 1, 0, 'C');
                $pdf->Cell(8, 6, $curso->not32, 1, 1, 'C');
            } else {
                $pdf->Cell(8, 4, $curso->not11, 1, 0, 'C');
                $pdf->Cell(8, 4, $curso->not12, 1, 0, 'C');
                $pdf->Cell(8, 4, $curso->not31, 1, 0, 'C');
                $pdf->Cell(8, 4, $curso->not32, 1, 1, 'C');
            }

            if ($cur1 == 'KG38') {
                $c2 = 1;
                /*  $pdf->Cell($b2,5,'',0,1,'C');
     $pdf->SetFont('Times','B',10);  */
                $pdf->SetFont('Times', '', 11);
                $pdf->Cell(97, 5, '', 0, 1, 'C');
                $pdf->Cell(50, 5, 'ASISTENCIA', 1, 0, 'L', true);
                $pdf->Cell(15, 5, 'Dic', 1, 0, 'C', true);
                $pdf->Cell(15, 5, 'May', 1, 0, 'C', true);
                $pdf->Cell(15, 5, 'Total', 1, 1, 'C', true);
                //     $pdf->Cell(97,5,'',0,0,'C');
                $pdf->Cell(50, 5, 'Ausencias', 1, 0, 'L');
                $pdf->Cell(15, 5, $au2, 1, 0, 'C');
                $pdf->Cell(15, 5, $au4, 1, 0, 'C');
                $pdf->Cell(15, 5, $au2 + $au4, 1, 1, 'C');
                $pdf->Cell(50, 5, 'Tardanzas', 1, 0, 'L');
                $pdf->Cell(15, 5, $ta2, 1, 0, 'C');
                $pdf->Cell(15, 5, $ta4, 1, 0, 'C');
                $pdf->Cell(15, 5, $ta2 + $ta4, 1, 1, 'C');

                $pdf->Cell(10, 5, '', 0, 1, 'C');
                $pdf->Cell(10, 6, '', 0, 0, 'C');
                $pdf->Cell(65, 40, utf8_encode('Sello Institución'), 1, 1, 'C');

                $pdf->Sety(75);
            }

            if ($cur1 == 'KG72') {

                $pdf->Ln(3);
                $pdf->Cell(97);
                $pdf->Cell(95, 5, 'Leyenda', 1, 1, 'C', true);
                $pdf->Cell(97);
                $pdf->SetFont('Times', '', 8.5);
                $pdf->Cell(95, 5, 'L = LOGRADO = Cumple con los requisitos según las expectativas del grado.', 0, 1, 'L');
                $pdf->Cell(97);
                $pdf->SetFont('Times', '', 8);

                $pdf->Cell(95, 5, utf8_encode('NL = NO LOGRADO = No cumple con los requisitos según las expectativas del grado.'), 0, 1, 'L');
                $pdf->SetFont('Times', '', 8.5);

                $pdf->Cell(97);
                $pdf->Cell(95, 5, 'EP = EN PROCESO = Necesita trabajar en el desarrollo de la expectativa.', 0, 1, 'L');
                $pdf->Cell(97);
                $pdf->Cell(95, 5, 'I = INICIADO = Se encuentra en el inicio de adquirir el desarrollo.', 0, 1, 'L');
                $pdf->Cell(97);
                $pdf->Cell(95, 5, 'NE = NO EVALUADO = No suministrada.', 0, 1, 'L');
            }
        }
    }
}
$pdf->Output();
