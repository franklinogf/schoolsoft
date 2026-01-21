<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\Student;
use App\Pdfs\InfirmaryReportPdf;
use Classes\Session;

Session::is_logged();

// Get student SS from query parameter
$ss = $_GET['ss'] ?? '';

if (empty($ss)) {
    die('Error: No se ha especificado un estudiante');
}

// Load student with family and infirmary data
$student = Student::where('ss', $ss)
    ->with(['family', 'infirmary'])
    ->first();

if (!$student) {
    die('Error: No se encontró el estudiante');
}

// Generate and output the PDF
$pdf = new InfirmaryReportPdf($student);
$pdf->generate();
$pdf->Output('I', 'Informe_Enfermeria_' . $student->ss . '.pdf');
