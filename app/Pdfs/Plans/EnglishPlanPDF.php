<?php

namespace App\Pdfs\Plans;

use App\Models\Admin;
use App\Models\EnglishPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class EnglishPlanPDF extends PDF implements PdfInterface
{
    private EnglishPlan $plan;

    public function __construct(EnglishPlan $plan)
    {
        parent::__construct();
        $this->plan = $plan;
        $this->SetTitle('English Plan');
        $this->SetAutoPageBreak(true, 10);
    }

    public function generate(): void
    {
        $this->renderPage1();
        $this->renderPage2();
        $this->renderPage3();
    }

    private function renderPage1(): void
    {
        $school = Admin::primaryAdmin();
        $this->AddPage('L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'ENGLISH PLAN', 0, 1, "C");
        $this->Ln(10);

        // Header Information
        $this->Cell(20, 7, "Teacher:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $this->plan->teacher, "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(45);
        $this->Cell(25, 7, "Institution:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $school->colegio, "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();

        $this->Cell(20, 7, "Grade:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $this->plan->grade, "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(45);
        $this->Cell(25, 7, "Dates:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $this->formatDate($this->plan->dates), "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();

        $this->Cell(20, 7, "Subject:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $this->plan->subject, "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(45);
        $this->Cell(25, 7, "Topic:");
        $this->SetFont('Arial', '', 10);
        $this->Cell(80, 7, $this->plan->topic, "B");
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(10);

        // Standards and Strategy Section
        $this->renderStandardsAndStrategy();

        $this->Ln(5);

        // General Objectives
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, "General Objectives:", 0, 1);
        $this->Ln(5);
        $this->Rect($this->GetX(), $this->GetY(), 250, 40);
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(250, 5, $this->plan->general);
    }

    private function renderStandardsAndStrategy(): void
    {
        $this->Rect($this->GetX(), $this->GetY(), 100, 7);
        $this->Cell(100, 7, "Standards", 0, 0, "C");
        $this->Rect($this->GetX(), $this->GetY(), 150, 7);
        $this->Cell(150, 7, "Strategy", 0, 1, "C");
        $this->Rect($this->GetX(), $this->GetY(), 100, 40);
        $y = $this->GetY();

        // Standards       
        $standards = [
            ['field' => 'standard1', 'label' => 'Oral Communication'],
            ['field' => 'standard2', 'label' => 'Written Communication'],
            ['field' => 'standard3', 'label' => 'Communication Reading']
        ];
        $this->SetFont('Arial', '', 11);
        foreach ($standards as $standard) {
            $this->Cell(4);
            $this->drawCheckbox($this->plan->{$standard['field']} == "Si");
            $this->Cell(98, 5, $standard['label']);
            $this->Ln();
        }

        // Depth levels
        $this->Ln(5);
        $this->Cell($this->getWidth("Depth level of knowledge:") + 2, 5, "Depth level of knowledge:");


        $depthLevels = [
            ['field' => 'depth1', 'label' => 'Rote', 'width' => 12],
            ['field' => 'depth2', 'label' => 'Processing', 'width' => 25],
            ['field' => 'depth3', 'label' => 'Strategic', 'width' => 20],
            ['field' => 'depth4', 'label' => 'Extended', 'width' => 25]
        ];
        foreach ($depthLevels as $index => $depth) {
            if ($index % 2 === 0 && $index !== 0) {
                $this->Cell($this->getWidth("Depth level of knowledge:") + 2, 5, "");
            }
            $this->Cell(2);
            $this->drawCheckbox($this->plan->{$depth['field']} == "Si");
            $this->Cell($depth['width'], 5, $depth['label'], 0, $index % 2 === 1 ? 1 : 0);
        }

        // Strategy Section
        $this->SetXY(110, $y);
        $this->Rect($this->GetX(), $this->GetY(), 150, 40);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 5, "Strategy:", 0, 1);
        $this->SetFont('Arial', '', 11);
        $this->Cell(102);


        $strategies = [
            ['field' => 'strategy1', 'label' => 'ECA', 'width' => 12],
            ['field' => 'strategy2', 'label' => 'Trilogy Literacy', 'width' => 30],
            ['field' => 'strategy3', 'label' => 'Cycles of learning', 'width' => 25]
        ];
        foreach ($strategies as $strategy) {
            $this->Cell(2);
            $this->drawCheckbox($this->plan->{$strategy['field']} == "Si");
            $this->Cell($strategy['width'], 5, $strategy['label']);
        }
        $this->Ln(7);

        // Appraisal Section
        $this->renderAppraisalSection();
    }

    private function renderAppraisalSection(): void
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(102);
        $this->Cell(20, 5, "Appraisal:", 0, 1);
        $this->SetFont('Arial', '', 11);

        $appraisals = [
            [
                ['field' => 'appraisal1', 'label' => 'Diagnostic Test', 'width' => 30],
                ['field' => 'appraisal2', 'label' => 'Whirlwind of ideas', 'width' => 35],
                ['field' => 'appraisal3', 'label' => 'Targeted List', 'width' => 30]
            ],
            [
                ['field' => 'appraisal4', 'label' => 'Concept Map', 'width' => 27],
                ['field' => 'appraisal5', 'label' => 'Concrete Poem', 'width' => 30],
                ['field' => 'appraisal6', 'label' => 'Comics', 'width' => 17],
                ['field' => 'appraisal7', 'label' => 'Open Question', 'width' => 30]
            ],
            [
                ['field' => 'appraisal8', 'label' => 'Reflective Journal', 'width' => 35],
                ['field' => 'appraisal9', 'label' => 'Test', 'width' => 12],
                ['field' => 'appraisal10', 'label' => 'Interviews', 'width' => 20],
                ['field' => 'appraisal11', 'label' => 'Quiz', 'width' => 13],
                ['field' => 'appraisal12', 'label' => 'Review', 'width' => 15],
                ['field' => 'appraisal13', 'label' => 'Draft', 'width' => 10]
            ],
            [
                ['field' => 'appraisal14', 'label' => 'Other', 'width' => 15]
            ]
        ];
        foreach ($appraisals as $row) {
            $this->Cell(102);
            foreach ($row as $appraisal) {
                $this->Cell(2);
                $this->drawCheckbox($this->plan->{$appraisal['field']} == "Si");
                $this->Cell($appraisal['width'], 5, $appraisal['label']);
            }
            $this->Ln();
        }
    }

    private function renderPage2(): void
    {
        $this->AddPage("L");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(125, 5, "Specific Objectives:", 0, 0, "C");
        $this->Cell(125, 5, "Use Norman Webb Verbs List:", 0, 1, "C");

        $this->SetFont('Arial', '', 11);

        // Level rows
        for ($level = 1; $level <= 4; $level++) {
            $this->Rect($this->GetX(), $this->GetY(), 15, 7);
            $this->Rect($this->GetX() + 15, $this->GetY(), 100, 7);
            $this->Rect($this->GetX() + 115, $this->GetY(), 135, 7);
            $this->Cell(15, 7, "Level {$level}");
            $this->Cell(100, 7, $this->plan->{"level{$level}_1"});
            $this->Cell(135, 7, $this->plan->{"level{$level}_2"}, 0, 1);
        }

        $this->Ln(5);

        // Activities table header
        $this->renderActivitiesTable();
    }

    private function renderActivitiesTable(): void
    {
        $this->SetFont('Arial', 'B', 12);
        $X = 41.6;

        // Header row
        $headers = ["Activities", "Materials", "Home", "Development", "Closing", "Assessment"];
        foreach ($headers as $i => $header) {
            $this->Rect($this->GetX() + $X * $i, $this->GetY(), $X, 7);
        }

        foreach ($headers as $header) {
            $this->Cell($X, 7, $header, 0, 0, "C");
        }
        $this->Ln();

        // Content boxes
        foreach ($headers as $i => $header) {
            $this->Rect($this->GetX() + $X * $i, $this->GetY(), $X, 70);
        }

        $Y = $this->GetY();

        // Activities column
        $this->renderActivitiesColumn($Y, $X);

        // Materials column
        $this->renderMaterialsColumn($Y, $X);

        // Home column
        $this->renderHomeColumn($Y, $X);

        // Development column
        $this->renderDevelopmentColumn($Y, $X);

        // Closing column
        $this->renderClosingColumn($Y, $X);

        // Assessment column
        $this->renderAssessmentColumn($Y, $X);
    }

    private function renderActivitiesColumn(float $Y, float $X): void
    {
        $this->SetFont('Arial', '', 10);
        $this->SetY($Y);
        $this->Cell($X, 5, "Fases:", 0, 1);

        $activities = [
            ['field' => 'activities1', 'label' => 'Exploration'],
            ['field' => 'activities2', 'label' => 'Conceptualization'],
            ['field' => 'activities3', 'label' => 'Implementation'],
            ['field' => 'activities4', 'label' => 'Before reading'],
            ['field' => 'activities5', 'label' => 'During read'],
            ['field' => 'activities6', 'label' => 'After reading'],
            ['field' => 'activities7', 'label' => 'Focus'],
            ['field' => 'activities8', 'label' => 'Scan'],
            ['field' => 'activities9', 'label' => 'Reflect'],
            ['field' => 'activities10', 'label' => 'Apply'],
        ];

        foreach ($activities as $activity) {
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$activity['field']} == "Si");
            $this->Cell($X, 5, $activity['label'], 0, 1);
        }
    }

    private function renderMaterialsColumn(float $Y, float $X): void
    {
        $materials = [
            ['field' => 'materials1', 'label' => 'Copy'],
            ['field' => 'materials2', 'label' => 'Book'],
            ['field' => 'materials3', 'label' => 'Slate'],
            ['field' => 'materials4', 'label' => 'Newspaper'],
            ['field' => 'materials5', 'label' => 'Calculator'],
            ['field' => 'materials6', 'label' => 'Computer'],
            ['field' => 'materials7', 'label' => 'Crayons'],
            ['field' => 'materials8', 'label' => 'Graph paper'],
            ['field' => 'materials9', 'label' => 'Construction paper'],
            ['field' => 'materials10', 'label' => 'Conveyor or rule'],
            ['field' => 'materials11', 'label' => 'Transparency'],
            ['field' => 'materials12', 'label' => 'Manipulatives'],
            ['field' => 'materials13', 'label' => 'Mimeographed sheet'],
            ['field' => 'materials14', 'label' => 'Other'],
        ];

        $this->SetY($Y);
        foreach ($materials as $material) {
            $this->Cell($X);
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$material['field']} == "Si");
            $this->Cell($X, 5, $material['label'], 0, 1);
        }
    }

    private function renderHomeColumn(float $Y, float $X): void
    {
        $homeItems = [
            ['field' => 'home1', 'label' => 'Reflection'],
            ['field' => 'home2', 'label' => 'Poem'],
            ['field' => 'home3', 'label' => 'Song'],
            ['field' => 'home4', 'label' => 'Game'],
            ['field' => 'home5', 'label' => 'Discussion of the', 'continuation' => 'Allocation'],
            ['field' => 'home6', 'label' => 'Questions on you to', 'continuation' => 'study'],
            ['field' => 'home7', 'label' => 'Review concepts', 'continuation' => 'discussed'],
            ['field' => 'home8', 'label' => 'Observation and study', 'continuation' => ['of: sheets, tables, graphs', 'and/or books.']],
        ];

        $this->SetY($Y);
        foreach ($homeItems as $item) {
            $this->Cell($X * 2);
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$item['field']} == "Si");
            $this->Cell($X, 5, $item['label'], 0, 1);

            if (isset($item['continuation'])) {
                if (\is_array($item['continuation'])) {
                    foreach ($item['continuation'] as $cont) {
                        $this->Cell($X * 2);
                        $this->Cell($X, 5, $cont, 0, 1);
                    }
                } else {
                    $this->Cell($X * 2);
                    $this->Cell($X, 5, $item['continuation'], 0, 1);
                }
            }
        }
    }

    private function renderDevelopmentColumn(float $Y, float $X): void
    {
        $developmentItems = [
            ['field' => 'development1', 'label' => 'Oral Reading'],
            ['field' => 'development2', 'label' => 'Reading and analySis'],
            ['field' => 'development3', 'label' => 'Definition of concepts'],
            ['field' => 'development4', 'label' => 'Demostration and', 'continuation' => 'examples aimed'],
            ['field' => 'development5', 'label' => 'Work practice: book,', 'continuation' => 'blackboard or paper'],
            ['field' => 'development6', 'label' => 'Oral Report'],
            ['field' => 'development7', 'label' => 'Fil analySis'],
            ['field' => 'development8', 'label' => 'Competition'],
            ['field' => 'development9', 'label' => 'Test'],
            ['field' => 'development10', 'label' => 'Test cut'],
        ];

        $this->SetY($Y);
        foreach ($developmentItems as $item) {
            $this->Cell($X * 3);
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$item['field']} == "Si");
            $this->Cell($X, 5, $item['label'], 0, 1);

            if (isset($item['continuation'])) {
                $this->Cell($X * 3);
                $this->Cell($X, 5, $item['continuation'], 0, 1);
            }
        }
    }

    private function renderClosingColumn(float $Y, float $X): void
    {
        $closingItems = [
            ['field' => 'closing1', 'label' => 'Clarifying concepts'],
            ['field' => 'closing2', 'label' => 'Discussion of the work'],
            ['field' => 'closing3', 'label' => 'To compare the work', 'continuation' => 'done'],
        ];

        $this->SetY($Y);
        foreach ($closingItems as $item) {
            $this->Cell($X * 4);
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$item['field']} == "Si");
            $this->Cell($X, 5, $item['label'], 0, 1);

            if (isset($item['continuation'])) {
                $this->Cell($X * 4);
                $this->Cell($X, 5, $item['continuation'], 0, 1);
            }
        }
    }

    private function renderAssessmentColumn(float $Y, float $X): void
    {
        $assessmentItems = [
            ['field' => 'assessment1', 'label' => 'Diagnostic test'],
            ['field' => 'assessment2', 'label' => 'Whirlwind of ideas'],
            ['field' => 'assessment3', 'label' => 'Targeted list'],
            ['field' => 'assessment4', 'label' => 'Concept map'],
            ['field' => 'assessment5', 'label' => 'Concrete poem'],
            ['field' => 'assessment6', 'label' => 'Comics'],
            ['field' => 'assessment7', 'label' => 'Draft'],
            ['field' => 'assessment8', 'label' => 'Open question'],
            ['field' => 'assessment9', 'label' => 'Reflective journal'],
            ['field' => 'assessment10', 'label' => 'Test'],
            ['field' => 'assessment11', 'label' => 'Interviews'],
            ['field' => 'assessment12', 'label' => 'Quiz'],
            ['field' => 'assessment13', 'label' => 'Review'],
            ['field' => 'assessment14', 'label' => 'Other'],
        ];

        $this->SetY($Y);
        foreach ($assessmentItems as $item) {
            $this->Cell($X * 5);
            $this->Cell(3);
            $this->drawCheckbox($this->plan->{$item['field']} == "Si");
            $this->Cell($X, 5, $item['label'], 0, 1);
        }
    }

    private function renderPage3(): void
    {
        $this->AddPage("L");
        $X = 41.6;

        $days = [
            ['name' => 'Tuesday', 'field' => 'tuesday'],
            ['name' => 'Wednesday', 'field' => 'wednesday'],
            ['name' => 'Thursday', 'field' => 'thursday'],
            ['name' => 'Friday', 'field' => 'friday'],
        ];

        foreach ($days as $day) {
            $this->renderDaySection($day['name'], $day['field'], $X);
            $this->Ln(30);
        }
    }

    private function renderDaySection(string $dayName, string $fieldPrefix, float $X): void
    {
        // Draw rectangles for all columns
        for ($i = 0; $i < 6; $i++) {
            $this->Rect($this->GetX() + $X * $i, $this->GetY(), $X, 30);
        }

        $this->SetFont('Arial', 'B', 12);
        $Y = $this->GetY();
        $this->Cell($X, 5, $dayName, 0, 1);
        $this->Cell($X, 5, "Fase:", 0, 1);
        $this->SetFont('Arial', '', 11);
        $this->Cell($X, 6, $this->plan->{$fieldPrefix}, "B");

        $this->SetXY(51.6, $Y);
        for ($i = 1; $i <= 5; $i++) {
            $this->Cell($X, 5, $this->plan->{$fieldPrefix . $i});
        }
    }
}
