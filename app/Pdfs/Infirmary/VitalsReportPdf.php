<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\Infirmary;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class VitalsReportPdf extends PDF implements PdfInterface
{
    private Student $student;
    private ?Infirmary $infirmary;
    private bool $withLines;
    private bool $additionalLines;

    public function __construct(Student $student, bool $withLines = true, bool $additionalLines = true)
    {
        parent::__construct('P', 'mm', 'Letter');

        $this->student = $student;
        $this->infirmary = $student->infirmary;
        $this->withLines = $withLines;
        $this->additionalLines = $additionalLines;

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Informe de Vitales') . ' - ' . $student->fullName, true);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderStudentInfo();
        $this->renderVitalsInfo();
        $this->renderVitalsTable();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('INFORME DE SIGNOS VITALES'), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderStudentInfo(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información del Estudiante'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Nombre') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->fullName, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Grado') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->grado, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Fecha de Nacimiento') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $birthDate = $this->student->fecha ? $this->formatDate($this->student->fecha) : '';
        $this->Cell(0, 5, $birthDate, 0, 1);

        $this->Ln(3);
    }

    private function renderVitalsInfo(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Medidas Actuales'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Peso') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 5, ($this->infirmary->peso ?? '') . ' lbs', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Estatura') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->infirmary->estatura ?? '', 0, 1);

        $this->Ln(5);
    }

    private function renderVitalsTable(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Registro de Signos Vitales'), 0, 1, 'L', true);
        $this->Ln(3);

        // Table headers
        $this->SetFont('Arial', 'B', 9);
        $this->Fill();
        $colWidth1 = 30; // Date
        $colWidth2 = 25; // Weight
        $colWidth3 = 25; // Height
        $colWidth4 = 30; // Blood Pressure
        $colWidth5 = 25; // Pulse
        $colWidth6 = 25; // Temperature
        $colWidth7 = 30; // Observations

        $this->Cell($colWidth1, 6, __('Fecha'), 1, 0, 'C', true);
        $this->Cell($colWidth2, 6, __('Peso'), 1, 0, 'C', true);
        $this->Cell($colWidth3, 6, __('Estatura'), 1, 0, 'C', true);
        $this->Cell($colWidth4, 6, __('Presión'), 1, 0, 'C', true);
        $this->Cell($colWidth5, 6, __('Pulso'), 1, 0, 'C', true);
        $this->Cell($colWidth6, 6, __('Temp.'), 1, 0, 'C', true);
        $this->Cell($colWidth7, 6, __('Obs.'), 1, 1, 'C', true);

        // Empty rows for recording
        $this->SetFont('Arial', '', 9);
        $rowCount = $this->additionalLines ? 20 : 10;

        for ($i = 0; $i < $rowCount; $i++) {
            $border = $this->withLines ? 1 : 0;
            $this->Cell($colWidth1, 8, '', $border, 0, 'C');
            $this->Cell($colWidth2, 8, '', $border, 0, 'C');
            $this->Cell($colWidth3, 8, '', $border, 0, 'C');
            $this->Cell($colWidth4, 8, '', $border, 0, 'C');
            $this->Cell($colWidth5, 8, '', $border, 0, 'C');
            $this->Cell($colWidth6, 8, '', $border, 0, 'C');
            $this->Cell($colWidth7, 8, '', $border, 1, 'C');
        }

        $this->Ln(5);

        // Signature area
        $this->Ln(10);
        $this->Cell(80, 0, '', 'T', 0, 'C');
        $this->Cell(30, 5, '', 0, 0);
        $this->Cell(80, 0, '', 'T', 1, 'C');
        $this->Cell(80, 5, __('Firma del Enfermero(a)'), 0, 0, 'C');
        $this->Cell(30, 5, '', 0, 0);
        $this->Cell(80, 5, __('Fecha'), 0, 1, 'C');
    }
}
