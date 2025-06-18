<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Informe Acomodo Razonable", "Reasonable Accommodation Report"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Impedimentos/Condisiones', 'Impediments/Conditions'],
    ['(T1, T2, T3, Ect)', '(Q1, Q2, Q3, Ect)'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['TRASLADOS', 'TRANSFERS'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['REMATRICULADOS', 'RE-ENROLLED
'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$grado = '';

$allGrades = DB::table('year')->select("DISTINCT grado")->where([
    ['acomodo', 'Si'],
    ['year', $year]
])->orderBy('grado')->get();

class nPDF extends PDF
{
    function setGrado($dat)
    {
        $this->grado = $dat;
    }

    function Header()
    {
        global $lang;
        global $year;
        global $grado;
        parent::header();

        $sp = 80;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'Documento Acomodo Razonable ' . $year, 0, 0, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', 'B', 11);
        $this->Ln(10);
        $this->Cell(0, 5, "GRADO: " . $grado, 0, 1);
        $this->Cell(20, 5, 'Cta.', 1, 0, 'C', true);
        $this->Cell(70, 5, 'Apellidos/Nombre', 1, 0, 'C', true);
        $this->Cell(50, 5, 'Acomodo Razonable', 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_encode('Trajo Evaluación'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Documento Acomodo Razonable") . " $year", true);
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);

foreach ($allGrades as $grade) {
    $grado = $grade->grado;

    $pdf->setGrado($grade->grado);
    $pdf->AddPage();

    $estudents = DB::table('year')->where([
        ['year', $year],
        ['grado', $grade->grado],
        ['activo', ''],
        ['acomodo', 'Si']
    ])->orderBy('Apellidos')->get();

    foreach ($estudents as $estu) {
        $pdf->Cell(20, 5, $estu->id, 'TRLB', 0, 'C');
        $pdf->Cell(70, 5, $estu->apellidos . ' ' . $estu->nombre, 'TRLB');
        $pdf->Cell(50, 5, 'Si', 'TRLB', 0, 'C');
        if ($estu->trajo != 'Si') {
            $pdf->SetFont('Times', 'B', 11);
            $pdf->Cell(50, 5, 'NO ENTREGADO', 1, 1, 'C');
            $pdf->SetFont('Times', '', 11);
        } else {
            if ($estu->trajo == "Si") {
                $pdf->Cell(50, 5, 'SI ENTREGADO', 'LRTB', 1, 'C');
            }
        }
    }
}

$pdf->Output();
