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
    ["Informe de raza y religión", "Race and Religion Report"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["NOMBRE DEL ESTUDIANTE", "STUDENT NAME"],
    ['Cuenta', 'Account'],
    ['GENERO', 'GENDER'],
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
        //    $this->SetFont('Arial', 'B', 15);
        //    $this->Cell(0, 5, $lang->translation("Lista de tel&#65533;fonos") . " $year", 0, 1, 'C');
        //    $this->Ln(5);
        //    $this->SetFont('Arial', 'B', 12);
        //    $this->splitCells($lang->translation("Maestro(a):") . ' '.utf8_decode($teacher2), $lang->translation("Grado:") . " $grade");
        //    $this->SetX(5);
        //    $this->SetFont('Arial', 'B', 10);
        //    $this->Cell(5, 5, '', 0, 0 );
        //    $this->Cell(10, 5, '', 1, 0, 'C', true);
        //    $this->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        //    $this->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        //    $this->Cell(25, 5, $lang->translation("Casa"), 1, 0, 'C', true);
        //    $this->Cell(25, 5, $lang->translation("Celular"), 1, 0, 'C', true);
        //    $this->Cell(25, 5, $lang->translation("Trabajo"), 1, 1, 'C', true);    
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




//$pdf = new PDF();
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Informe de raza y religión") . " $year", true);
$pdf->Fill();

foreach ($allGrades as $grade) {
    $teacher = $teacherClass->findByGrade($grade);
    $students = $studentClass->findByGrade($grade);
    $nom = $teacher->nombre ?? '';
    $nom2 = $teacher->apellidos ?? '';
    $teacher2 = $nom . ' ' . $nom2;

    $genderCount = ['M' => 0, 'F' => 0, 'T' => 0];
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Informe de raza y religión") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(20, 5, $lang->translation("Grado:"), 1, 0, 'L', true);
    $pdf->Cell(20, 5, $grade, 0, 0, 'L');
    $pdf->Cell(35, 5, '', 0, 0, 'L');
    $pdf->Cell(25, 5, $lang->translation("Maestro(a):"), 1, 0, 'L', true);
    $pdf->Cell(85, 5, $teacher2, 0, 1, 'L');
    $pdf->Cell(10, 5, '', 0, 1, 'C');

    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->Cell(85, 33, $lang->translation("NOMBRE DEL ESTUDIANTE"), 1, 0, 'C', true);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(107, 98, 'BLANCO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(114, 98, 'NEGRO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(121, 98, 'MESTIZO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(128, 98, 'ASIATICO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(135, 98, 'INDIGENA', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(142, 98, 'OTROS', 90);

    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(149, 98, 'ADVENTISTA', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(156, 98, 'BAUTISTA', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(163, 98, 'CATOLICO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(170, 98, 'EVANGELICO', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(177, 98, 'PENTECOSTAL', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(184, 98, 'METODISTA', 90);
    $pdf->Cell(7, 33, '', 1, 0, 'C', true);
    $pdf->RotatedText(191, 98, 'MITA', 90);
    $pdf->Cell(7, 33, '', 1, 1, 'C', true);
    $pdf->RotatedText(198, 98, 'NINGUNO', 90);


    $pdf->Cell(7, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(48, 5, $lang->translation("APELLIDOS"), 1, 0, 'C', true);
    $pdf->Cell(37, 5, $lang->translation("NOMBRE"), 1, 0, 'C', true);
    $pdf->Cell(42, 5, $lang->translation("RAZA"), 1, 0, 'C', true);
    $pdf->Cell(56, 5, 'RELIGION', 1, 1, 'C', true);


    $c1 = 0;
    $c2 = 0;
    $c3 = 0;
    $c4 = 0;
    $c5 = 0;
    $c6 = 0;
    $c7 = 0;
    $c8 = 0;
    $c9 = 0;
    $x = 0;
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





    $pdf->SetFont('Arial', '', 10);

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
        $x = $x + 1;
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
        $pdf->Cell(7, 5, $g1 . $x, 1, 0, 'R');
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(48, 5, $student->apellidos, 1, 0, 'L');
        $pdf->Cell(37, 5, $student->nombre, 1, 0, 'L');
        $pdf->SetFont('Times', '', 12);
        if ($student->raza == 1) {
            $a1 = $a1 + 1;
            $b1 = '1';
        }
        $pdf->Cell(7, 5, $b1, 1, 0, 'C');
        if ($student->raza == 2) {
            $a2 = $a2 + 1;
            $b2 = '1';
        }
        $pdf->Cell(7, 5, $b2, 1, 0, 'C');
        if ($student->raza == 3) {
            $a3 = $a3 + 1;
            $b3 = '1';
        }
        $pdf->Cell(7, 5, $b3, 1, 0, 'C');
        if ($student->raza == 6) {
            $a4 = $a4 + 1;
            $b4 = '1';
        }
        $pdf->Cell(7, 5, $b4, 1, 0, 'C');
        if ($student->raza == 4) {
            $a5 = $a5 + 1;
            $b5 = '1';
        }
        $pdf->Cell(7, 5, $b5, 1, 0, 'C');
        if ($student->raza == 5 or $student->raza == 0) {
            $a6 = $a6 + 1;
            $b6 = '1';
        }
        $pdf->Cell(7, 5, $b6, 1, 0, 'C');


        if ($student->rel == 1) {
            $c1 = $c1 + 1;
            $d1 = '1';
        }
        $pdf->Cell(7, 5, $d1, 1, 0, 'C');
        if ($student->rel == 2) {
            $c2 = $c2 + 1;
            $d2 = '1';
        }
        $pdf->Cell(7, 5, $d2, 1, 0, 'C');
        if ($student->rel == 3) {
            $c3 = $c3 + 1;
            $d3 = '1';
        }
        $pdf->Cell(7, 5, $d3, 1, 0, 'C');
        if ($student->rel == 4) {
            $c4 = $c4 + 1;
            $d4 = '1';
        }
        $pdf->Cell(7, 5, $d4, 1, 0, 'C');
        if ($student->rel == 5) {
            $c5 = $c5 + 1;
            $d5 = '1';
        }
        $pdf->Cell(7, 5, $d5, 1, 0, 'C');
        if ($student->rel == 6) {
            $c6 = $c6 + 1;
            $d6 = '1';
        }
        $pdf->Cell(7, 5, $d6, 1, 0, 'C');
        if ($student->rel == 7) {
            $c7 = $c7 + 1;
            $d7 = '1';
        }
        $pdf->Cell(7, 5, $d7, 1, 0, 'C');
        if ($student->rel == 0) {
            $c8 = $c8 + 1;
            $d8 = '1';
        }
        $pdf->Cell(7, 5, $d8, 1, 1, 'C');
    }
    $pdf->Cell(55, 5, '', 0, 0, 'R');
    $pdf->Cell(37, 5, $lang->translation("TOTALES: "), 1, 0, 'R', true);
    $pdf->Cell(7, 5, $a1, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $a2, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $a3, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $a4, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $a5, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $a6, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c1, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c2, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c3, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c4, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c5, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c6, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c7, 1, 0, 'C', true);
    $pdf->Cell(7, 5, $c8, 1, 1, 'C', true);

    $pdf->Cell(55, 5, '', 0, 0, 'R');
    $pdf->Cell(37, 5, $lang->translation("PORCIENTOS: "), 1, 0, 'R', true);
    $pdf->SetFont('Times', '', 9);
    $S = '';
    if ($a1 > 0) {
        $S = round(($a1 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);

    $S = '';
    if ($a2 > 0) {
        $S = round(($a2 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($a3 > 0) {
        $S = round(($a3 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($a4 > 0) {
        $S = round(($a4 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($a5 > 0) {
        $S = round(($a5 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($a6 > 0) {
        $S = round(($a6 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);


    $S = '';
    if ($c1 > 0) {
        $S = round(($c1 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c2 > 0) {
        $S = round(($c2 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c3 > 0) {
        $S = round(($c3 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c4 > 0) {
        $S = round(($c4 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c5 > 0) {
        $S = round(($c5 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c6 > 0) {
        $S = round(($c6 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c7 > 0) {
        $S = round(($c7 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 0, 'C', true);
    $S = '';
    if ($c8 > 0) {
        $S = round(($c8 / $x) * 100, 1);
    }
    $pdf->Cell(7, 5, $S, 1, 1, 'C', true);

    $pdf->Cell(7, 5, '', 0, 1, 'C');

    $pdf->SetFont('Times', 'B', 12);

    $pdf->Cell(30, 5, $lang->translation("GENERO"), 1, 1, 'C', true);
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(7, 5, 'F', 1, 0, 'C', true);
    $pdf->Cell(7, 5, $f, 1, 0, 'C');
    $pdf->Cell(16, 5, round(($f / $x) * 100, 1), 1, 1, 'C');
    $pdf->Cell(7, 5, 'M', 1, 0, 'C', true);
    $pdf->Cell(7, 5, $m, 1, 0, 'C');
    $pdf->Cell(16, 5, round(($m / $x) * 100, 1), 1, 1, 'C');

    $pdf->Cell(7, 5, '', 0, 0, 'C');
    $pdf->Cell(7, 5, $m + $f, 1, 0, 'C');
    $pdf->Cell(16, 5, round(($tg / $x) * 100, 1), 1, 1, 'C');
}

$pdf->Output();
