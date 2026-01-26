<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\Infirmary;
use App\Pdfs\PdfInterface;
use Carbon\Carbon;
use Classes\PDF;

class BasicHealthReportPdf extends PDF implements PdfInterface
{
    private Student $student;
    private ?Infirmary $infirmary;
    private ?int $age;

    public function __construct(Student $student)
    {
        parent::__construct('P', 'mm', 'Letter');

        $this->student = $student;
        $this->infirmary = $student->infirmary;

        // Calculate age
        if ($this->student->fecha) {
            $birthDate = Carbon::parse($this->student->fecha);
            $this->age = $birthDate->age;
        } else {
            $this->age = null;
        }

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Informe de Enfermería') . ' - ' . $student->fullName, true);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderStudentInfo();
        $this->renderVaccinations();
        $this->renderPhysicalMeasurements();
        $this->renderHealthConditions();
        $this->renderBehavior();

        $this->AddPage();
        $this->renderPhysicalExamination();
        $this->renderFamilyMedicalHistory();
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('DEPARTAMENTO DE ENFERMERÍA'), 0, 1, 'C');
        $this->Cell(0, 6, __('Información Básica de Salud'), 0, 1, 'C');
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
        $this->Cell(90, 5, $this->student->fecha ? $this->formatDate($this->student->fecha) : '', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Edad') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->age ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Lugar de Nacimiento') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->lugar_nac ?? '', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Género') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->genero ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Madre') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->family->madre ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Padre') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->family->padre ?? '', 0, 1);

        $this->Ln(3);
    }

    private function renderVaccinations(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Vacunas'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Vacunas al día') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 5, ($this->infirmary->va_dia ?? '') == 'Si' ? __('Sí') : 'No', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Refuerzos') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, ($this->infirmary->refuerzos ?? '') == 'Si' ? __('Sí') : 'No', 0, 1);

        $this->Ln(2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Vacunas Recibidas') . ':', 0, 1);

        if ($this->infirmary) {
            $vaccines = $this->getVaccinationsReceived();
            if (count($vaccines) > 0) {
                $this->SetFont('Arial', '', 8);
                $colCount = 0;
                foreach ($vaccines as $vaccine) {
                    $this->Cell(47, 4, '- ' . $vaccine, 0, $colCount % 4 == 3 ? 1 : 0);
                    $colCount++;
                }
                if ($colCount % 4 != 0) {
                    $this->Ln();
                }
            } else {
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 4, __('Ninguna vacuna registrada'), 0, 1);
            }
        }

        $this->Ln(3);
    }

    private function getVaccinationsReceived(): array
    {
        $vaccineLabels = [
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
            'vac14' => 'COVID-19',
            'vac15' => 'Otro 1',
            'vac16' => 'Otro 2',
            'vac17' => 'Otro 3',
            'vac18' => 'Otro 4',
        ];

        $received = [];
        foreach ($vaccineLabels as $field => $label) {
            if (!empty($this->infirmary->$field) && $this->infirmary->$field === 'Si') {
                $received[] = $label;
            }
        }
        return $received;
    }

    private function renderPhysicalMeasurements(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Medidas Físicas'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Peso') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 5, ($this->infirmary->peso ?? '') . ' lbs', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Estatura') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, ($this->infirmary->estatura ?? ''), 0, 1);

        $this->Ln(3);
    }

    private function renderHealthConditions(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Condiciones de Salud'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->cond_salud)) {
            $this->MultiCell(0, 4, $this->infirmary->cond_salud, 0, 'L');
        } else {
            $this->Cell(0, 4, __('Ninguna condición registrada'), 0, 1);
        }

        $this->Ln(2);

        // Daily Medication
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Medicamento de uso diario') . ':', 0, 1);
        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->med_usodi)) {
            $this->MultiCell(0, 4, $this->infirmary->med_usodi, 0, 'L');

            if (!empty($this->infirmary->dosis) || !empty($this->infirmary->frec)) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(30, 4, __('Dosis') . ':', 0, 0);
                $this->Cell(70, 4, $this->infirmary->dosis ?? '', 0, 0);
                $this->Cell(30, 4, __('Frecuencia') . ':', 0, 0);
                $this->Cell(0, 4, $this->infirmary->frec ?? '', 0, 1);
            }
        } else {
            $this->Cell(0, 4, __('Ningún medicamento registrado'), 0, 1);
        }

        $this->Ln(3);
    }

    private function renderBehavior(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Comportamiento'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->comportamiento)) {
            $this->MultiCell(0, 4, $this->infirmary->comportamiento, 0, 'L');
        } else {
            $this->Cell(0, 4, __('Sin observaciones'), 0, 1);
        }

        $this->Ln(3);
    }

    private function renderPhysicalExamination(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('EXAMEN FÍSICO'), 0, 1, 'C');
        $this->Ln(3);

        // Skin
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Piel'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $skinConditions = [];
        if (!empty($this->infirmary->piel1)) $skinConditions[] = $this->infirmary->piel1;
        if (!empty($this->infirmary->piel2)) $skinConditions[] = $this->infirmary->piel2;
        if (!empty($this->infirmary->piel3)) $skinConditions[] = $this->infirmary->piel3;
        if (!empty($this->infirmary->piel4)) $skinConditions[] = $this->infirmary->piel4;
        if (!empty($this->infirmary->piel5)) $skinConditions[] = $this->infirmary->piel5;

        if (count($skinConditions) > 0) {
            $this->Cell(0, 4, implode(', ', $skinConditions), 0, 1);
        } else {
            $this->Cell(0, 4, __('Normal'), 0, 1);
        }

        $this->Cell(50, 5, __('Cicatrices') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->cicatrices ?? __('No'), 0, 1);
        $this->Cell(50, 5, __('Quemaduras') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->quemaduras ?? __('No'), 0, 1);
        $this->Ln(2);

        // Senses
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Sentidos'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Visión') . ':', 0, 0);
        $this->Cell(50, 5, $this->infirmary->vision ?? '', 0, 0);
        $this->Cell(30, 5, __('Espejuelos') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->espejuelos ?? __('No'), 0, 1);

        $this->Cell(50, 5, __('Audición') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->audicion ?? '', 0, 1);

        $this->Cell(50, 5, __('Nasal') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->nasal ?? '', 0, 1);
        $this->Ln(2);

        // Dentition
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Dentadura'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->infirmary->dentadura ?? __('Normal'), 0, 1);
        $this->Ln(2);

        // Respiratory
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Sistema Respiratorio'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Respiración') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->respiracion ?? __('Normal'), 0, 1);
        $this->Cell(50, 5, __('Asma') . ':', 0, 0);
        $this->Cell(50, 5, $this->infirmary->asma ?? __('No'), 0, 0);
        $this->Cell(30, 5, __('Condritis') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->condritis ?? __('No'), 0, 1);
        $this->Cell(50, 5, __('Espasmos') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->espasmos ?? __('No'), 0, 1);
        $this->Ln(2);

        // Abdomen
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Abdomen'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Edema') . ':', 0, 0);
        $this->Cell(50, 5, $this->infirmary->ab_edema ?? __('No'), 0, 0);
        $this->Cell(30, 5, __('Herida') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->ab_herida ?? __('No'), 0, 1);
        $this->Cell(50, 5, __('Deformidad') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->ab_deformidad ?? __('No'), 0, 1);
        if (!empty($this->infirmary->desc1)) {
            $this->Cell(50, 5, __('Descripción') . ':', 0, 0);
            $this->MultiCell(0, 5, $this->infirmary->desc1, 0, 'L');
        }
        $this->Ln(2);

        // Extremities
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Extremidades'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Edema') . ':', 0, 0);
        $this->Cell(50, 5, $this->infirmary->ex_edema ?? __('No'), 0, 0);
        $this->Cell(30, 5, __('Herida') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->ex_herida ?? __('No'), 0, 1);
        $this->Cell(50, 5, __('Deformidad') . ':', 0, 0);
        $this->Cell(50, 5, $this->infirmary->ex_deformidad ?? __('No'), 0, 0);
        $this->Cell(30, 5, __('Prótesis') . ':', 0, 0);
        $this->Cell(0, 5, $this->infirmary->ex_protesis ?? __('No'), 0, 1);
        if (!empty($this->infirmary->desc2)) {
            $this->Cell(50, 5, __('Descripción') . ':', 0, 0);
            $this->MultiCell(0, 5, $this->infirmary->desc2, 0, 'L');
        }
        $this->Ln(3);
    }

    private function renderFamilyMedicalHistory(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('HISTORIAL MÉDICO FAMILIAR'), 0, 1, 'C');
        $this->Ln(3);

        $familyHistory = $this->infirmary ? $this->infirmary->getFamilyHistoryArray() : [];

        // Table header
        $this->SetFont('Arial', 'B', 8);
        $this->Fill();
        $colWidth = 32;
        $this->Cell(45, 6, __('Condición'), 1, 0, 'C', true);
        $this->Cell($colWidth, 6, __('Estudiante'), 1, 0, 'C', true);
        $this->Cell($colWidth, 6, __('Padre'), 1, 0, 'C', true);
        $this->Cell($colWidth, 6, __('Madre'), 1, 0, 'C', true);
        $this->Cell($colWidth, 6, __('Hermano'), 1, 0, 'C', true);
        $this->Cell($colWidth, 6, __('Otro'), 1, 1, 'C', true);

        $conditions = [
            'heart_disease' => __('Enfermedad cardíaca'),
            'cancer' => __('Cáncer'),
            'diabetes' => __('Diabetes'),
            'hypertension' => __('Hipertensión'),
            'hypotension' => __('Hipotensión'),
            'hyperthyroidism' => __('Hipertiroidismo'),
            'hypothyroidism' => __('Hipotiroidismo'),
            'anemia' => __('Anemia'),
            'other' => __('Otro'),
        ];

        $this->SetFont('Arial', '', 8);
        foreach ($conditions as $key => $label) {
            $this->Cell(45, 5, $label, 1, 0, 'L');
            $history = $familyHistory[$key] ?? [];
            $this->Cell($colWidth, 5, ($history['student'] ?? '') === 'Si' ? __('Sí') : '', 1, 0, 'C');
            $this->Cell($colWidth, 5, ($history['father'] ?? '') === 'Si' ? __('Sí') : '', 1, 0, 'C');
            $this->Cell($colWidth, 5, ($history['mother'] ?? '') === 'Si' ? __('Sí') : '', 1, 0, 'C');
            $this->Cell($colWidth, 5, ($history['sibling'] ?? '') === 'Si' ? __('Sí') : '', 1, 0, 'C');
            $this->Cell($colWidth, 5, ($history['other'] ?? '') === 'Si' ? __('Sí') : '', 1, 1, 'C');
        }

        $this->Ln(3);

        // Comments
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Comentarios'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $comments = [];
        if (!empty($this->infirmary->com1)) $comments[] = $this->infirmary->com1;
        if (!empty($this->infirmary->com2)) $comments[] = $this->infirmary->com2;
        if (!empty($this->infirmary->com3)) $comments[] = $this->infirmary->com3;
        if (!empty($this->infirmary->com4)) $comments[] = $this->infirmary->com4;

        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $this->MultiCell(0, 4, '- ' . $comment, 0, 'L');
            }
        } else {
            $this->Cell(0, 4, __('Sin comentarios'), 0, 1);
        }
    }
}
