<?php

namespace App\Pdfs\Infirmary;

use App\Models\Student;
use App\Models\DiabetesInfo;
use App\Models\DiabetesInsulin;
use App\Models\DiabetesExercise;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class DiabetesReportPdf extends PDF implements PdfInterface
{
    private Student $student;
    private ?DiabetesInfo $diabetesInfo;
    private ?DiabetesInsulin $diabetesInsulin;
    private ?DiabetesExercise $diabetesExercise;
    private int $reportType;

    /**
     * @param Student $student
     * @param int $reportType 1 = Info General, 2 = Insulina, 3 = Ejercicio
     */
    public function __construct(Student $student, int $reportType = 1)
    {
        parent::__construct('P', 'mm', 'Letter');

        $this->student = $student;
        $this->reportType = $reportType;

        $this->diabetesInfo = DiabetesInfo::where('ss', $student->ss)->first();
        $this->diabetesInsulin = DiabetesInsulin::where('ss', $student->ss)->first();
        $this->diabetesExercise = DiabetesExercise::where('ss', $student->ss)->first();

        $this->SetAutoPageBreak(true, 15);
        $this->SetTitle(__('Informe de Diabetes') . ' - ' . $student->fullName, true);
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderTitle();
        $this->renderStudentInfo();

        switch ($this->reportType) {
            case 1:
                $this->renderGeneralInfo();
                break;
            case 2:
                $this->renderInsulinInfo();
                break;
            case 3:
                $this->renderExerciseInfo();
                break;
        }
    }

    private function renderTitle(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, __('INFORME DE DIABETES'), 0, 1, 'C');

        $reportTypes = [
            1 => __('Información General'),
            2 => __('Información de Insulina'),
            3 => __('Ejercicio y Deportes'),
        ];

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 6, $reportTypes[$this->reportType] ?? '', 0, 1, 'C');
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

        $this->Ln(3);
    }

    private function renderGeneralInfo(): void
    {
        if (!$this->diabetesInfo) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, __('No hay información de diabetes registrada'), 0, 1, 'C');
            return;
        }

        // Type of Diabetes
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Tipo de Diabetes'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->diabetesInfo->diabetes ?? __('No especificado'), 0, 1);
        $this->Ln(3);

        // Doctor Information
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información del Médico'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Doctor') . ':', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, $this->diabetesInfo->doctor ?? '', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, __('Dirección') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInfo->direccion ?? '', 0, 1);

        $this->Cell(50, 5, __('Calle') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInfo->calle ?? '', 0, 1);

        $this->Cell(50, 5, __('Pueblo') . ':', 0, 0);
        $this->Cell(50, 5, $this->diabetesInfo->pueblo ?? '', 0, 0);
        $this->Cell(30, 5, __('Código Postal') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInfo->postal ?? '', 0, 1);

        $this->Cell(50, 5, __('Teléfono del Doctor') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInfo->tel_doc ?? '', 0, 1);

        $this->Cell(50, 5, __('Teléfono de Emergencia') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInfo->tel_emer ?? '', 0, 1);
        $this->Ln(3);

        // Important Dates
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Fechas Importantes'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        if ($this->diabetesInfo->fecha1) {
            $this->Cell(50, 5, __('Fecha 1') . ':', 0, 0);
            $this->Cell(0, 5, $this->formatDate($this->diabetesInfo->fecha1), 0, 1);
        }
        if ($this->diabetesInfo->fecha2) {
            $this->Cell(50, 5, __('Fecha 2') . ':', 0, 0);
            $this->Cell(0, 5, $this->formatDate($this->diabetesInfo->fecha2), 0, 1);
        }
        if ($this->diabetesInfo->fecha3) {
            $this->Cell(50, 5, __('Fecha 3') . ':', 0, 0);
            $this->Cell(0, 5, $this->formatDate($this->diabetesInfo->fecha3), 0, 1);
        }
        $this->Ln(3);

        // Notification
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Notificación'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);
        if (!empty($this->diabetesInfo->notificacion)) {
            $this->MultiCell(0, 5, $this->diabetesInfo->notificacion, 0, 'L');
        } else {
            $this->Cell(0, 5, __('Sin notificaciones'), 0, 1);
        }
    }

    private function renderInsulinInfo(): void
    {
        if (!$this->diabetesInsulin) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, __('No hay información de insulina registrada'), 0, 1, 'C');
            return;
        }

        // Glucose Range
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Rango de Glucosa'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Rango objetivo') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->rango ?? '', 0, 1);

        $this->Cell(50, 5, __('Horarios de medición') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->horas ?? '', 0, 1);
        $this->Ln(3);

        // Insulin Information
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información de Insulina'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        // Insulin 1
        if (!empty($this->diabetesInsulin->ins1)) {
            $this->Cell(50, 5, __('Insulina 1') . ':', 0, 0);
            $this->Cell(60, 5, $this->diabetesInsulin->ins1, 0, 0);
            $this->Cell(30, 5, __('Nombre') . ': ' . ($this->diabetesInsulin->ins1_n ?? ''), 0, 0);
            $this->Cell(0, 5, __('Unidades') . ': ' . ($this->diabetesInsulin->ins1_u ?? ''), 0, 1);
        }

        // Insulin 2
        if (!empty($this->diabetesInsulin->ins2)) {
            $this->Cell(50, 5, __('Insulina 2') . ':', 0, 0);
            $this->Cell(60, 5, $this->diabetesInsulin->ins2, 0, 0);
            $this->Cell(30, 5, __('Nombre') . ': ' . ($this->diabetesInsulin->ins2_n ?? ''), 0, 0);
            $this->Cell(0, 5, __('Unidades') . ': ' . ($this->diabetesInsulin->ins2_u ?? ''), 0, 1);
        }

        // Insulin 3
        if (!empty($this->diabetesInsulin->ins3)) {
            $this->Cell(50, 5, __('Insulina 3') . ':', 0, 0);
            $this->Cell(60, 5, $this->diabetesInsulin->ins3, 0, 0);
            $this->Cell(30, 5, __('Nombre') . ': ' . ($this->diabetesInsulin->ins3_n ?? ''), 0, 0);
            $this->Cell(0, 5, __('Unidades') . ': ' . ($this->diabetesInsulin->ins3_u ?? ''), 0, 1);
        }
        $this->Ln(3);

        // Glucose Levels
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Niveles de Glucosa'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Glucosa 1') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->gluc1 ?? '', 0, 1);
        $this->Cell(50, 5, __('Glucosa 2') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->gluc2 ?? '', 0, 1);
        $this->Cell(50, 5, __('Glucosa 3') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->gluc3 ?? '', 0, 1);
        $this->Cell(50, 5, __('Glucosa (medición)') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->gluc_med ?? '', 0, 1);
        $this->Ln(3);

        // Symptoms
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Síntomas'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(50, 5, __('Hiperglucemia') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->hiper ?? '', 0, 1);
        $this->Cell(50, 5, __('Hipoglucemia') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesInsulin->hipo ?? '', 0, 1);
    }

    private function renderExerciseInfo(): void
    {
        if (!$this->diabetesExercise) {
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 10, __('No hay información de ejercicio registrada'), 0, 1, 'C');
            return;
        }

        // Exercise Information
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Información de Ejercicio'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(60, 5, __('Carbohidratos antes del ejercicio') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesExercise->carb ?? '', 0, 1);

        $this->Cell(60, 5, __('Tipo de actividad') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesExercise->actividad ?? '', 0, 1);
        $this->Ln(3);

        // Glucose Range
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Rango de Glucosa para Ejercicio'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(60, 5, __('Glucosa mínima') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesExercise->glucosa_min ?? '', 0, 1);
        $this->Cell(60, 5, __('Glucosa máxima') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesExercise->glucosa_max ?? '', 0, 1);
        $this->Ln(3);

        // Hypoglycemia during exercise
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Hipoglucemia durante el Ejercicio'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(60, 5, __('Síntomas') . ':', 0, 0);
        $this->MultiCell(0, 5, $this->diabetesExercise->sintomas_hipo ?? '', 0, 'L');

        $this->Cell(60, 5, __('Tratamiento') . ':', 0, 0);
        $this->MultiCell(0, 5, $this->diabetesExercise->tratamiento_hipo ?? '', 0, 'L');

        $this->Cell(60, 5, __('Dosis') . ':', 0, 0);
        $this->Cell(0, 5, $this->diabetesExercise->dosis ?? '', 0, 1);
        $this->Ln(3);

        // Hyperglycemia during exercise
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Hiperglucemia durante el Ejercicio'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);

        $this->Cell(60, 5, __('Síntomas') . ':', 0, 0);
        $this->MultiCell(0, 5, $this->diabetesExercise->sintomas_hiper ?? '', 0, 'L');

        $this->Cell(60, 5, __('Tratamiento') . ':', 0, 0);
        $this->MultiCell(0, 5, $this->diabetesExercise->tratamiento_hiper ?? '', 0, 'L');
        $this->Ln(3);

        // Sugar
        $this->SetFont('Arial', 'B', 11);
        $this->Fill();
        $this->Cell(0, 6, __('Azúcar'), 0, 1, 'L', true);
        $this->Ln(1);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->diabetesExercise->azucar ?? '', 0, 1);
    }
}
