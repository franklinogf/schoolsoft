<?php

namespace App\Pdfs\Infirmary;

use App\Models\Admin;
use App\Models\Student;
use App\Models\IncompleteVaccine;
use App\Pdfs\PdfInterface;
use App\Services\SchoolService;
use Classes\PDF;
use Illuminate\Support\Collection;

class IncompleteVaccinesPdf extends PDF implements PdfInterface
{
    private Collection $records;
    private ?string $grade;
    private string $year;

    private array $vaccineLabels = [
        'vacuna1' => 'DPT',
        'vacuna2' => 'Polio',
        'vacuna3' => 'HIB',
        'vacuna4' => 'Hepatitis B',
        'vacuna5' => 'MMR',
        'vacuna6' => 'Varicela',
        'vacuna7' => 'PCV',
        'vacuna8' => 'Hepatitis A',
        'vacuna9' => 'Meningococo',
        'vacuna10' => 'Rotavirus',
        'vacuna11' => 'Influenza',
        'vacuna12' => 'Tdap',
        'vacuna13' => 'HPV',
    ];

    public function __construct(?string $grade = null, string $orderBy = 'apellidos')
    {
        parent::__construct('L', 'mm', 'Letter');

        $this->grade = $grade;
        $this->year = SchoolService::getCurrentYear();

        $this->loadRecords($orderBy);

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Vacunas Incompletas'), true);
    }

    private function loadRecords(string $orderBy): void
    {
        $query = IncompleteVaccine::query()
            ->with('student')
            ->where('year', $this->year);

        if ($this->grade && $this->grade !== 'Todos') {
            $query->where('curso', $this->grade);
        }

        $this->records = $query->get();

        // Sort by student name
        if ($orderBy === 'Grados') {
            $this->records = $this->records->sortBy([
                ['curso', 'asc'],
                fn($a, $b) => ($a->student?->apellidos ?? '') <=> ($b->student?->apellidos ?? '')
            ])->values();
        } else {
            $this->records = $this->records->sortBy(
                fn($r) => $r->student?->apellidos ?? ''
            )->values();
        }
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
        $this->Cell(0, 8, __('ESTUDIANTES CON VACUNAS INCOMPLETAS'), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, __('Año Escolar') . ': ' . $this->year, 0, 1, 'C');

        if ($this->grade && $this->grade !== 'Todos') {
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
        foreach ($this->records as $record) {
            $student = $record->student;

            $this->Cell($nameWidth, 5, $this->truncate($student?->fullName ?? '', 32), 1);
            $this->Cell($gradeWidth, 5, $record->curso, 1, 0, 'C');

            foreach (array_keys($this->vaccineLabels) as $field) {
                // Mark with X if vaccine is incomplete/missing
                $value = !empty($record->$field) ? 'X' : '';
                $this->Cell($checkWidth, 5, $value, 1, 0, 'C');
            }
            $this->Ln();
        }

        if ($this->records->isEmpty()) {
            $totalWidth = $nameWidth + $gradeWidth + (count($this->vaccineLabels) * $checkWidth);
            $this->SetFont('Arial', 'I', 9);
            $this->Cell($totalWidth, 8, __('No se encontraron estudiantes con vacunas incompletas'), 1, 1, 'C');
        }

        $this->Ln(3);
    }

    private function renderSummary(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('Total de estudiantes') . ': ' . $this->records->count(), 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, 'X = ' . __('Vacuna pendiente o incompleta'), 0, 1);
    }

    private function truncate(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3) . '...';
    }
}
