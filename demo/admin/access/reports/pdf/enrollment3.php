<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Parents;
use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Route;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Certificación de costos", "Certificación de costos"],

]);
$school = new School();
$year = $school->year();
$withFees = $_POST['fees'] === 'si' ? true : false;
$option = $_POST['option'];
$fees = DB::table('hojacostos')->orderBy('id')->get();
$pdf = new PDF();
$pdf->useFooter(false);
$pdf->SetAutoPageBreak(false);
$pdf->SetTitle($lang->translation("Certificación de costos"), true);
$pdf->Fill();
$discounts = [0 => 0, 1 => 30, 2 => 150, 3 => 175];
$newFees = [0 => 220, 1 => 200, 2 => 200, 3 => 200];
$monthlyFee = [0 => 13, 1 => 14, 2 => 15, 3 => 16];

if ($option === 'student') {
    $studentMt = $_POST['student'];
    $student = new Student($studentMt);
    $students = $student->findById($student->id);
    foreach ($students as $Student) {
        $studentClass = new Student();
        $studentsFees = $studentClass->findById($Student->id);
        foreach ($studentsFees as $count => $studentFee) {           
            DB::table('year')->where(['mt', $studentFee->mt])->update([
                'cuota' => $newFees[$count],
                'major' => $count === 0 ? 'M' : '',
                'desc_men' => $discounts[$count],
            ]);
        }
    }
    $students = $student->findById($student->id);
} else {
    $grade = $_POST['grade'];
    $students = DB::table("year")->where([
        ['grado', $grade],
        ['year', $year],
        ['activo', '']
    ])->whereRaw("AND grado NOT LIKE '%12%'")->orderBy("apellidos")->get();
    foreach ($students as $Student) {
        $studentClass = new Student();
        $studentsFees = $studentClass->findById($Student->id);
        foreach ($studentsFees as $count => $studentFee) {
           
            DB::table('year')->where(['mt', $studentFee->mt])->update([
                'cuota' => $newFees[$count],
                'major' => $count === 0 ? 'M' : '',
                'desc_men' => $discounts[$count],
            ]);
        }
    }
    $students = DB::table("year")->where([
        ['grado', $grade],
        ['year', $year],
        ['activo', '']
    ])->whereRaw("AND grado NOT LIKE '%12%'")->orderBy("apellidos")->get();
}



foreach ($students as $student) {
    $charges = [];
    $monthly = $total = $subTotal = 0;
    foreach ($students as $Student) {
        $studentClass = new Student();
        $studentsFees = $studentClass->findById($Student->id);
        foreach ($studentsFees as $count => $studentFee) {           
           if($student->mt === $studentFee->mt){
            $monthly = $fees[$monthlyFee[$count]]->regular;
           }
        }
    }
    $parent = new Parents($student->id);
    $nextGrade = Util::getNextGrade($student->grado, true);
    $pdf->addPage();
    $pdf->SetDash();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, utf8_decode($lang->translation("Certificación de costos")), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, "Periodo escolar $year", 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 5, '# Cuenta ', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 5, $student->id, 1, 1, 'R');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(90, 5, 'Nombre del Estudiante', 1, 0, 'C', true);
    $pdf->Cell(30, 5, '# Est.', 1, 0, 'C', true);
    $pdf->Cell(30, 5, 'Grado Anterior', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Grado Nuevo Curso', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(90, 5, "$student->apellidos $student->nombre", 1);
    $pdf->Cell(30, 5, "XXX-XX-" . Util::ssLast4Digits($student->ss), 1, 0, 'C');
    $pdf->Cell(30, 5, $student->grado, 1, 0, 'C');
    $pdf->Cell(40, 5, $nextGrade, 1, 1, 'C');

    if ($withFees) {
        $charges[0] = $student->cuota;
        // $charges[0] = $fees[0]->regular;
        if ($nextGrade === 'KG' || $nextGrade === 'PK') {
            $charges[1] = $fees[1]->regular;
        } else {
            $charges[1] = $fees[2]->regular;
        }
        if ($nextGrade === 'KG') {
            $charges[2] = $fees[3]->regular;
        } else if ($nextGrade === '06') {
            $charges[2] = $fees[4]->regular;
        } else if ($nextGrade === '09') {
            $charges[2] = $fees[5]->regular;
        } else if ($nextGrade === '10') {
            $charges[2] = $fees[7]->regular;
        }

        if (is_numeric($nextGrade) && $nextGrade >= 9) {
            $charges[3] = $fees[6]->regular;
        }
        if (is_numeric($nextGrade) && $nextGrade >= 3 && $nextGrade <= 10) {
            $charges[4] = 25;
        }
        if (is_numeric($nextGrade) && $nextGrade === 4) {
            $charges[5] = $fees[8]->regular;
        } else if (is_numeric($nextGrade) && $nextGrade >= 6 && $nextGrade <= 10) {
            $charges[5] = $fees[9]->regular;
        }



        // Fees
        $pdf->Ln(2);
        $pdf->Cell(0, 5, utf8_decode('CUOTA DE INSCRIPCIÓN (NO REEMBOLSABLE)'), 0, 1);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'Estudiantes', 0, 1);
        newLine('A. Primer estudiante', $fees[0]->regular);
        newLine('B. Otros hermanos', $fees[0]->hermano, $charges[0]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'RECURSOS Y MATERIALES EDUCATIVOS', 0, 1);
        newLine('Pre-Kinder, Kinder', $fees[1]->regular);
        newLine('1ro a 12mo', $fees[2]->regular, $charges[1]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, utf8_decode('PROMOCIÓN'), 0, 1);
        newLine('Kinder (Promoción)', $fees[3]->regular);
        newLine('Sexto (Promoción)', $fees[4]->regular);
        newLine('Noveno (Promoción)', $fees[5]->regular);
        newLine('Duodecimo (Graduación)', $fees[7]->regular, $charges[2]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'PRUEBA DE DOPAJE', 0, 1);
        newLine('9no a 12mo', $fees[6]->regular, $charges[3]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'EXAMENES ESTANDARIZADOS', 0, 1);
        newLine('3ro a 10mo', 25, $charges[4]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'ACTIVIDADES', 0, 1);
        newLine('Primera comunión (Cuarto grado)', $fees[8]->regular);
        newLine('Retiro (Sexto a Duodecimo grado)', $fees[9]->regular, $charges[5]);
        $pdf->Ln(3);

        $pdf->Cell(0, 5, 'CUOTAS POR FAMILIA', 0, 1);
        newLine('1. Mantenimiento y mejoras', $fees[10]->regular, $student->major === 'M' ? $fees[10]->regular : 0);
        newLine('2. Fondos becas padre claro', $fees[11]->regular, $student->major === 'M' ? $fees[11]->regular : 0);
        $pdf->Ln(5);
        //enf fees
    } else {
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 13);
        $pdf->Cell(20);
        $pdf->Cell(0, 5, utf8_decode('¡Paz y Bien!'), 0, 1);
        $pdf->Ln(2);
        $pdf->Cell(20);
        $pdf->Cell(0, 5, utf8_decode('Gracias por permitirnos continuar ofreciéndoles educación de excelencia.'), 0, 1);
        $pdf->Ln(2);
        $pdf->Cell(20);
        $pdf->Cell(0, 5, utf8_decode('A continuación desglosamos los costos de estudios.'), 0, 1);
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 10);
    }
    $pdf->Cell(0, 5, utf8_decode('SUB TOTAL MATRÍCULA'));
    $pdf->Cell(0, 5, number_format($subTotal, 2), 0, 1, 'R');
    $pdf->Ln(3);
    $total = $subTotal + $monthly;
    $pdf->SetDash(2, 2);
    $pdf->Cell(0, 5, "MENSUALIDAD ADELANTADA DE MAYO 2024");
    $pdf->Line(90, $pdf->GetY() + 2.5, 175, $pdf->GetY() + 2.5);
    $pdf->Cell(0, 5, number_format($monthly,2), 0, 1, 'R');
    $pdf->Ln(3);

    $pdf->Cell(0, 5, utf8_decode('TOTAL MATRÍCULA'));
    $pdf->Line(50, $pdf->GetY() + 2.5, 175, $pdf->GetY() + 2.5);
    $pdf->SetDash(2, 1);
    $pdf->Rect(180, $pdf->GetY(), 20, 5);
    $pdf->Cell(0, 5, number_format($total, 2), 0, 1, 'R');
    $pdf->Ln(20);

    // if (is_numeric($nextGrade) && $nextGrade >= 8) {
    //     $pdf->Image('./signs/firma2.gif',140,$pdf->GetY()-12,50);
    // }else{
    //     $pdf->Image('./signs/firma1.gif',143,$pdf->GetY()-9,50);
    // }
    $pdf->SetDash();
    $pdf->Cell(130);
    $pdf->Cell(50, 5, '', 'B', 2);
    $pdf->Cell(50, 5, 'PRINCIPAL', 0, 1, 'C');
}
// functions 
function newLine($text, $price1, $price2 = '')
{
    global $pdf;
    global $subTotal;
    $pdf->Cell(10);
    $pdf->Cell(80, 5, utf8_decode($text));
    $pdf->Cell(50, 5, number_format($price1, 2));
    $pdf->Cell(50, 5, $price2 !== '' ? number_format($price2, 2) : '', 0, 1);
    $subTotal += $price2 !== '' ? number_format($price2, 2) : 0;
};

$pdf->Output();
