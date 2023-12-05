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

$lang = new Lang([
    ["Estudio Socioeconómico", "Socio-economic study"],
    ['Nombre del estudiante:', 'Student name: '],
    ['Nombre de padre o encargado: ', 'Name of parent or guardian: '],
    ['Grado: ', 'Grade: '],
    ['Fecha', 'Date'],
    ['Ocupación: ', 'Occupation: '],
    ['Género: ', 'Gender: '],
    ['Nombre de la Madre: ', "Mother's name: "],
    ['Total Ingreso Familiar: ', 'Total Family Income: '],
    ['Composición Familiar: ', 'Family composition: '],
    ['Edad: ', 'Age: '],
    ['Masculino', 'Male'],
    ['Femenina', 'Female'],
    ['Cantidad en estudiantes en la institución: ', 'Number of students at the institution:'],
    ['Firma: ', 'Signature: '],
    ['Padre, Madre o encargado', 'Father, Mother or guardian'],

    
    
]);

$school = new School();
$studentClass = new Student();

$year = $school->info('year');
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Estudio Socioeconómico") . " $year", true);
$pdf->Fill();
$students = $_POST['students'];
foreach ($students as $ss) {
    $pdf->AddPage();
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Estudio Socioeconómico") . " $year", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    $pdf->Ln(5);

    $pdf->Ln(10);
    $genero='';
    $students = $studentClass->findBySs($ss);
    $parent = new Parents($students->id);
    if ($students->genero == 'M')
       {$genero=$lang->translation("Masculino");}
    if ($students->genero == 'F')
       {$genero=$lang->translation("Femenina");}

        $dia=date(j);
        $mes=date(n);
        $ano=date(Y);
        $fec=$students->fecha; 
        list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
        if (($mesnaz == $mes) && ($dianaz > $dia)) {$ano=($ano-1);}
        if ($mesnaz > $mes) {$ano=($ano-1);}
        $edad=$ano-$anonaz;
        if ($edad > 20){$edad='';}
       
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(40, 5, $lang->translation("Nombre del estudiante:"), 0, 0, 'L');
    $pdf->Cell(100, 5, $students->apellidos.' '.$students->nombre,'B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(20, 5, $lang->translation("Grado: "), 0, 0, 'L');
    $pdf->Cell(20, 5, $students->grado,'B', 0, 'C');
    $pdf->Cell(25, 5, '',0, 0, 'L');
    $pdf->Cell(15, 5, $lang->translation("Edad: "), 0, 0, 'L');
    $pdf->Cell(20, 5, $edad,'B', 0, 'C');
    $pdf->Cell(25, 5, '',0, 0, 'L');
    $pdf->Cell(20, 5, $lang->translation("Género: "), 0, 0, 'L');
    $pdf->Cell(20, 5, $genero,'B', 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(50, 5, $lang->translation("Nombre de padre o encargado: "), 0, 0, 'L');
    $pdf->Cell(100, 5, $parent->padre,'B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 5, $lang->translation("Ocupación: "), 0, 0, 'L');
    $pdf->Cell(100, 5, $parent->posicion_p,'B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(50, 5, $lang->translation("Nombre de la Madre: "), 0, 0, 'L');
    $pdf->Cell(100, 5, $parent->madre,'B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 5, $lang->translation("Ocupación: "), 0, 0, 'L');
    $pdf->Cell(100, 5, $parent->posicion_m,'B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 5, $lang->translation("Total Ingreso Familiar: "), 0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '0 - 13,590',0, 0, 'L');
    $pdf->Cell(12, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '13,591 - 18,310',0, 0, 'L');
    $pdf->Cell(12, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '18,311 - 23,030',0, 0, 'L');
    $pdf->Cell(12, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '23,031 - 27,750',0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '27,751 - 32,470',0, 0, 'L');
    $pdf->Cell(13, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '32,471 - 37,190',0, 0, 'L');
    $pdf->Cell(13, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '37,191 - 41,910',0, 0, 'L');
    $pdf->Cell(13, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '41,911 - 46,630',0, 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(40, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '46,631 - 51,350',0, 0, 'L');
    $pdf->Cell(13, 5, '',0, 0, 'L');
    $pdf->Cell(5, 5, '',1, 0, 'L');
    $pdf->Cell(20, 5, '51,351 - +',0, 0, 'L');

    $pdf->Ln(15);
    $pdf->Cell(40, 5, $lang->translation("Composición Familiar: "), 0, 0, 'L');
    $pdf->Cell(70, 5, '','B', 1, 'L');
    $pdf->Ln(5);
    $pdf->Cell(70, 5, $lang->translation("Cantidad en estudiantes en la institución: "), 0, 0, 'L');
    $pdf->Cell(40, 5, '','B', 1, 'L');
    $pdf->Ln(15);
    $pdf->Cell(35, 5, $lang->translation("Firma: "), 0, 0, 'L');
    $pdf->Cell(75, 5, '','B', 1, 'L');
    $pdf->Cell(35, 5, '',0, 0, 'L');
    $pdf->Cell(75, 5, $lang->translation("Padre, Madre o encargado"), 0, 0, 'C');
    $pdf->Ln(10);
    $pdf->Cell(35, 5, $lang->translation("Fecha"), 0, 0, 'L');
    $pdf->Cell(75, 5, '','B', 1, 'L');

}



$pdf->Output();
