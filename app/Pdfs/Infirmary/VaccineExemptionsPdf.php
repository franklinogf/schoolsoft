<?php

namespace App\Pdfs\Infirmary;

use App\Models\Admin;
use App\Models\Student;
use App\Models\InfirmaryCertification;
use App\Pdfs\PdfInterface;
use App\Services\SchoolService;
use Classes\PDF;

class VaccineExemptionsPdf extends PDF implements PdfInterface
{
    private Student $student;
    private ?InfirmaryCertification $certification;
    private string $year;

    public function __construct(Student $student)
    {
        parent::__construct('P', 'mm', 'Letter');

        $this->student = $student;
        $this->year = SchoolService::getCurrentYear();

        $this->certification = InfirmaryCertification::where('ss', $student->ss)
            ->where('year', $this->year)
            ->first();

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Excenciones de Vacunas') . ' - ' . $student->fullName, true);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderStudentInfo();
        $this->renderCertifications();
        $this->renderDeclarations();
        $this->renderTestInfo();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('EXCENCIONES DE VACUNAS'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, __('Año Escolar') . ': ' . $this->year, 0, 1, 'C');
        $this->Ln(5);
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
        $this->Cell(0, 5, $this->student->fullName, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Grado') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->grado, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Seguro Social') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->ss, 0, 1);

        $this->Ln(3);
    }

    private function renderCertifications(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Certificaciones'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);

        $this->Cell(80, 5, __('Certificación Médica (PVAC-3)') . ':', 0, 0);
        $this->drawCheckbox($this->certification?->cert1 === 'Si');
        $this->Cell(20, 5, __('Sí'), 0, 0);
        $this->drawCheckbox($this->certification?->cert1 !== 'Si');
        $this->Cell(0, 5, __('No'), 0, 1);

        $this->Cell(80, 5, __('Exención Religiosa (PVAC-4)') . ':', 0, 0);
        $this->drawCheckbox($this->certification?->cert2 === 'Si');
        $this->Cell(20, 5, __('Sí'), 0, 0);
        $this->drawCheckbox($this->certification?->cert2 !== 'Si');
        $this->Cell(0, 5, __('No'), 0, 1);

        $this->Ln(3);
    }

    private function renderDeclarations(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Declaraciones'), 0, 1, 'L', true);
        $this->Ln(1);

        $declarations = [
            'dec1' => __('El estudiante tiene contraindicación médica'),
            'dec2' => __('El estudiante tiene alergia a componentes'),
            'dec3' => __('El estudiante tiene enfermedad inmunológica'),
            'dec4' => __('Razones religiosas'),
            'dec5' => __('Razones filosóficas'),
            'dec6' => __('El padre/tutor se opone'),
            'dec7' => __('Otra razón médica'),
            'dec8' => __('Documentación pendiente'),
        ];

        $this->SetFont('Arial', '', 9);
        foreach ($declarations as $field => $label) {
            $this->drawCheckbox($this->certification?->$field === 'Si');
            $this->Cell(5, 5, '', 0, 0);
            $this->Cell(0, 5, $label, 0, 1);
        }

        $this->Ln(3);
    }

    private function renderTestInfo(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información de Pruebas'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);

        $testFields = [
            'tes1' => __('Prueba de Tuberculosis (PPD)'),
            'tes2' => __('Fecha de la prueba'),
            'tes3' => __('Resultado'),
            'tes4' => __('Radiografía de pecho'),
            'tes6' => __('Fecha de radiografía'),
            'tes7' => __('Resultado de radiografía'),
        ];

        foreach ($testFields as $field => $label) {
            $this->Cell(80, 5, $label . ':', 0, 0);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(0, 5, $this->certification?->$field ?? '', 0, 1);
            $this->SetFont('Arial', '', 9);
        }

        $this->Ln(5);

        // Signature line
        $this->Ln(15);
        $this->Cell(80, 0, '', 'T', 0, 'C');
        $this->Cell(30, 5, '', 0, 0);
        $this->Cell(80, 0, '', 'T', 1, 'C');
        $this->Cell(80, 5, __('Firma del Padre/Tutor'), 0, 0, 'C');
        $this->Cell(30, 5, '', 0, 0);
        $this->Cell(80, 5, __('Fecha'), 0, 1, 'C');
    }
}
