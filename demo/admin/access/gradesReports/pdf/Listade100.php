<?php
require_once '../../../../app.php';

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
    ['Asistencia perfecta/tardanzaa', 'Perfect assistance/Delays'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Edad', 'Age'],
    ['Cantidad', 'Amount'],
    ['Estudiante', 'Student'],
    ['Grado', 'Grade'],
    ['Año', 'Year'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],

]);

$school = new School(Session::id());
$year = $school->info('year2');

$pdf = new PDF();
$pdf->AliasNbPages();

$grado = $_POST['grade'] ?? '';
if ($grado == 'Todos') {
    $allGrades = DB::table('year')->select("distinct grado")->where([
        ['year', $year],
    ])->orderBy('grado')->get();

    foreach ($allGrades as $gr) {
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Fill();
        $pdf->AddPage();
        $pdf->Cell(0, 5, $lang->translation('Grado') . " $gr->grado / " . utf8_decode($lang->translation('Año')) . " $year", 0, 1, 'C');
        $ESTUDIANTES = array();
        $students = DB::table('year')->select("nombre, apellidos, ss, grado")->where([
            ['year', $year],
            ['grado', $gr->grado],
        ])->orderBy('apellidos')->get();
        foreach ($students as $r) {
            $ESTUDIANTES[$r->ss] = $r;
        }

        foreach ($ESTUDIANTES as $key => $value) {
            $cant = 0;
            for ($i = 1; $i <= 6; $i++) {
                $padre = ($i == 1) ? 'padres' : "padres$i";
                for ($i = 1; $i <= 40; $i++) {

                    $re = DB::table($padre)->select("not{$i} as nota")->where([
                        ['ss', $key],
                        ['year', $year],
                            ['grado', $gr->grado],
                        ])->orderBy('curso')->get();

                    foreach ($re as $row) {
                        if ($row->nota >= 100) {
                            $cant++;
                        }
                    }
                    $ESTUDIANTES[$key]->cantidad = $cant;
                }
            }
        }
        usort($ESTUDIANTES, function ($first, $second) {
            return $first->cantidad < $second->cantidad;
        });
        $pdf->Fill();
        $pdf->Ln(10);
        $pdf->Cell(30);
        $pdf->Cell(100, 5, $lang->translation('Estudiante'), 1, 0, 'C', true);
        $pdf->Cell(30, 5, $lang->translation('Cantidad'), 1, 1, 'C', true);
        $pdf->SetFont('Times', '', 12);
        foreach ($ESTUDIANTES as $key => $value) {
            $pdf->Cell(30);
            $pdf->Cell(100, 5, $value->nombre . " " . $value->apellidos, 1);
            $pdf->Cell(30, 5, $value->cantidad, 1, 1, 'C');
        }
    }
} else {
    $ESTUDIANTES = array();
    $pdf->AddPage();
    $allGrades = DB::table('year')->select("nombre, apellidos, ss, grado")->where([
        ['year', $year],
        ['grado', $grado],
    ])->orderBy('apellidos')->get();

    foreach ($allGrades as $r) {
        $ESTUDIANTES[$r->ss] = $r;
    }
    foreach ($ESTUDIANTES as $key => $value) {
        $cant = 0;
        for ($i = 1; $i <= 6; $i++) {
            $padre = ($i == 1) ? 'padres' : "padres$i";
            for ($i = 1; $i <= 40; $i++) {
                $re = DB::table($padre)->select("not{$i} as nota")->where([
                    ['ss', $key],
                    ['year', $year],
                    ['grado', $grado],
                ])->orderBy('curso')->get();
                foreach ($re as $row) {
                    if ($row->nota >= 100) {
                        $cant++;
                    }
                }
                $ESTUDIANTES[$key]->cantidad = $cant;
            }
        }
    }

    usort($ESTUDIANTES, function ($first, $second) {
        return $first->cantidad < $second->cantidad;
    });

    $pdf->Ln(3);
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation('Grado') . " $grado / " . utf8_decode($lang->translation('A&#65533;o')) . " $year", 0, 1, 'C');
    $pdf->Fill();
    $pdf->Ln(5);
    $pdf->Cell(30);
    $pdf->Cell(100, 5, $lang->translation('Estudiante'), 1, 0, 'C', true);
    $pdf->Cell(30, 5, $lang->translation('Cantidad'), 1, 1, 'C', true);

    $pdf->SetFont('Times', '', 12);

    foreach ($ESTUDIANTES as $key => $value) {

        $pdf->Cell(30);
        $pdf->Cell(100, 5, $value->nombre . " " . $value->apellidos, 1);
        $pdf->Cell(30, 5, $value->cantidad, 1, 1, 'C');
    }

}

$pdf->Output();
?>