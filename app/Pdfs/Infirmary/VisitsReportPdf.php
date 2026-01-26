<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\InfirmaryVisit;
use App\Pdfs\PdfInterface;
use Classes\PDF;
use Illuminate\Support\Collection;

class VisitsReportPdf extends PDF implements PdfInterface
{
    private Collection $visits;
    private ?Student $student;
    private ?string $grade;
    private string $fromDate;
    private string $toDate;

    public function __construct(
        ?Student $student = null,
        ?string $grade = null,
        string $fromDate = '',
        string $toDate = ''
    ) {
        parent::__construct('L', 'mm', 'Letter');

        $this->student = $student;
        $this->grade = $grade;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

        $this->loadVisits();

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Informe de Visitas a Enfermería'), true);
    }

    private function loadVisits(): void
    {
        $query = InfirmaryVisit::query()
            ->with('student');

        if ($this->fromDate && $this->toDate) {
            $query->whereBetween('fecha', [$this->fromDate, $this->toDate]);
        }

        if ($this->student) {
            $query->where('ss', $this->student->ss);
        }

        if ($this->grade && $this->grade !== 'Todos') {
            $query->whereHas('student', fn($q) => $q->where('grado', $this->grade));
        }

        $this->visits = $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->get();
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderFilters();
        $this->renderTable();
        $this->renderSummary();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('INFORME DE VISITAS A ENFERMERÍA'), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderFilters(): void
    {
        $this->SetFont('Arial', '', 10);

        if ($this->fromDate && $this->toDate) {
            $this->Cell(0, 5, __('Período') . ': ' . $this->formatDate($this->fromDate) . ' - ' . $this->formatDate($this->toDate), 0, 1);
        }

        if ($this->student) {
            $this->Cell(0, 5, __('Estudiante') . ': ' . $this->student->fullName, 0, 1);
        }

        if ($this->grade && $this->grade !== 'Todos') {
            $this->Cell(0, 5, __('Grado') . ': ' . $this->grade, 0, 1);
        }

        $this->Ln(3);
    }

    private function renderTable(): void
    {
        // Table headers
        $this->SetFont('Arial', 'B', 9);
        $this->Fill();
        $this->Cell(22, 6, __('Fecha'), 1, 0, 'C', true);
        $this->Cell(15, 6, __('Hora'), 1, 0, 'C', true);
        $this->Cell(60, 6, __('Estudiante'), 1, 0, 'C', true);
        $this->Cell(15, 6, __('Grado'), 1, 0, 'C', true);
        $this->Cell(60, 6, __('Razón'), 1, 0, 'C', true);
        $this->Cell(60, 6, __('Tratamiento'), 1, 0, 'C', true);
        $this->Cell(25, 6, __('Notificación'), 1, 1, 'C', true);

        // Table rows
        $this->SetFont('Arial', '', 8);
        foreach ($this->visits as $visit) {
            $this->Cell(22, 5, $visit->fecha ? $this->formatDate($visit->fecha) : '', 1);
            $this->Cell(15, 5, $visit->hora ?? '', 1, 0, 'C');
            $this->Cell(60, 5, $this->truncate($visit->student?->fullName ?? '', 35), 1);
            $this->Cell(15, 5, $visit->student?->grado ?? '', 1, 0, 'C');
            $this->Cell(60, 5, $this->truncate($visit->razon ?? '', 35), 1);
            $this->Cell(60, 5, $this->truncate($visit->tratamiento ?? '', 35), 1);
            $this->Cell(25, 5, $visit->notif_padres ?? '', 1, 1, 'C');
        }

        if ($this->visits->isEmpty()) {
            $this->SetFont('Arial', 'I', 9);
            $this->Cell(257, 8, __('No se encontraron visitas en el período seleccionado'), 1, 1, 'C');
        }

        $this->Ln(3);
    }

    private function renderSummary(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('Total de visitas') . ': ' . $this->visits->count(), 0, 1);
    }

    private function truncate(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3) . '...';
    }
}
