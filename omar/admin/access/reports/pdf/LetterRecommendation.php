<?php
require_once '../../../../app.php';

use Classes\Session;
use Classes\Server;
use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Server::is_post();
Session::is_logged();

$school = new School();
$studentClass = new Student();

$year = $school->year();
$colegio = $school->info('colegio');


$lang = new Lang([
    ["Carta de recomendación", "Letter of recommendation"],
    ['A quien pueda interesar: ', 'To whom it May concern:'],
    ['Fecha: ', 'Date: '],
    ['Por este medio se certifica que el/la estudiante ', 'It is hereby certified that the student '],
    [' grado.', ' grade.'],
    ['cursó estudios en nuestro ' . $colegio . ' durante el Año Escolar en el ', 'studied at our ' . $colegio . ' during the School Year in the '],
    ['Ha observado una conducta satisfactoria y por tal motivo le recomendamos para continuar estudios ', 'You have observed satisfactory behavior and for this reason we recommend you to continue studies '],
    ['en otro colegio o escuela.', 'in another college or school.'],
    ['Cordialmente,', 'Cordially,'],
    ['Director(a)', 'Director'],
    ['Edad: ', 'Age: '],
    ['Masculino', 'Male'],
    ['Femenina', 'Female'],
    ['Cantidad en estudiantes en la instituci&#65533;n: ', 'Number of students at the institution:'],
    ['Firma: ', 'Signature: '],
    ['Padre, Madre o encargado', 'Father, Mother or guardian'],



]);


$pdf = new PDF();
$pdf->SetTitle($lang->translation("Carta de recomendación") . " $year", true);
$pdf->Fill();
$students = $_POST['students'];
foreach ($students as $ss) {
    $pdf->AddPage();
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Carta de recomendación") . " $year", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    $pdf->Ln(5);

    $pdf->Ln(10);
    $genero = '';
    $students = $studentClass->findBySs($ss);
    $parent = new Parents($students->id);
    if ($students->genero == 'M') {
        $genero = $lang->translation("Masculino");
    }
    if ($students->genero == 'F') {
        $genero = $lang->translation("Femenina");
    }

    $dia = date(j);
    $mes = date(n);
    $ano = date(Y);
    $fec = $students->fecha;
    list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
    if (($mesnaz == $mes) && ($dianaz > $dia)) {
        $ano = ($ano - 1);
    }
    if ($mesnaz > $mes) {
        $ano = ($ano - 1);
    }
    $edad = $ano - $anonaz;
    if ($edad > 20) {
        $edad = '';
    }

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(20, 5, $lang->translation("fecha: "), 0, 0, 'L');
    $pdf->Cell(20, 5, date('m/d/Y'), 'B', 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(20, 5, $lang->translation("A quien pueda interesar: "), 0, 1, 'L');
    $pdf->Ln(10);

    $pdf->Cell(70, 5, $lang->translation("Por este medio se certifica que el/la estudiante "), 0, 0, 'L');
    $pdf->Cell(70, 5, $students->apellidos . ' ' . $students->nombre, 'B', 1, 'L');

    $pdf->Cell(100, 5, $lang->translation("cursó estudios en nuestro " . $colegio . " durante el Año Escolar en el ") . $students->grado . $lang->translation(" grado."), 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(100, 5, $lang->translation("Ha observado una conducta satisfactoria y por tal motivo le recomendamos para continuar estudios "), 0, 1, 'L');
    $pdf->Cell(100, 5, $lang->translation("en otro colegio o escuela."), 0, 1, 'L');
    $pdf->Ln(15);
    $pdf->Cell(50, 5, $lang->translation("Cordialmente,"), 0, 1, 'L');
    $pdf->Ln(10);
    $pdf->Cell(50, 5, $lang->translation("Director(a)"), 0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Ln(10);
}



$pdf->Output();
