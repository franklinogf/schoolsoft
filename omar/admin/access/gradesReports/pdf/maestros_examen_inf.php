<?php
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
    ['Calificaciones de los exámenes', 'Exams Grades'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Trimestre', 'Quarter'],
    ['Promedio', 'Average'],
    ['Nota B', 'Note B'],
    ['Nota C', 'Note C'],
    ['Nota D', 'Note D'],
    ['Nota F', 'Note F'],
    ['Otros', 'Other'],
    ['Total', 'Total'],
    ['P-C', 'QZ'],
    ['Pagina ', 'Page '],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Notas', 'Grades'],
    ['Pruebas-Cortas', 'Quiz'],
    ['Trab-Diarios', 'Daily Homework'],
    ['Trab-Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Trimestre-1', 'Quarter-1'],
    ['Trimestre-2', 'Quarter-2'],
    ['Trimestre-3', 'Quarter-3'],
    ['Trimestre-4', 'Quarter-4'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['Fecha', 'Date'],
    ['Titulo', 'Title'],
    ['Final', 'Final'],

]);
$school = new School(Session::id());
$year = $school->info('year2');
$cuatri = $_POST['cuatrimestre'];
if ($cuatri == 'Trimestre-1') {
    $num = 0;
} elseif ($cuatri == 'Trimestre-2') {
    $num = 10;
}
if ($cuatri == 'Trimestre-3') {
    $num = 20;
}
if ($cuatri == 'Trimestre-4') {
    $num = 30;
}
$pagina = $_POST['pagina'];
if ($pagina == 'Notas') {
    $tri = 'padres';
    $cant = 9;
} else {
    if ($pagina == 'Pruebas-Cortas') {
        $tri = 'padres2';
    } elseif ($pagina == 'Trab-Diarios') {
        $tri = 'padres4';
    } elseif ($pagina == 'Trab-Libreta') {
        $tri = 'padres3';
    }
    $cant = 10;
}
//class PDF extends FPDF
class nPDF extends PDF
{
    private $curso = '';
    function setCurso($curso)
    {
        $this->curso = $curso;
    }
    private $info = '';
    function setInfo($info)
    {
        $this->info = $info;
    }

    //Cabecera de pagina

    function Header()
    {
        parent::header();

        global $lang;
        global $year;
        global $pagina;
        global $cuatri;
        global $profe;
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 5, utf8_encode($lang->translation("Calificaciones de los exámenes")) . " $year", 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 12);
        $this->splitCells($lang->translation("$profe "), $lang->translation($cuatri) . ' - ' . $lang->translation($pagina));
        $this->Cell(15, 7, "", 1, 0, "C", true);
        $this->Cell(115, 7, $lang->translation("Titulo"), 1, 0, "C", true);
        $this->Cell(25, 7, $lang->translation("Fecha"), 1, 0, "C", true);
        $this->Cell(15, 7, "A", 1, 0, "C", true);
        $this->Cell(15, 7, "B", 1, 0, "C", true);
        $this->Cell(15, 7, "C", 1, 0, "C", true);
        $this->Cell(15, 7, "D", 1, 0, "C", true);
        $this->Cell(15, 7, "F", 1, 0, "C", true);
        $this->Cell(15, 7, $lang->translation("Otros"), 1, 0, "C", true);
        $this->Cell(15, 7, "Total", 1, 0, "C", true);
        $this->Cell(15, 7, "70%", 1, 0, "C", true);
        $this->Ln();
    }
    function Footer()
    {
        global $lang;
        $footer = $lang->translation('Pagina ') . $this->PageNo() . ' de {nb} ' . ' | ' . date("m-d-Y");
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Numero de pagina
        $this->Cell(0, 10, $footer, 0, 0, 'C');
    }
}

$re1 = DB::table('profesor')->where([
    ['activo', 'Activo'],
    ['docente', 'Docente']
])->orderBy('apellidos, nombre')->get();

$pdf = new nPDF();
$pdf->SetTitle(utf8_encode($lang->translation("Calificaciones de los exámenes")) . " $year", true);
$pdf->Fill();
$pdf->AliasNbPages();
foreach ($re1 as $re) {
    $count = 0;
    $profe = "$re->nombre $re->apellidos";
    $AA = 0;
    $BB = 0;
    $CC = 0;
    $DD = 0;
    $FF = 0;
    $OO = 0;
    $ABC = 0;
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetFont('Arial', '', 10);
    $result = DB::table('cursos')->where([
        ['year', $year],
        ['id', $re->id]
    ])->orderBy('curso')->get();
    foreach ($result as $cur) {
        $pdf->Cell(150, 7, utf8_decode("($cur->curso - $cur->desc1 $year)"), 0, 1, "L");
        $row = DB::table('valores')->whereRaw("year='$year' and curso='$cur->curso' and trimestre='$cuatri' and nivel='$pagina'")->first();
        $count = $count + 1;
        $num1 = $num;
        for ($i = 1; $i <= $cant; $i++) {

            $A = 0;
            $B = 0;
            $C = 0;
            $D = 0;
            $F = 0;
            $O = 0;
            $num1 = $num1 + 1;
            $rs = DB::table('padres')->select("not$num1 AS puntos")->whereRaw("year='$year' and curso='$cur->curso'")->get();

            foreach ($rs as $e) {
                $puntos = '';
                $punto = $e->puntos ?? '';
                if ($row->{"val$i"} ?? '' <= 100 and $row->{"val$i"} ?? '' > 0 and $punto != '') {
                    $puntos = ($punto / $row->{"val$i"}) * 100;
                }

                if (is_numeric($puntos) && $puntos != '') {
                    if ($puntos >= 90) {
                        $A++;
                    } elseif ($puntos >= 80 && $puntos <= 89.99) {
                        $B++;
                    } elseif ($puntos >= 70 && $puntos <= 79.99) {
                        $C++;
                    } elseif ($puntos >= 60 && $puntos <= 69.99) {
                        $D++;
                    } elseif ($puntos <= 59.99 && $puntos > 0) {
                        $F++;
                    }
                } else {
                    $O++;
                }
            }
            if ($A + $B + $C + $D + $F > 0) {
                $pdf->Cell(15, 5, $i, 1, 0, "C");
                $pdf->Cell(115, 5, $row->{"tema$i"}, 1);
                $pdf->Cell(25, 5, $row->{"fec$i"}, 1);
                $pdf->Cell(15, 5, $A, 1, 0, "C");
                $pdf->Cell(15, 5, $B, 1, 0, "C");
                $pdf->Cell(15, 5, $C, 1, 0, "C");
                $pdf->Cell(15, 5, $D, 1, 0, "C");
                $pdf->Cell(15, 5, $F, 1, 0, "C");
                $pdf->Cell(15, 5, $O, 1, 0, "C");
                $P1 = $A + $B + $C + $D + $F;
                $P2 = $A + $B + $C;
                $por = 0;
                if ($P1 > 0) {
                    $por = ($P2 / $P1) * 100;
                }
                $pdf->Cell(15, 5, $A + $B + $C + $D + $F, 1, 0, "C", true);
                $pdf->Cell(15, 5, number_format($por, 2), 1, 1, "C");
                $ABC += $A + $B + $C + $D + $F;
                $AA += $A;
                $BB += $B;
                $CC += $C;
                $DD += $D;
                $FF += $F;
                $OO += $O;
            }
        }
    }

    $P1 = $AA + $BB + $CC + $DD + $FF;
    $P2 = $AA + $BB + $CC;
    $por = 0;
    if ($P1 > 0) {
        $por = ($P2 / $P1) * 100;
    }

    $por = 0;
    if ($P1 > 0) {
        $por = ($P2 / $P1) * 100;
    }
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(15, 5, '', 0, 0, "C");
    $pdf->Cell(115, 5, '');
    $pdf->Cell(25, 5, 'Total', 1, 0, 'R', true);
    $pdf->Cell(15, 5, $AA, 1, 0, "C", true);
    $pdf->Cell(15, 5, $BB, 1, 0, "C", true);
    $pdf->Cell(15, 5, $CC, 1, 0, "C", true);
    $pdf->Cell(15, 5, $DD, 1, 0, "C", true);
    $pdf->Cell(15, 5, $FF, 1, 0, "C", true);
    $pdf->Cell(15, 5, $OO, 1, 0, "C", true);
    $pdf->Cell(15, 5, $ABC ?? '', 1, 0, "C", true);
    $pdf->Cell(15, 5, number_format($por, 2), 1, 1, "C", true);
}
$pdf->Output();
