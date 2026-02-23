<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ["Lista de totales por grado", "List of totals by grade"],
    ['Maestro(a)', 'Teacher'],
    ['Nuevos', 'New'],
    ['Grado', 'Grade'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
    ['Total de estudiantes', 'Total students'],
    ['Sin Géneros', 'Without genders'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$studentClass = new Student();
$a = 0;
$singeneros=0;
$allGrades = $school->allGrades();

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Proyecciones por grado") . " $year", true);
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Proyecciones por grado") . " $year", 0, 1, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 5, '', 1, 0, 'C', true);
$pdf->Cell(65, 5, $lang->translation("Maestro(a)"), 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Nuevos"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
$pdf->Cell(15, 5, "Total", 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Grado"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);
$genderCountByGrade = $totalGenderCountByGrade = [];
$totalGenderCountByGrade['N'] = 0;
$totalGenderCountByGrade['M'] = 0;
$totalGenderCountByGrade['F'] = 0;

$totalGenderCountByGrade['students'] = 0;
foreach ($allGrades as $count => $grade) {
    $students = DB::table('year')->where([
        ['codigobaja', 0],
        ['grado', $grade],
        ['year', $year]
    ])->orderBy('id')->get();
    
    $pdf->Cell(15, 5, $count + 1, 1, 0, 'C');

    $nom = $teacher->nombre ?? '';
    $ape = $teacher->apellidos ?? '';

//    $pdf->Cell(65, 5, "$nom $ape", 1);
    $pdf->Cell(65, 5, "", 1);
    $totalGenderCountByGrade['students'] += sizeof($students);
    $genderCountByGrade[$grade]['N'] = 0;
    $genderCountByGrade[$grade]['F'] = 0;
    $genderCountByGrade[$grade]['M'] = 0;
    $genderCountByGrade[$grade]['N'] = 0;
    $genderCountByGrade[$grade]['T'] = 0;

    foreach ($students as $count => $student) {
        $gender = Util::gender($student->genero);
        if ($student->nuevo === 'Si') {
            $genderCountByGrade[$grade]['N']++;
            $totalGenderCountByGrade['N']++;
        }
        if ($gender != '') {
           $genderCountByGrade[$grade][$gender]++;
           $totalGenderCountByGrade[$gender]++;
           $genderCountByGrade[$grade]['T']++;
           }
        else
           {$singeneros++;}
    }
    $pdf->Cell(20, 5, $genderCountByGrade[$grade]['N'], 1, 0, 'C');
    $pdf->Cell(25, 5, $genderCountByGrade[$grade]['F'], 1, 0, 'C');
    $pdf->Cell(25, 5, $genderCountByGrade[$grade]['M'], 1, 0, 'C');
    $pdf->Cell(15, 5, $genderCountByGrade[$grade]['T'], 1, 0, 'C');
    $pdf->Cell(20, 5, $grade, 1, 1, 'C');
}
$pdf->Ln(2);
$pdf->Cell(40, 5, $lang->translation("Total de estudiantes"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $totalGenderCountByGrade['students'], 1, 1, 'C');
$pdf->Cell(40, 5, $lang->translation("Nuevos"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $totalGenderCountByGrade['N'], 1, 1, 'C');
$pdf->Cell(40, 5, $lang->translation("Masculinos"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $totalGenderCountByGrade['F'], 1, 1, 'C');
$pdf->Cell(40, 5, $lang->translation("Femeninas"), 1, 0, 'C', true);
$pdf->Cell(25, 5, $totalGenderCountByGrade['M'], 1, 1, 'C');
$pdf->Cell(40, 5, utf8_encode($lang->translation("Sin Géneros")), 1, 0, 'C', true);
$pdf->Cell(25, 5, $singeneros, 1, 1, 'C');

$pdf->Output();
