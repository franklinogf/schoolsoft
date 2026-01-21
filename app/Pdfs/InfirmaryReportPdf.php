<?php

namespace App\Pdfs;

use App\Models\Student;
use App\Pdfs\PdfInterface;
use Carbon\Carbon;
use Classes\PDF;

class InfirmaryReportPdf extends PDF implements PdfInterface
{
    private $student;
    private $infirmary;
    private $familyHistory;
    private $age;

    public function __construct(Student $student)
    {
        parent::__construct('P', 'mm', 'Letter');
        
        $this->student = $student;
        $this->infirmary = $student->infirmary;
        $this->familyHistory = $this->infirmary ? $this->infirmary->getFamilyHistoryArray() : [];
        
        // Calculate age
        if ($this->student->fecha) {
            $birthDate = Carbon::parse($this->student->fecha);
            $this->age = $birthDate->age;
        } else {
            $this->age = null;
        }
        
        $this->AliasNbPages();
        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle('Informe de Enfermería - ' . $student->fullName, true);
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
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Información del Estudiante'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Nombre:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->fullName, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Grado:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->grado, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Fecha de Nacimiento:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->fecha ? $this->student->fecha->format('Y-m-d') : '', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Edad:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->age ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Lugar de Nacimiento:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 5, $this->student->lugar_nac, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Género:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->genero, 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Madre:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->family->madre ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Padre:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->student->family->padre ?? '', 0, 1);

        $this->Ln(3);
    }

    private function renderVaccinations(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Vacunas'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Vacunas al día:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 5, ($this->infirmary->va_dia ?? '') == 'Si' ? __('Sí') : 'No', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Refuerzos:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, ($this->infirmary->refuerzos ?? '') == 'Si' ? __('Sí') : 'No', 0, 1);

        $this->Ln(2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Vacunas Recibidas:'), 0, 1);

        if ($this->infirmary) {
            $vaccines = $this->infirmary->getVaccinationsReceived();
            if (count($vaccines) > 0) {
                $this->SetFont('Arial', '', 8);
                $colCount = 0;
                foreach ($vaccines as $vaccine) {
                    $this->Cell(42, 4, '- ' . $vaccine, 0, $colCount % 4 == 3 ? 1 : 0);
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

    private function renderPhysicalMeasurements(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Medidas Físicas'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Peso:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(50, 5, ($this->infirmary->peso ?? '') . ' lbs', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(30, 5, __('Estatura:'), 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, ($this->infirmary->estatura ?? ''), 0, 1);

        $this->Ln(3);
    }

    private function renderHealthConditions(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
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
        $this->Cell(0, 5, __('Medicamento de uso diario:'), 0, 1);
        $this->SetFont('Arial', '', 9);
        if (!empty($this->infirmary->med_usodi)) {
            $this->MultiCell(0, 4, $this->infirmary->med_usodi, 0, 'L');
            
            if (!empty($this->infirmary->dosis) || !empty($this->infirmary->frec)) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(30, 4, __('Dosis:'), 0, 0);
                $this->Cell(70, 4, $this->infirmary->dosis ?? '', 0, 0);
                $this->Cell(30, 4, __('Frecuencia:'), 0, 0);
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
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Comportamiento'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', '', 9);
        $behaviors = [];
        if (($this->infirmary->com1 ?? '') == 'Si') $behaviors[] = __('Cooperador');
        if (($this->infirmary->com2 ?? '') == 'Si') $behaviors[] = __('No Cooperador');
        if (($this->infirmary->com3 ?? '') == 'Si') $behaviors[] = __('Agresivo');
        if (($this->infirmary->com4 ?? '') == 'Si') $behaviors[] = __('Otros');

        if (count($behaviors) > 0) {
            $this->Cell(0, 5, implode(', ', $behaviors), 0, 1);
        } else {
            $this->Cell(0, 5, __('No especificado'), 0, 1);
        }
    }

    private function renderPhysicalExamination(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Examen Físico'), 0, 1, 'L', true);
        $this->Ln(1);

        // Skin
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Piel:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $skinConditions = [];
        if (($this->infirmary->piel1 ?? '') == 'Si') $skinConditions[] = __('Seca');
        if (($this->infirmary->piel2 ?? '') == 'Si') $skinConditions[] = __('Rosada');
        if (($this->infirmary->piel3 ?? '') == 'Si') $skinConditions[] = __('Pálida');
        if (($this->infirmary->piel4 ?? '') == 'Si') $skinConditions[] = __('Sudorosa');
        if (($this->infirmary->piel5 ?? '') == 'Si') $skinConditions[] = __('Eritemas');
        if (($this->infirmary->cicatrices ?? '') == 'Si') $skinConditions[] = __('Cicatrices');
        if (($this->infirmary->quemaduras ?? '') == 'Si') $skinConditions[] = __('Quemaduras');
        $this->Cell(0, 4, count($skinConditions) > 0 ? implode(', ', $skinConditions) : __('Normal'), 0, 1);

        $this->Ln(1);
        // Head
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Cabeza:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $headConditions = [];
        if (($this->infirmary->vision ?? '') == 'Si') $headConditions[] = __('Pérdida de visión');
        if (($this->infirmary->audicion ?? '') == 'Si') $headConditions[] = __('Dificultad auditiva');
        if (($this->infirmary->nasal ?? '') == 'Si') $headConditions[] = __('Congestión nasal');
        $this->Cell(0, 4, count($headConditions) > 0 ? implode(', ', $headConditions) : __('Normal'), 0, 1);
        $this->Cell(50, 4, __('Espejuelos:'), 0, 0);
        $this->Cell(0, 4, ($this->infirmary->espejuelos ?? '') == 'Si' ? __('Sí') : 'No', 0, 1);

        $this->Ln(1);
        // Chest
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Pecho:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $chestConditions = [];
        if (($this->infirmary->respiracion ?? '') == 'Si') $chestConditions[] = __('Dificultad respiratoria');
        if (($this->infirmary->asma ?? '') == 'Si') $chestConditions[] = __('Asma');
        if (($this->infirmary->condritis ?? '') == 'Si') $chestConditions[] = __('Costo condritis');
        if (($this->infirmary->espasmos ?? '') == 'Si') $chestConditions[] = __('Espasmos');
        $this->Cell(0, 4, count($chestConditions) > 0 ? implode(', ', $chestConditions) : __('Normal'), 0, 1);

        $this->Ln(1);
        // Abdomen
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Abdomen:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $abdomConditions = [];
        if (($this->infirmary->ab_edema ?? '') == 'Si') $abdomConditions[] = __('Edema');
        if (($this->infirmary->ab_herida ?? '') == 'Si') $abdomConditions[] = __('Herida');
        if (($this->infirmary->ab_deformidad ?? '') == 'Si') $abdomConditions[] = __('Deformidad');
        $this->Cell(0, 4, count($abdomConditions) > 0 ? implode(', ', $abdomConditions) : __('Normal'), 0, 1);
        if (!empty($this->infirmary->desc1)) {
            $this->MultiCell(0, 4, __('Descripción: ') . $this->infirmary->desc1, 0, 'L');
        }

        $this->Ln(1);
        // Extremities
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Extremidades:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $extremConditions = [];
        if (($this->infirmary->ex_edema ?? '') == 'Si') $extremConditions[] = __('Edema');
        if (($this->infirmary->ex_herida ?? '') == 'Si') $extremConditions[] = __('Heridas');
        if (($this->infirmary->ex_deformidad ?? '') == 'Si') $extremConditions[] = __('Deformación');
        if (($this->infirmary->ex_protesis ?? '') == 'Si') $extremConditions[] = __('Prótesis');
        $this->Cell(0, 4, count($extremConditions) > 0 ? implode(', ', $extremConditions) : __('Normal'), 0, 1);
        if (!empty($this->infirmary->desc2)) {
            $this->MultiCell(0, 4, __('Descripción: ') . $this->infirmary->desc2, 0, 'L');
        }

        $this->Ln(3);
    }

    private function renderFamilyMedicalHistory(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 6, __('Historial de Enfermedad Familiar'), 0, 1, 'L', true);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(50, 5, __('Condición'), 1, 0, 'C', true);
        $this->Cell(25, 5, __('Estudiante'), 1, 0, 'C', true);
        $this->Cell(25, 5, __('Padre'), 1, 0, 'C', true);
        $this->Cell(25, 5, __('Madre'), 1, 0, 'C', true);
        $this->Cell(25, 5, __('Hermano'), 1, 0, 'C', true);
        $this->Cell(25, 5, __('Otro Fam.'), 1, 1, 'C', true);

        $diseases = [
            'heart_disease' => __('Enf del corazón'),
            'cancer' => __('Cáncer'),
            'diabetes' => __('Diabetes'),
            'hypertension' => __('Hipertensión'),
            'hypotension' => __('Hipotensión'),
            'hyperthyroidism' => __('Hipertiroidismo'),
            'hypothyroidism' => __('Hipotiroidismo'),
            'anemia' => __('Anemia'),
            'other' => __('Otros')
        ];

        $this->SetFont('Arial', '', 7);
        foreach ($diseases as $diseaseKey => $diseaseName) {
            $history = $this->familyHistory[$diseaseKey] ?? ['student' => '', 'father' => '', 'mother' => '', 'sibling' => '', 'other' => ''];
            
            $this->Cell(50, 5, $diseaseName, 1, 0, 'L');
            $this->Cell(25, 5, $history['student'] == 'Si' ? 'X' : '', 1, 0, 'C');
            $this->Cell(25, 5, $history['father'] == 'Si' ? 'X' : '', 1, 0, 'C');
            $this->Cell(25, 5, $history['mother'] == 'Si' ? 'X' : '', 1, 0, 'C');
            $this->Cell(25, 5, $history['sibling'] == 'Si' ? 'X' : '', 1, 0, 'C');
            $this->Cell(25, 5, $history['other'] == 'Si' ? 'X' : '', 1, 1, 'C');
        }
    }
}
