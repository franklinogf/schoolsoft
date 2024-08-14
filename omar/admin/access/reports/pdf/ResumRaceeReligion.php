<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Resumen de raza y religión", "Summary of race and religion"],
    ["Maestro(a):", "Teacher:"],
    ["GRADOS", "GRADE"],
    ["NOMBRE DEL ESTUDIANTE", "STUDENT NAME"],
    ['Cuenta', 'Account'],
    ['GENEROS', 'GENDER'],
    ['APELLIDOS', 'SURNAMES'],
    ['NOMBRE', 'NAME'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['RAZA', 'RACE'],
    ['TOTALES: ', 'TOTALS: '],
    ['PORCIENTOS: ', 'PER CENT: '],

]);

$school = new School();
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year');
$allGrades = $school->allGrades();

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grade;
        global $teacher2;
        parent::header();
        $this->SetFont('Arial', '', 10);
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function RotatedImage($file, $x, $y, $w, $h, $angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle, $x, $y);
        $this->Image($file, $x, $y, $w, $h);
        $this->Rotate(0);
    }

    //Pie de p&#65533;gina
    function Footer()
    {

        //Posici&oacute;n: a 1,5 cm del final
        $this->SetY(-15);

        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }

    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}




$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Resumen de raza y religión") . " $year", true);
$pdf->Fill();
$pdf->AddPage('L');

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Resumen de raza y religión") . " $year", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(10, 5, '', 0, 1, 'C');

$pdf->Cell(7, 33, '', 1, 0, 'C', true);
$pdf->Cell(30, 33, $lang->translation("GRADOS"), 1, 0, 'C', true);
$pdf->Cell(36, 33, $lang->translation("GENEROS"), 1, 0, 'C', true);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(90, 93, ' BLANCO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(102, 93, ' NEGRO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(114, 93, ' MESTIZO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(126, 93, ' ASIATICO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(138, 93, ' INDIGENA', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(150, 93, ' OTROS', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(162, 93, ' TOTAL', 90);

$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(174, 93, ' ADVENTISTA', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(186, 93, ' BAUTISTA', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(198, 93, ' CATOLICO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(210, 93, ' EVANGELICO', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(222, 93, ' PENTECOSTAL', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(234, 93, ' METODISTA', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(246, 93, ' MITA', 90);
$pdf->Cell(12, 33, '', 1, 0, 'C', true);
$pdf->RotatedText(258, 93, ' NINGUNO', 90);
$pdf->Cell(12, 33, '', 1, 1, 'C', true);
$pdf->RotatedText(270, 93, ' TOTAL', 90);


$pdf->Cell(7, 5, '#', 1, 0, 'C', true);
$pdf->Cell(30, 5, '', 1, 0, 'C', true);
$pdf->Cell(12, 5, 'F', 1, 0, 'C', true);
$pdf->Cell(12, 5, 'M', 1, 0, 'C', true);
$pdf->Cell(12, 5, 'T', 1, 0, 'C', true);
$pdf->Cell(72, 5, $lang->translation("RAZA"), 1, 0, 'C', true);
$pdf->Cell(120, 5, 'RELIGION', 1, 1, 'C', true);


$c1 = 0;
$c2 = 0;
$c3 = 0;
$c4 = 0;
$c5 = 0;
$c6 = 0;
$c7 = 0;
$c8 = 0;
$c9 = 0;
$gdo = array("KG-", "01-", "02-", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$gdo2 = array("kinder", "primero", "segundo", "tercero", "cuarto", "quinto", "sexto", "septimo", "octavo", "noveno", "decimo", "undecimo", "dudecimo");
$f = 0;
$m = 0;
$tbp = 0;
$x1 = 0;
$tf = 0;
$tm = 0;
$t1 = 0;
$t2 = 0;
$t3 = 0;
$gt = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';
    $teacher2 = $nom . ' ' . $ape;

    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];



    $pdf->SetFont('Arial', '', 10);
    $x2 = 0;
    $f = 0;
    $m = 0;
    $tg = 0;
    $tm = 0;
    $a1 = 0;
    $a2 = 0;
    $a3 = 0;
    $a4 = 0;
    $a5 = 0;
    $a6 = 0;
    $c1 = 0;
    $c2 = 0;
    $c3 = 0;
    $c4 = 0;
    $c5 = 0;
    $c6 = 0;
    $c7 = 0;
    $c8 = 0;
    $c9 = 0;

    foreach ($students as $count => $student) {
        $g1 = '*';
        if ($student->genero == '1' or $student->genero == 'F') {
            $f = $f + 1;
            $tg = $tg + 1;
            $g1 = '';
        }
        if ($student->genero == '2' or $student->genero == 'M') {
            $m = $m + 1;
            $tg = $tg + 1;
            $g1 = '';
        }
        $pdf->SetFont('Times', '', 12);
        $b1 = '';
        $b2 = '';
        $b3 = '';
        $b4 = '';
        $b5 = '';
        $b6 = '';
        $d1 = '';
        $d2 = '';
        $d3 = '';
        $d4 = '';
        $d5 = '';
        $d6 = '';
        $d7 = '';
        $d8 = '';
        if ($student->raza == 1) {
            $a1 = $a1 + 1;
            $b1 = '1';
        }
        if ($student->raza == 2) {
            $a2 = $a2 + 1;
            $b2 = '1';
        }
        if ($student->raza == 3) {
            $a3 = $a3 + 1;
            $b3 = '1';
        }
        if ($student->raza == 6) {
            $a4 = $a4 + 1;
            $b4 = '1';
        }
        if ($student->raza == 4) {
            $a5 = $a5 + 1;
            $b5 = '1';
        }
        if ($student->raza == 5 or $student->raza == 0) {
            $a6 = $a6 + 1;
            $b6 = '1';
        }

        if ($student->rel == 1) {
            $c1 = $c1 + 1;
            $d1 = '1';
        }
        if ($student->rel == 2) {
            $c2 = $c2 + 1;
            $d2 = '1';
        }
        if ($student->rel == 3) {
            $c3 = $c3 + 1;
            $d3 = '1';
        }
        if ($student->rel == 4) {
            $c4 = $c4 + 1;
            $d4 = '1';
        }
        if ($student->rel == 5) {
            $c5 = $c5 + 1;
            $d5 = '1';
        }
        if ($student->rel == 6) {
            $c6 = $c6 + 1;
            $d6 = '1';
        }
        if ($student->rel == 7) {
            $c7 = $c7 + 1;
            $d7 = '1';
        }
        if ($student->rel == 0) {
            $c8 = $c8 + 1;
            $d8 = '1';
        }
    }


    $x1 = $x1 + 1;

    $pdf->Cell(7, 5, $g1 . $x1, 1, 0, 'R');
    $pdf->Cell(30, 5, $grade, 1, 0, 'R');
    $pdf->Cell(12, 5, $f, 1, 0, 'R');
    $pdf->Cell(12, 5, $m, 1, 0, 'R');
    $pdf->Cell(12, 5, $tg, 1, 0, 'R');
    $pdf->Cell(12, 5, $a1, 1, 0, 'C');
    $pdf->Cell(12, 5, $a2, 1, 0, 'C');
    $pdf->Cell(12, 5, $a3, 1, 0, 'C');
    $pdf->Cell(12, 5, $a4, 1, 0, 'C');
    $pdf->Cell(12, 5, $a5, 1, 0, 'C');
    $pdf->Cell(12, 5, $a6, 1, 0, 'C');
    $pdf->Cell(12, 5, $a1 + $a2 + $a3 + $a4 + $a5 + $a6, 1, 0, 'C');

    $pdf->Cell(12, 5, $c1, 1, 0, 'C');
    $pdf->Cell(12, 5, $c2, 1, 0, 'C');
    $pdf->Cell(12, 5, $c3, 1, 0, 'C');
    $pdf->Cell(12, 5, $c4, 1, 0, 'C');
    $pdf->Cell(12, 5, $c5, 1, 0, 'C');
    $pdf->Cell(12, 5, $c6, 1, 0, 'C');
    $pdf->Cell(12, 5, $c7, 1, 0, 'C');
    $pdf->Cell(12, 5, $c8, 1, 0, 'C');
    $tt = $c1 + $c2 + $c3 + $c4 + $c5 + $c6 + $c7 + $c8;
    $z = '';
    if ($tg != $tt) {
        $z = '*';
    }
    $pdf->Cell(12, 5, $z . $tt, 1, 1, 'C');
    $gt[1] = $gt[1] + $f;
    $gt[2] = $gt[2] + $m;
    $gt[3] = $gt[3] + $f + $m;
    $gt[4] = $gt[4] + $a1;
    $gt[5] = $gt[5] + $a2;
    $gt[6] = $gt[6] + $a3;
    $gt[7] = $gt[7] + $a4;
    $gt[8] = $gt[8] + $a5;
    $gt[9] = $gt[9] + $a6;
    $gt[10] = $gt[10] + $a1 + $a2 + $a3 + $a4 + $a5 + $a6;

    $gt[11] = $gt[11] + $c1;
    $gt[12] = $gt[12] + $c2;
    $gt[13] = $gt[13] + $c3;
    $gt[14] = $gt[14] + $c4;
    $gt[15] = $gt[15] + $c5;
    $gt[16] = $gt[16] + $c6;
    $gt[17] = $gt[17] + $c7;
    $gt[18] = $gt[18] + $c8;
    $gt[19] = $gt[19] + $c1 + $c2 + $c3 + $c4 + $c5 + $c6 + $c7 + $c8;
}
$pdf->SetFont('Times', '', 10);

$pdf->Cell(7, 5, '', 0, 0, 'R');
$pdf->Cell(30, 5, $lang->translation("TOTALES: "), 1, 0, 'R', true);
$pdf->Cell(12, 5, $gt[1], 1, 0, 'R', true);
$pdf->Cell(12, 5, $gt[2], 1, 0, 'R', true);
$pdf->Cell(12, 5, $gt[3], 1, 0, 'R', true);
for ($x = 4; $x <= 19; $x++) {
    $pdf->Cell(12, 5, $gt[$x], 1, 0, 'C', true);
}


$pdf->SetFont('Times', '', 10);

$pdf->Cell(7, 5, '', 0, 1, 'R');
$pdf->Cell(7, 5, '', 0, 0, 'R');
$pdf->Cell(30, 5, $lang->translation("PORCIENTOS: "), 1, 0, 'R', true);
$pdf->Cell(12, 5, round(($gt[1] / $gt[3]) * 100, 2) . '%', 1, 0, 'R', true);
$pdf->Cell(12, 5, round(($gt[2] / $gt[3]) * 100, 2) . '%', 1, 0, 'R', true);
$pdf->Cell(12, 5, round(($gt[3] / $gt[3]) * 100, 2) . '%', 1, 0, 'R', true);
for ($x = 4; $x <= 19; $x++) {
    $m1 = '';
    if ($gt[$x] > 0) {
        $m1 = round(($gt[$x] / $gt[3]) * 100, 2) . '%';
    }
    $pdf->Cell(12, 5, $m1, 1, 0, 'R', true);
}

$pdf->Output();
