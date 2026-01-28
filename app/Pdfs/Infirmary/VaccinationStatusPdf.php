<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\Infirmary;
use App\Pdfs\PdfInterface;
use Classes\PDF;
use Illuminate\Support\Collection;

class VaccinationStatusPdf extends PDF implements PdfInterface
{
    private Collection $students;
    private ?string $grade;

    private array $vaccineLabels = [
        'vac1' => 'DPT',
        'vac2' => 'Polio',
        'vac3' => 'HIB',
        'vac4' => 'Hepatitis B',
        'vac5' => 'MMR',
        'vac6' => 'Varicela',
        'vac7' => 'PCV',
        'vac8' => 'Hepatitis A',
        'vac9' => 'Meningococo',
        'vac10' => 'Rotavirus',
        'vac11' => 'Influenza',
        'vac12' => 'Tdap',
        'vac13' => 'HPV',
    ];

    public function __construct(?string $grade = null, string $orderBy = 'apellidos')
    {
        parent::__construct('L', 'mm', 'Letter');

        $this->grade = $grade;

        $this->loadStudents($orderBy);

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Estado de Vacunación'), true);
    }

    private function loadStudents(string $orderBy): void
    {
        $query = Student::query()
            ->with('infirmary')
            ->whereHas('infirmary');

        if ($this->grade && $this->grade !== 'Todos') {
            $query->where('grado', $this->grade);
        }

        if ($orderBy === 'Grados') {
            $query->orderBy('grado')->orderBy('apellidos');
        } else {
            $query->orderBy('apellidos');
        }

        $this->students = $query->get();
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderTable();
        $this->renderSummary();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('ESTADO DE VACUNACIÓN'), 0, 1, 'C');

        if ($this->grade && $this->grade !== 'Todos') {
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, __('Grado') . ': ' . $this->grade, 0, 1, 'C');
        }

        $this->Ln(3);
    }

    private function renderTable(): void
    {
        // Calculate column widths
        $nameWidth = 55;
        $gradeWidth = 12;
        $checkWidth = 15;

        // Table headers
        $this->SetFont('Arial', 'B', 7);
        $this->Fill();
        $this->Cell($nameWidth, 8, __('Estudiante'), 1, 0, 'C', true);
        $this->Cell($gradeWidth, 8, __('Grado'), 1, 0, 'C', true);

        foreach ($this->vaccineLabels as $label) {
            $this->Cell($checkWidth, 8, $label, 1, 0, 'C', true);
        }
        $this->Ln();

        // Table rows
        $this->SetFont('Arial', '', 7);
        foreach ($this->students as $student) {
            $infirmary = $student->infirmary;

            $this->Cell($nameWidth, 5, $this->truncate($student->fullName, 32), 1);
            $this->Cell($gradeWidth, 5, $student->grado, 1, 0, 'C');

            foreach (array_keys($this->vaccineLabels) as $field) {
                $value = $infirmary?->$field === 'Si' ? 'X' : '';
                $this->Cell($checkWidth, 5, $value, 1, 0, 'C');
            }
            $this->Ln();
        }

        if ($this->students->isEmpty()) {
            $totalWidth = $nameWidth + $gradeWidth + (count($this->vaccineLabels) * $checkWidth);
            $this->SetFont('Arial', 'I', 9);
            $this->Cell($totalWidth, 8, __('No se encontraron estudiantes'), 1, 1, 'C');
        }

        $this->Ln(3);
    }

    private function renderSummary(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('Total de estudiantes') . ': ' . $this->students->count(), 0, 1);

        // Count students with all vaccines
        $completeCount = $this->students->filter(function ($student) {
            return $student->infirmary?->va_dia === 'Si';
        })->count();

        $this->Cell(0, 5, __('Vacunas al día') . ': ' . $completeCount, 0, 1);
    }

    private function truncate(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3) . '...';
    }
}
