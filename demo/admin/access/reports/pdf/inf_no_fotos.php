<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Lista de tel&#65533;fonos", "Phone list"],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Casa', 'Home'],
    ['Celular', 'Cell Phone'],
    ['Trabajo', 'Job'],
    ['Padre: ', 'Father: '],
    ['Madre: ', 'Mother: '],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$grade = '';
$teacherClass = new Teacher();
$studentClass = new Student();

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grade;
        parent::header();
        $sp = 80;
        $this->Cell($sp);
        $this->Cell(30, 5, 'Listado Fotos NO Autorizadas ' . $year, 0, 0, 'C');

        $this->SetFont('Arial', 'B', 11);
        $this->Ln(15);
        $this->Cell(0, 5, "GRADO: " . $grade, 0, 1);
        $this->Cell(20, 5, 'Cta.', 1, 0, 'C', true);
        $this->Cell(50, 5, 'Apellidos', 1, 0, 'C', true);
        $this->Cell(50, 5, 'Nombre', 1, 0, 'C', true);
        $this->Cell(70, 5, 'Comentarios', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Listado Fotos NO Autorizadas") . " $year", true);
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);

$result = DB::table('year')->select("DISTINCT grado")->where([
    ['pfoto', 'N'],
    ['year', $year]
])->orderBy('grado')->get();

foreach ($result as $row) {

    $grade = $row->grado;
    $pdf->AddPage();

    $rs = DB::table('year')->where([
        ['grado', $row->grado],
        ['activo', ''],
        ['pfoto', 'N'],
        ['year', $year]
    ])->orderBy('apellidos')->get();

    foreach ($rs as $estu) {
        $pdf->SetFont('Times', '', 11);
        $pdf->Cell(20, 5, $estu->id, 'TRLB', 0, 'C');
        $pdf->Cell(50, 5, $estu->apellidos, 'TRLB');
        $pdf->Cell(50, 5, $estu->nombre, 'TRLB');
        $pdf->Cell(70, 5, '', 'B', 1, 'C');
    }
}
$pdf->Output();
