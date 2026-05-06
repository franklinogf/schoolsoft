<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;

Session::is_logged();

$students = new Student();
$year1 = $students->info('year');
$year2 = (($year1[0] . $year1[1]) + 1) . '-' . (($year1[3] . $year1[4]) + 1);

$students = DB::table('year')->where([
         ['year', $year1],
         ['grado', 'not like', '12-%']
     ])->orderBy('id')->get();;

$lang = new Lang([
    ['Lista de estudiantes no matriculados', 'List of students without enrollment'],
    ['Lista de estudiantes no matriculados del grado', 'List of students without enrollment in grade'],
    ['Nombre del estudiante', 'Student name'],
    ['Si V / NO V', 'Yes V / No V'],
    ['Teléfonos', 'Telephones'],
    ['Comentario', 'Comment'],
]);

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes no matriculados"));
$pdf->Fill();

    $pdf->addPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, $lang->translation("Lista de estudiantes no matriculados por cuentas") . " $year1", 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(7, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'CUENTA', 1, 0, 'C', true);
    $pdf->Cell(64, 5, $lang->translation("Nombre del estudiante"), 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'GRADO', 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Si V / NO V"), 1, 0, 'C', true);
    $pdf->Cell(46, 5, utf8_encode($lang->translation("Teléfonos")), 1, 0, 'C', true);
    $pdf->Cell(30, 5, $lang->translation("Comentario"), 1, 1, 'C', true);

$index = 0;

foreach ($students as $student) {
    $pdf->SetFont('Arial', '', 8);
        $mother = new Parents($student->id);

        $data = DB::table('year')->where([
            ['year', $year2],
            ['ss', $student->ss]
        ])->first();
        $si = $data->nombre ?? 'no';
        if ($si == 'no')
           {
           $index = $index + 1;
        $pdf->Cell(7, 5, $index, 1, 0, 'C');
        $pdf->Cell(14, 5, $student->id, 1);
        $pdf->Cell(64, 5, strtoupper("$student->apellidos $student->nombre"), 1);
        $pdf->Cell(14, 5, $student->grado, 1, 0, 'C');
        $pdf->Cell(21, 5, "[   ] / [   ]", 1, 0, 'C');
        $phones = '';
        if ($mother->tel_m !== '') {
            $phones = "M: $mother->tel_m";
        }
        if ($mother->tel_p !== '') {
            $phones .= " P: $mother->tel_p";
        }
        $pdf->Cell(46, 5, $phones, 1, 0, 'C');
        $pdf->Cell(30, 5, "", 1, 1, 'C');
    }
}

$pdf->Output();
