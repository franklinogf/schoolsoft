<?php

namespace App\Pdfs\Plans;

use App\Models\Admin;
use App\Models\WeeklyPlan;
use Classes\PDF;

class WeeklyPlan1PDF extends PDF
{

    public function __construct(private WeeklyPlan $plan)
    {
        parent::__construct();
        $this->SetTitle(__("Plan Semanal 1") . " - " . $plan->tema, true);
        $this->SetAutoPageBreak(true, 15);
        $this->Fill();
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderHeader();
        $this->renderGeneralInfo();
        $this->renderStandards();
        $this->renderExpectations();
        $this->renderGeneralObjectives();
        $this->renderSpecificObjectives();
        $this->renderWeeklyActivities();
        $this->renderComments();
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, __("PLAN SEMANAL"), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderGeneralInfo(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("INFORMACIÓN GENERAL"), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        $school = Admin::primaryAdmin();
        $fields = [
            [__("Maestro(a)"), $this->plan->teacher->apellidos . ', ' . $this->plan->teacher->nombre],
            [__("Institución"), $school->colegio],
            [__("Clase"), $this->plan->clase],
            [__("Grado"), $this->plan->grado],
            [__("Tema"), $this->plan->tema],
            [__("Fechas"), $this->plan->fecha],
            [__("Lección"), $this->plan->leccion],
        ];

        foreach ($fields as [$label, $value]) {
            $this->Cell(50, 6, $label . ": ", 1, 0);
            $this->Cell(140, 6, $value, 1, 1);
        }

        $this->Ln(2);
    }

    private function renderStandards(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("ESTÁNDARES"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 5, $this->plan->est, 1, 'L');
        $this->Ln(2);
    }

    private function renderExpectations(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("EXPECTATIVAS"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 5, $this->plan->exp, 1, 'L');
        $this->Ln(2);
    }

    private function renderGeneralObjectives(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("OBJETIVOS GENERALES"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 5, $this->plan->obj_gen, 1, 'L');
        $this->Ln(2);
    }

    private function renderSpecificObjectives(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("OBJETIVOS ESPECÍFICOS - VERBOS DE REFERENCIA"), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 8);

        for ($i = 1; $i <= 4; $i++) {
            $nivelField = "nivel{$i}";
            $listField = "lst_v{$i}";
            if (!empty($this->plan->$nivelField) || !empty($this->plan->$listField)) {
                $this->Cell(50, 5, __("Nivel") . " {$i}: " . $this->plan->$nivelField, 1, 0);
                $this->Cell(140, 5, $this->plan->$listField, 1, 1);
            }
        }

        $this->Ln(2);
    }

    private function renderWeeklyActivities(): void
    {
        // Nueva página si es necesario
        if ($this->GetY() > 150) {
            $this->AddPage();
        }

        for ($i = 1; $i <= 5; $i++) {
            $this->renderDayActivities($i);
        }

        $this->Ln(2);
    }

    private function renderDayActivities(int $dayIndex): void
    {
        $weekDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $actField = "act{$dayIndex}";
        $matField = "mat{$dayIndex}";
        $iniField = "ini{$dayIndex}";
        $desField = "des{$dayIndex}";
        $cieField = "cie{$dayIndex}";
        $asseField = "asse{$dayIndex}";

        // Obtener arrays de materiales
        $matArray = $this->plan->getMaterialsArray($matField);
        $iniArray = $this->plan->getMaterialsArray($iniField);
        $desArray = $this->plan->getMaterialsArray($desField);
        $cieArray = $this->plan->getMaterialsArray($cieField);
        $asseArray = $this->plan->getMaterialsArray($asseField);

        // Campos de "otros"
        $otros_m = "otros_m{$dayIndex}";
        $otros_i = "otros_i{$dayIndex}";
        $otros_d = "otros_d{$dayIndex}";
        $otros_c = "otros_c{$dayIndex}";
        $otros_a = "otros_a{$dayIndex}";

        // Combinar materiales con "otros"
        $materiales = $this->combineWithOthers($matArray, $this->plan->$otros_m);
        $inicio = $this->combineWithOthers($iniArray, $this->plan->$otros_i);
        $desarrollo = $this->combineWithOthers($desArray, $this->plan->$otros_d);
        $cierre = $this->combineWithOthers($cieArray, $this->plan->$otros_c);
        $assessment = $this->combineWithOthers($asseArray, $this->plan->$otros_a);

        // Día y Fase
        $this->SetFont('Arial', 'B', 9);
        $dayName = $weekDays[$dayIndex - 1];
        $this->Cell(0, 6, $dayName . " - Fase: " . $this->plan->$actField, 1, 1, 'C', true);

        // Renderizar secciones si tienen contenido
        $this->renderSection(__("MATERIALES"), $materiales);
        $this->renderSection(__("INICIO"), $inicio);
        $this->renderSection(__("DESARROLLO"), $desarrollo);
        $this->renderSection(__("CIERRE"), $cierre);
        $this->renderSection(__("ASSESSMENT"), $assessment);

        $this->Ln(3);
    }

    private function combineWithOthers(array $items, ?string $otros): string
    {
        $result = implode(", ", $items);
        if (!empty($otros)) {
            $result .= " | " . $otros;
        }
        return ltrim($result, ' | ');
    }

    private function renderSection(string $title, string $content): void
    {
        if (!empty($content)) {
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, 5, $title, 1, 1, 'L', true);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell(0, 5, $content, 1, 'L');
        }
    }

    private function renderComments(): void
    {
        if (!empty($this->plan->coment)) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 6, __("COMENTARIOS"), 1, 1, 'C', true);
            $this->SetFont('Arial', '', 8);
            $this->MultiCell(0, 5, $this->plan->coment, 1, 'L');
        }
    }
}
