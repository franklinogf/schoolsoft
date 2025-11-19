<?php
require_once __DIR__ . '/../../../../app.php';
require_once '../../../../../vendor/autoload.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WeeklyPlan3;
use Classes\PDF;
use Classes\Session;

Session::is_logged();

$planId = $_GET['id'] ?? null;

if (!$planId) {
    die('Plan ID required');
}

$weeklyPlan = WeeklyPlan3::find($planId);

if (!$weeklyPlan) {
    die('Plan not found');
}

$teacher = $weeklyPlan->teacher;

// Get students for this course
$students = Student::query()
    ->whereHas('classes', function ($query) use ($weeklyPlan) {
        $query->where('curso', $weeklyPlan->curso);
    })->orderBy('apellidos')->get();

$days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
$months = [
    "01" => "Enero",
    "02" => "Febrero",
    "03" => "Marzo",
    "04" => "Abril",
    "05" => "Mayo",
    "06" => "Junio",
    "07" => "Julio",
    "08" => "Agosto",
    "09" => "Septiembre",
    "10" => "Octubre",
    "11" => "Noviembre",
    "12" => "Diciembre"
];

// Parse week information
$week = strstr($weeklyPlan->week, "W");
$y = str_replace('-', '', strstr($weeklyPlan->week, "W", true));
$monthName = $months[date("m", strtotime($y . $week . "1"))];

// Create PDF
$pdf = new PDF();
$pdf->headerFirstPage = true;
$pdf->SetLeftMargin(10);
$pdf->useFooter(false);


// Page 1: Cover page
$pdf->AddPage();
$pdf->SetFillColor(240);

// Title
$pdf->SetFont('Arial', 'B', 40);
$pdf->Ln(35);
$pdf->Cell(0, 20, "LESSON", 0, 1, 'C');
$pdf->Cell(0, 20, "PLAN", 0, 1, 'C');
$pdf->Cell(0, 20, "BOOK", 0, 1, 'C');

// Teacher info
$pdf->Ln(55);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(15);
$pdf->Cell(28, 5, 'TEACHER:');
$pdf->Cell(70, 5, $teacher->nombre . ' ' . $teacher->apellidos, 'B', 1);
$pdf->Ln(20);
$pdf->Cell(15);
$pdf->Cell(33, 5, 'SCHOOL YEAR:');
$pdf->Cell(50, 5, $weeklyPlan->year, 'B', 0, 'C');
$pdf->Cell(15);
$pdf->Cell(33, 5, 'COURSE/GRADE:');
$pdf->Cell(48, 5, $weeklyPlan->curso, 'B', 1, 'C');

// Page 2: Weekly activities
$pdf->AddPage('L');
$pdf->SetFont('Arial', 'B', 15);
$pdf->Ln(5);
$pdf->Cell(30, 5, $monthName, 0, 0, 'C');
$pdf->Cell(100, 5, 'Tema', 0, 0, 'C');
$pdf->Cell(30);
$pdf->Cell(120, 5, 'Objetivo', 0, 1, 'C');
$pdf->Ln(3);

// Table setup
$X = $pdf->GetX();
$Y = $pdf->GetY();

// Draw table header borders
$pdf->Rect($X, 10, 280, 0); // Top horizontal
$pdf->Rect($X + 30, 10, 0, 15); // Left vertical 2
$pdf->Rect($X + 145, 10, 0, 15); // Middle vertical
$pdf->Rect($X + 280, 10, 0, 15); // Right vertical
$pdf->Rect($X, 10, 0, 15); // Left vertical

$pdf->SetXY($X, $Y);
$pdf->SetMargins($X, $Y);
$pdf->SetFont('Arial', '', 11);

// Draw table rows
$pdf->SetRowWidths([30, 115, 135]);

for ($i = 1; $i <= 5; $i++) {
    $dayDate = date('d', strtotime($y . $week . $i));
    $pdf->Row([
        $days[$i - 1] . " " . $dayDate,
        $weeklyPlan->{"dia{$i}_1"} ?? '',
        $weeklyPlan->{"dia{$i}_2"} ?? ''
    ]);
}




// Page 3: Student list with needs
$pdf->SetXY($X, $Y);
$pdf->SetMargins($X, $Y);
$pdf->AddPage('L');
$pdf->SetFont('Arial', '', 9);

// Table headers
$pdf->Cell(5, 5, '', 'LTB');
$pdf->Cell(61, 5, 'Student', 1, 0, 'C');
$pdf->Cell(50, 5, 'Parent/Guardian', 1, 0, 'C');
$pdf->Cell(18, 5, 'Home phone', 1, 0, 'C');
$pdf->Cell(18, 5, 'Cell phone', 1, 0, 'C');
$pdf->Cell(42, 5, 'E-mail', 1, 0, 'C');
$pdf->Cell(90, 5, 'Special needs', 1, 1, 'C');

// $count = 1;
$pdf->SetFont('Arial', '', 7);

foreach ($students as $index => $student) {
    $family = $student->family;
    $pdf->Cell(5, 5, $index + 1, 'LTB', 0, 'C');
    $pdf->Cell(61, 5, "$student->apellidos $student->nombre", 1);
    $pdf->Cell(50, 5, $family->madre ?: $family->padre ?: '', 1);
    $pdf->Cell(18, 5, $family->tel_m ?: $family->tel_p ?: '', 1, 0, 'C');
    $pdf->Cell(18, 5, $family->cel_m ?: $family->cel_p ?: '', 1, 0, 'C');
    $pdf->Cell(42, 5, $family->email_m ?: $family->email_p ?: '', 1, 0, 'C');
    $pdf->Cell(90, 5, $student->needs->necesidad ?? '', 1, 1);
}

// Page 4: Notes

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);

// Add post-it background if available
if (file_exists(__DIR__ . '/post it.png')) {
    $pdf->Image(__DIR__ . '/post it.png', 10, 5, 190, 250);
}

$pdf->Ln(80);
$pdf->Cell(0, 10, "NOTA", 0, 1, 'C');
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(20);
$pdf->MultiCell(150, 6, $weeklyPlan->nota ?? '');

$pdf->Output();
