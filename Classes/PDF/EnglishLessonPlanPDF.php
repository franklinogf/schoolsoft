<?php

namespace Classes\PDF;

use App\Models\EnglishLessonPlan;
use Classes\PDF;

class EnglishLessonPlanPDF extends PDF
{
    private EnglishLessonPlan $plan;

    private array $transversalThemes = [
        ['title' => 'Educate to love each other', 'length' => 5],
        ['title' => 'Educate for citizenship', 'length' => 5],
        ['title' => 'Educate for healthy communion', 'newLine' => true],
        ['title' => 'Educate for conservation of Environment', 'length' => 5],
        ['title' => 'Educate for Promotion of Life', 'length' => 5],
        ['title' => 'Educate for Transcendence', 'length' => 5],
        ['title' => 'Educate for Ethical Leadership', 'newLine' => true],
    ];

    private array $integrations = [
        ['title' => 'Spanish', 'length' => 5],
        ['title' => 'History', 'length' => 5],
        ['title' => 'Science', 'length' => 5],
        ['title' => 'Math', 'length' => 5],
        ['title' => 'Art', 'length' => 5],
        ['title' => 'Physical Education', 'length' => 5],
        ['title' => 'Health', 'length' => 5],
        ['title' => 'Technology', 'length' => 5],
        ['title' => 'Others', 'newLine' => true],
    ];

    private array $modifications = [
        'Seating',
        'Additional Time',
        'Individualized help',
        'Additional time for tests',
        'Positive reinforcement',
        'Others',
    ];

    public function __construct(EnglishLessonPlan $plan)
    {
        parent::__construct();
        $this->plan = $plan;
        $this->headerFirstPage = true;
        $this->SetAutoPageBreak(false);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderPage1();
        $this->AddPage();
        $this->renderPage2();
        $this->AddPage();
        $this->renderPage3();
    }

    private function renderPage1(): void
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'ENGLISH LESSON PLAN', 0, 1, 'C');

        $this->SetFont('Arial', '', 8);

        // Title, Date, Duration
        $this->Cell($this->getWidth('TITLE:', 1), 5, 'TITLE:');
        $this->Cell(70, 5, $this->plan->titulo, 'B');
        $this->Cell($this->getWidth('DATE'), 5, 'DATE:');
        $dateStr = $this->formatDate($this->plan->fecha);
        $this->Cell(25, 5, $dateStr, 'B', 0, 'C');
        $this->Cell($this->getWidth('DURATION OF LESSON:'), 5, 'DURATION OF LESSON:');
        $this->Cell(15, 5, (string)$this->plan->duracion, 'B', 1, 'C');

        $this->Ln(2);

        // Teacher, Class, Grade
        $this->Cell($this->getWidth('TEACHER:'), 5, 'TEACHER:');
        $this->Cell(60, 5, $this->plan->profesor, 'B');
        $this->Cell($this->getWidth('CLASS:'), 5, 'CLASS:');
        $this->Cell(30, 5, $this->plan->materia, 'B', 0, 'C');
        $this->Cell($this->getWidth('GRADE:'), 5, 'GRADE:');
        $grade = strlen($this->plan->materia) >= 5 ? substr($this->plan->materia, 3, 2) : '';
        $this->Cell(30, 5, $grade, 'B', 1, 'C');

        $this->Ln(2);

        // Transversal Themes
        $this->Cell($this->getWidth('TRANSVERSAL THEMES:', 5), 5, 'TRANSVERSAL THEMES:');
        foreach ($this->transversalThemes as $key => $theme) {
            $index = $key + 1;
            $checked = $this->plan->{"transversal{$index}"} === 'si';
            $this->drawCheckbox($checked);
            $length = $theme['length'] ?? 3;
            $newLine = $theme['newLine'] ?? false;
            $this->Cell($this->getWidth($theme['title'], $length), 5, $theme['title'], 0, $newLine ? 1 : 0);
        }

        $this->Ln(2);

        // Integration
        $this->Cell($this->getWidth('INTEGRATION: '), 5, 'INTEGRATION:');
        foreach ($this->integrations as $key => $integration) {
            $index = $key + 1;
            $checked = $this->plan->{"integracion{$index}"} === 'si';
            $this->drawCheckbox($checked);
            $length = $integration['length'] ?? 3;
            $newLine = $integration['newLine'] ?? false;
            $this->Cell($this->getWidth($integration['title'], $length), 5, $integration['title'], 0, $newLine ? 1 : 0);
        }

        $this->Ln(5);

        // Stage 1 - Expected Results
        $this->Cell(0, 5, 'STAGE 1 - EXPECTED RESULTS', 1, 1, 'C');
        $resumenY = $this->GetY();
        $this->Rect($this->GetX(), $resumenY, 190, 15);
        $this->MultiCell(190, 5, $this->plan->resumen ?? '');
        $this->SetXY(10, $resumenY + 15);

        // Essential Questions and Understanding
        $preguntasY = $this->GetY();
        $this->Rect($this->GetX(), $preguntasY + 5, 95, 40);
        $this->Cell(95, 5, 'ESSENTIAL QUESTIONS', 1, 1, 'C');
        for ($i = 1; $i <= 5; $i++) {
            $this->Ln(2);
            $this->Cell(10, 5, "PE{$i}");
            $this->Cell(80, 5, $this->plan->{"pe{$i}"} ?? '', 'B', 1);
        }

        $this->SetXY(105, $preguntasY);
        $this->SetMargins(105, $preguntasY);
        $this->Rect(105, $preguntasY + 5, 95, 40);
        $this->Cell(95, 5, 'ESSENTIAL UNDERSTANDING', 1, 1, 'C');
        for ($i = 1; $i <= 5; $i++) {
            $this->Ln(2);
            $this->Cell(10, 5, "ED{$i}");
            $this->Cell(80, 5, $this->plan->{"ed{$i}"} ?? '', 'B', 1);
        }

        // General Objectives
        $this->SetMargins(10, $preguntasY + 45);
        $this->SetXY(10, $preguntasY + 45);
        $this->Rect(10, $this->GetY(), 190, 10);
        $this->Ln(2);
        $this->Cell($this->getWidth('GENERAL OBJECTIVES '), 5, 'GENERAL OBJECTIVES');
        $this->Cell(0, 5, $this->plan->objetivo_general ?? '', 'B', 1);
    }

    private function renderPage2(): void
    {
        $this->SetMargins(10, 10);
        $this->SetXY(10, 10);

        $this->Cell(0, 5, 'STAGE 2 - EVIDENCE TO EVALUATE LEARNING', 1, 1, 'C');
        $etapa2Y = $this->GetY();

        // Performance Tasks
        $this->Cell(95, 5, 'PERFORMANCE TASKS', 1, 1, 'C');
        $this->Rect($this->GetX(), $etapa2Y + 5, 95, 100);
        $this->MultiCell(95, 5, $this->plan->tareas ?? '');

        // Other Evidence
        $this->SetMargins(105, $etapa2Y);
        $this->SetXY(105, $etapa2Y);
        $this->Cell(95, 5, 'OTHER EVIDENCE', 1, 1, 'C');
        $this->Rect($this->GetX(), $etapa2Y + 5, 95, 100);
        $this->MultiCell(95, 5, $this->plan->otra ?? '');
    }

    private function renderPage3(): void
    {
        $this->SetMargins(10, 10);
        $this->SetXY(10, 10);

        // Stage 3 - Learning Plan
        $this->Cell(0, 5, 'STAGE 3 - LEARNING PLAN', 1, 1, 'C');
        $this->Rect(10, $this->GetY(), 190, 23);
        $this->Ln(2);

        $this->Cell($this->getWidth('Standard: '), 5, 'Standard:');
        $this->Cell(0, 5, $this->plan->expectativa ?? '', 'B', 1);
        $this->Ln(2);

        $this->Cell($this->getWidth('Depth of Knowledge: '), 5, 'Depth of Knowledge:');
        $this->Cell(0, 5, $this->plan->estrategia ?? '', 'B', 1);
        $this->Ln(2);

        $this->Cell($this->getWidth('Objectives: '), 5, 'Objectives:');
        $this->Cell(0, 5, $this->plan->objetivos ?? '', 'B', 1);

        // Weekly Schedule Header
        $this->SetFillColor(200);
        $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY'];
        $this->Cell(20, 5, 'Date', 'LTR', 0, 'C', true);
        foreach ($days as $day) {
            $this->Cell(34, 5, $day, 'LTR', 0, 'C', true);
        }
        $this->Ln();

        $this->Cell(20, 5, 'Fecha', 'LBR', 0, 'C', true);
        for ($i = 1; $i <= 5; $i++) {
            $date = $this->plan->{"fecha{$i}"};
            $dateStr = $this->formatDate($date);
            if (empty($dateStr)) {
                $dateStr = '__/__/20__';
            } else {
                // Convert to d/m/Y format if we have a valid date
                $timestamp = strtotime($dateStr);
                if ($timestamp) {
                    $dateStr = date('d/m/Y', $timestamp);
                }
            }
            $this->Cell(34, 5, $dateStr, 'LBR', 0, 'C', true);
        }
        $this->Ln();
        $this->SetFillColor(0);
        $this->Ln();

        // Activities
        $actividadesY = $this->GetY();
        $this->Cell(20, 30, 'Activities', 1, 0, 'C');
        for ($i = 1; $i <= 5; $i++) {
            $this->SetXY(30 + (34 * ($i - 1)), $actividadesY);
            $this->SetMargins(30 + (34 * ($i - 1)), $actividadesY);
            $this->Rect($this->GetX(), $actividadesY, 34, 30);
            $this->MultiCell(34, 5, $this->plan->{"actividades{$i}"} ?? '');
        }
        $this->SetXY(10, $actividadesY + 30);
        $this->SetMargins(10, $actividadesY + 30);

        // Modifications for Students
        $acomodoY = $this->GetY();
        $this->Rect($this->GetX(), $acomodoY, 20, 45);
        $this->Ln(17);
        $this->MultiCell(20, 4, 'Modification for Students');

        $this->SetFontSize(7);
        for ($i = 1; $i <= 5; $i++) {
            $this->SetXY(30 + (34 * ($i - 1)), $acomodoY);
            $this->SetMargins(30 + (34 * ($i - 1)), $acomodoY);
            $this->Rect($this->GetX(), $acomodoY, 34, 45);

            for ($a = 1; $a <= 6; $a++) {
                $checked = $this->plan->{"acomodo{$i}_{$a}"} === 'si';
                $this->drawCheckbox($checked, -1.5);
                $this->Cell(4);
                $modification = $this->modifications[$a - 1];
                if ($a === 6 && !empty($this->plan->{"otro{$i}"})) {
                    $modification .= ': ' . $this->plan->{"otro{$i}"};
                }
                $this->MultiCell(30, 5, $modification);
            }
        }
    }
}
