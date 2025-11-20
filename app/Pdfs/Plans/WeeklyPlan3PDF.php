<?php

namespace App\Pdfs\Plans;

use App\Models\WeeklyPlan3;
use Classes\PDF;

class WeeklyPlan3PDF extends PDF
{


    public function __construct(private WeeklyPlan3 $plan)
    {
        parent::__construct();
        $this->SetTitle('Lesson Plan Book', true);
        $this->headerFirstPage = true;
        $this->SetLeftMargin(10);
        $this->useFooter(false);
    }

    public function generate(): void
    {
        $this->renderCoverPage();
        $this->renderWeeklyActivities();
        $this->renderStudentList();
        $this->renderNotes();
    }

    private function renderCoverPage(): void
    {
        $this->AddPage();
        $this->SetFillColor(240);

        // Title
        $this->SetFont('Arial', 'B', 40);
        $this->Ln(35);
        $this->Cell(0, 20, "LESSON", 0, 1, 'C');
        $this->Cell(0, 20, "PLAN", 0, 1, 'C');
        $this->Cell(0, 20, "BOOK", 0, 1, 'C');

        // Teacher info
        $teacher = $this->plan->teacher;
        $this->Ln(55);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(15);
        $this->Cell(28, 5, 'TEACHER:');
        $this->Cell(70, 5, $teacher->nombre . ' ' . $teacher->apellidos, 'B', 1);
        $this->Ln(20);
        $this->Cell(15);
        $this->Cell(33, 5, 'SCHOOL YEAR:');
        $this->Cell(50, 5, $this->plan->year, 'B', 0, 'C');
        $this->Cell(15);
        $this->Cell(33, 5, 'COURSE/GRADE:');
        $this->Cell(48, 5, $this->plan->curso, 'B', 1, 'C');
    }

    private function renderWeeklyActivities(): void
    {
        $weekDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
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
        $this->AddPage('L');

        // Get week info
        $week = strstr($this->plan->week, "W");
        $y = str_replace('-', '', strstr($this->plan->week, "W", true));
        $monthName = $months[date("m", strtotime($y . $week . "1"))];

        $this->SetFont('Arial', 'B', 15);
        $this->Ln(5);
        $this->Cell(30, 5, $monthName, 0, 0, 'C');
        $this->Cell(100, 5, 'Tema', 0, 0, 'C');
        $this->Cell(30);
        $this->Cell(120, 5, 'Objetivo', 0, 1, 'C');
        $this->Ln(3);

        // Table setup
        $X = $this->GetX();
        $Y = $this->GetY();

        // Draw table header borders
        $this->Rect($X, 10, 280, 0); // Top horizontal
        $this->Rect($X + 30, 10, 0, 15); // Left vertical 2
        $this->Rect($X + 145, 10, 0, 15); // Middle vertical
        $this->Rect($X + 280, 10, 0, 15); // Right vertical
        $this->Rect($X, 10, 0, 15); // Left vertical

        $this->SetXY($X, $Y);
        $this->SetMargins($X, $Y);
        $this->SetFont('Arial', '', 11);

        // Draw table rows
        $this->SetRowWidths([30, 115, 135]);

        for ($i = 1; $i <= 5; $i++) {
            $dayDate = date('d', strtotime($y . $week . $i));
            $this->Row([
                $weekDays[$i - 1] . " " . $dayDate,
                $this->plan->{"dia{$i}_1"} ?? '',
                $this->plan->{"dia{$i}_2"} ?? ''
            ]);
        }
    }

    private function renderStudentList(): void
    {
        $X = $this->GetX();
        $Y = $this->GetY();

        $this->SetXY($X, $Y);
        $this->SetMargins($X, $Y);
        $this->AddPage('L');
        $this->SetFont('Arial', '', 9);

        // Table headers
        $this->Cell(5, 5, '', 'LTB');
        $this->Cell(61, 5, 'Student', 1, 0, 'C');
        $this->Cell(50, 5, 'Parent/Guardian', 1, 0, 'C');
        $this->Cell(18, 5, 'Home phone', 1, 0, 'C');
        $this->Cell(18, 5, 'Cell phone', 1, 0, 'C');
        $this->Cell(42, 5, 'E-mail', 1, 0, 'C');
        $this->Cell(90, 5, 'Special needs', 1, 1, 'C');

        $this->SetFont('Arial', '', 7);

        foreach ($this->plan->getStudents() as $index => $student) {
            $family = $student->family;
            $this->Cell(5, 5, $index + 1, 'LTB', 0, 'C');
            $this->Cell(61, 5, "$student->apellidos $student->nombre", 1);
            $this->Cell(50, 5, $family->madre ?: $family->padre ?: '', 1);
            $this->Cell(18, 5, $family->tel_m ?: $family->tel_p ?: '', 1, 0, 'C');
            $this->Cell(18, 5, $family->cel_m ?: $family->cel_p ?: '', 1, 0, 'C');
            $this->Cell(42, 5, $family->email_m ?: $family->email_p ?: '', 1, 0, 'C');
            $this->Cell(90, 5, $student->needs->necesidad ?? '', 1, 1);
        }
    }

    private function renderNotes(): void
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 15);

        // Add post-it background if available
        $imagePath = __DIR__ . '/../../demo/regiweb/options/weeklyplans/plan3/post it.png';
        if (file_exists($imagePath)) {
            $this->Image($imagePath, 10, 5, 190, 250);
        }

        $this->Ln(80);
        $this->Cell(0, 10, "NOTA", 0, 1, 'C');
        $this->Ln(15);
        $this->SetFont('Arial', '', 12);
        $this->Cell(20);
        $this->MultiCell(150, 6, $this->plan->nota ?? '');
    }
}
