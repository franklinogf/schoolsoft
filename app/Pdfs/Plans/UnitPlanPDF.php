<?php

namespace App\Pdfs\Plans;

use App\Models\UnitPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class UnitPlanPDF extends PDF implements PdfInterface
{
    public function __construct(private UnitPlan $plan)
    {
        parent::__construct();
        $this->SetTitle(__("Plan de Unidad") . " - " . $plan->titulo, true);
        $this->SetAutoPageBreak(true, 15);
    }

    public function generate(): void
    {
        $this->renderPage1();
        $this->renderPage2();
        $this->renderPage3();
    }

    private function renderPage1(): void
    {
        $this->AddPage();
        $this->renderHeader();
        $this->renderGeneralInfo();
        $this->renderTransversalThemes();
        $this->renderIntegration();
        $this->renderStandardsAndGoal();
        $this->renderStage1();
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, __('PLAN DE UNIDAD'), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderGeneralInfo(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $tituloLabel = __('TÍTULO DE LA UNIDAD:');
        $this->Cell($this->GetStringWidth($tituloLabel . ' ') + 3, 6, $tituloLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(70, 6, $this->plan->titulo, 'B', 0);

        $this->SetFont('Arial', 'B', 10);
        $fechaLabel = __('FECHA:');
        $this->Cell($this->GetStringWidth($fechaLabel . ' ') + 3, 6, $fechaLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(25, 6, $this->plan->fecha ?? '', 'B', 0, 'C');

        $this->SetFont('Arial', 'B', 10);
        $duracionLabel = __('DURACIÓN:');
        $this->Cell($this->GetStringWidth($duracionLabel . ' '), 6, $duracionLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(15, 6, $this->plan->duracion . ' ' . __('semanas'), 'B', 1, 'C');

        $this->Ln(2);

        $this->SetFont('Arial', 'B', 10);
        $maestroLabel = __('MAESTRO(A):');
        $this->Cell($this->GetStringWidth($maestroLabel . ' ') + 3, 6, $maestroLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 6, $this->plan->profesor, 'B', 0);

        $this->SetFont('Arial', 'B', 10);
        $materiaLabel = __('MATERIA:');
        $this->Cell($this->GetStringWidth($materiaLabel . ' ') + 3, 6, $materiaLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 6, $this->plan->materia, 'B', 0, 'C');

        $this->SetFont('Arial', 'B', 10);
        $gradoLabel = __('GRADO:');
        $this->Cell($this->GetStringWidth($gradoLabel . ' ') + 3, 6, $gradoLabel, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 6, substr($this->plan->materia, 0, 3), 'B', 1, 'C');

        $this->Ln(3);
    }

    private function renderTransversalThemes(): void
    {
        $transversales = [
            'Educar para el amor al prójimo',
            'Educar para la transcendencia',
            'Educación para la promoción de la vida',
            'Educar para el liderazgo moral',
            'Educación para la ciudadanía consciente y activa',
            'Educar para la comunión',
            'Educar para la conservación del medio ambiente'
        ];

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('TEMA TRANSVERSAL:'), 0, 1);
        $this->SetFont('Arial', '', 9);

        foreach ($transversales as $i => $label) {
            $index = $i + 1;
            $this->drawCheckbox($this->plan->{"transversal{$index}"} === 'si', y: 1.2);
            $this->Cell(3);
            $this->Cell(0, 5, $label, 0, 1);
        }

        $this->Ln(3);
    }

    private function renderIntegration(): void
    {
        $integraciones = [
            'Español',
            'Inglés',
            'Estudios Sociales',
            'Ciencia',
            'Matemáticas',
            'Bellas Artes',
            'Educación Física',
            'Salud Escolar',
            'Tecnología'
        ];

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __('INTEGRACIÓN:'), 0, 1);
        $this->SetFont('Arial', '', 9);

        $halfCount = ceil(count($integraciones) / 2);
        for ($i = 0; $i < $halfCount; $i++) {
            $index1 = $i + 1;
            $this->drawCheckbox($this->plan->{"integracion{$index1}"} === 'si', y: 1.2);
            $this->Cell(3);
            $this->Cell(60, 5, $integraciones[$i]);

            if (isset($integraciones[$i + $halfCount])) {
                $index2 = $i + $halfCount + 1;
                $this->drawCheckbox($this->plan->{"integracion{$index2}"} === 'si', y: 1.2);
                $this->Cell(3);
                $this->Cell(0, 5, $integraciones[$i + $halfCount], 0, 1);
            } else {
                $this->Ln();
            }
        }

        $this->Ln(5);
    }

    private function renderStandardsAndGoal(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $estandaresLabel = __('Estándares: a)');
        $this->Cell($this->GetStringWidth($estandaresLabel . ' ') + 3, 5, $estandaresLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, 5, $this->plan->estandar1 ?? '', 'B', 0);
        $this->Cell(10);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($this->GetStringWidth('b) ') + 3, 5, 'b)', 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, 5, $this->plan->estandar2 ?? '', 'B', 1);

        $this->Ln(2);

        $this->SetFont('Arial', 'B', 9);
        $metaLabel = __('Meta:');
        $this->Cell($this->GetStringWidth($metaLabel . ' ') + 3, 5, $metaLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->plan->meta ?? '', 'B', 1);

        $this->Ln(5);
    }

    private function renderStage1(): void
    {
        $this->SetFillColor(200);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __('ETAPA 1 - RESULTADOS ESPERADOS'), 1, 1, 'C', true);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __('RESUMEN DE LA UNIDAD'), 1, 1, 'C', true);
        $this->SetFillColor(255);

        $resumenY = $this->GetY();
        $this->Rect($this->GetX(), $resumenY, 190, 20);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(190, 4, $this->plan->resumen ?? '');
        $this->SetY($resumenY + 20);

        $this->Ln(2);

        $this->renderEssentialQuestionsAndUnderstanding();
        $this->renderTransferObjectives();
        $this->renderAcquisitionObjectives();
    }

    private function renderEssentialQuestionsAndUnderstanding(): void
    {
        $preguntasY = $this->GetY();
        $this->SetFillColor(200);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(95, 6, __('PREGUNTAS ESENCIALES'), 1, 0, 'C', true);
        $this->Cell(95, 6, __('ENTENDIMIENTO DURADERO'), 1, 1, 'C', true);
        $this->SetFillColor(255);

        $this->Rect(10, $this->GetY(), 95, 45);
        $this->Rect(105, $preguntasY + 6, 95, 45);

        $this->SetFont('Arial', '', 9);
        for ($i = 1; $i <= 5; $i++) {
            $this->SetX(10);
            $this->Cell(10, 5, "PE{$i}:");
            $this->Cell(82, 5, $this->plan->{"pe{$i}"} ?? '', 'B', 0);

            $this->SetX(105);
            $this->Cell(10, 5, "ED{$i}:");
            $this->Cell(82, 5, $this->plan->{"ed{$i}"} ?? '', 'B', 1);
            $this->Ln(1);
        }

        $this->SetY($preguntasY + 51);
        $this->Ln(2);
    }

    private function renderTransferObjectives(): void
    {
        $this->Rect(10, $this->GetY(), 190, 10);
        $this->SetFont('Arial', 'B', 9);
        $objetivosLabel = __('OBJETIVOS DE TRANSFERENCIA (GENERAL):');
        $this->Cell($this->GetStringWidth($objetivosLabel . ' ') + 3, 5, $objetivosLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(0, 5, $this->plan->objetivo_general ?? '');

        $this->Ln(5);
    }

    private function renderAcquisitionObjectives(): void
    {
        $this->Rect(10, $this->GetY(), 190, 30);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __('Objetivos de Adquisición:'), 0, 1);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 4, __('Al finalizar esta unidad, el estudiante:'), 0, 1);
        $this->MultiCell(0, 4, $this->plan->objetivo_adquisicion ?? '');
    }

    private function renderPage2(): void
    {
        $this->AddPage();
        $this->renderStage2();
    }

    private function renderStage2(): void
    {
        $this->SetFillColor(200);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __('ETAPA 2 - EVIDENCIA PARA EVALUAR APRENDIZAJE'), 1, 1, 'C', true);
        $this->SetFillColor(255);

        $etapa2Y = $this->GetY();

        // Headers
        $this->SetFillColor(200);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(63.3, 6, __('TAREAS DE DESEMPEÑO AUTÉNTICO'), 1, 0, 'C', true);
        $this->Cell(63.3, 6, __('OTRA EVIDENCIA'), 1, 0, 'C', true);
        $this->Cell(63.3, 6, __('ACTIVIDADES'), 1, 1, 'C', true);
        $this->SetFillColor(255);

        // Content boxes
        $this->Rect(10, $this->GetY(), 63.3, 100);
        $this->Rect(73.3, $etapa2Y + 6, 63.3, 100);
        $this->Rect(136.6, $etapa2Y + 6, 63.3, 100);

        $this->SetFont('Arial', '', 8);
        $this->SetXY(10, $this->GetY());
        $this->MultiCell(63.3, 4, $this->plan->tareas ?? '');

        $this->SetXY(73.3, $etapa2Y + 6);
        $this->MultiCell(63.3, 4, $this->plan->otra ?? '');

        $this->SetXY(136.6, $etapa2Y + 6);
        $this->MultiCell(63.3, 4, $this->plan->actividades ?? '');

        // Observations
        $this->SetY($etapa2Y + 106);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(63.3, 5, __('Observaciones:'), 0, 0);
        $this->SetX(73.3);
        $this->Cell(63.3, 5, __('Observaciones:'), 0, 0);
        $this->SetX(136.6);
        $this->Cell(63.3, 5, __('Observaciones:'), 0, 1);

        $this->Rect(10, $this->GetY(), 63.3, 50);
        $this->Rect(73.3, $etapa2Y + 111, 63.3, 50);
        $this->Rect(136.6, $etapa2Y + 111, 63.3, 50);

        $this->SetFont('Arial', '', 8);
        $this->SetXY(10, $this->GetY());
        $this->MultiCell(63.3, 4, $this->plan->tareas_observaciones ?? '');

        $this->SetXY(73.3, $etapa2Y + 111);
        $this->MultiCell(63.3, 4, $this->plan->otra_observaciones ?? '');

        $this->SetXY(136.6, $etapa2Y + 111);
        $this->MultiCell(63.3, 4, $this->plan->actividades_observaciones ?? '');
    }

    private function renderPage3(): void
    {
        $this->AddPage();
        $this->renderStage3();
        $this->renderWeeklySchedule();
        $this->renderAccommodations();
    }

    private function renderStage3(): void
    {
        $this->SetFillColor(200);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __('ETAPA 3 - PLAN DE APRENDIZAJE'), 1, 1, 'C', true);
        $this->SetFillColor(0);

        $this->Ln(2);

        $this->Rect(10, $this->GetY(), 190, 20);
        $this->SetFont('Arial', 'B', 9);
        $expectativaLabel = __('Expectativa o indicador:');
        $this->Cell($this->GetStringWidth($expectativaLabel . ' ') + 3, 5, $expectativaLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->plan->expectativa ?? '', 'B', 1);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 9);
        $estrategiaLabel = __('Estrategia general:');
        $this->Cell($this->GetStringWidth($estrategiaLabel . ' ') + 3, 5, $estrategiaLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->plan->estrategia ?? '', 'B', 1);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 9);
        $objetivosDetailLabel = __('Objetivos:');
        $this->Cell($this->GetStringWidth($objetivosDetailLabel . ' ') + 3, 5, $objetivosDetailLabel, 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, $this->plan->objetivos ?? '', 'B', 1);

        $this->Ln(3);
    }

    private function renderWeeklySchedule(): void
    {
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $niveles = ['Memorístico', 'Procesamiento', 'Estratégico', 'Extendido'];

        $this->SetFillColor(220);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, __('Día'), 1, 0, 'C', true);
        foreach ($dias as $dia) {
            $this->Cell(33, 5, $dia, 1, 0, 'C', true);
        }
        $this->Ln();

        $this->Cell(25, 5, __('Fecha'), 1, 0, 'C', true);
        foreach (range(1, 5) as $i) {
            $fecha = $this->plan->{"fecha{$i}"} ?? '';
            if ($fecha) {
                $fecha = date('d/m/Y', strtotime($fecha));
            }
            $this->Cell(33, 5, $fecha, 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetFillColor(0);

        // Nivel de Profundidad
        $this->SetFont('Arial', 'B', 7);
        $nivelY = $this->GetY();
        $this->MultiCell(25, 5, __('Nivel de') . "\n" . __('Profundidad'), 1, 'C');
        $this->SetXY(10, $nivelY);
        $this->Cell(25, 20, '', 1, 0, 'C');
        $this->SetFont('Arial', '', 7);

        foreach (range(1, 5) as $day) {
            $x = 10 + 25 + (($day - 1) * 33);
            $y = $nivelY;
            $this->Rect($x, $y, 33, 20);

            foreach (range(1, 4) as $level) {
                $this->SetXY($x + 0.5, $y + ($level - 1) * 5 + 0.5);
                $this->Cell(3);
                $this->drawCheckbox($this->plan->{"nivel{$day}_{$level}"} === 'si');
                $this->SetX($x + 5);
                $this->Cell(27, 5, $niveles[$level - 1]);
            }
        }
        $this->SetY($nivelY + 20);

        // Inicio, Desarrollo, Cierre
        $this->renderWeeklySection('Inicio', 'inicio', 25);
        $this->renderWeeklySection('Desarrollo', 'desarrollo', 25);
        $this->renderWeeklySection('Cierre', 'cierre', 25);
    }

    private function renderWeeklySection(string $label, string $fieldPrefix, int $height): void
    {
        $this->SetFont('Arial', 'B', 8);
        $sectionY = $this->GetY();
        $this->Cell(25, $height, __($label), 1, 0, 'C');
        $this->SetFont('Arial', '', 7);

        foreach (range(1, 5) as $i) {
            $x = 10 + 25 + (($i - 1) * 33);
            $y = $sectionY;
            $this->Rect($x, $y, 33, $height);
            $this->SetXY($x + 0.5, $y + 0.5);
            $this->MultiCell(32, 3, $this->plan->{"{$fieldPrefix}{$i}"} ?? '');
        }
        $this->SetY($sectionY + $height);
    }

    private function renderAccommodations(): void
    {
        $acomodos = [
            'Ubicación adecuada del pupitre',
            'Tiempo adicional',
            'Ayuda individualizada',
            'Tareas y exámenes mas cortos',
            'Refuerzo positivo',
            'Otro'
        ];

        $this->SetFont('Arial', 'B', 7);
        $acomodoY = $this->GetY();
        $this->MultiCell(25, 5, __('Acomodo') . "\n" . __('Razonable'), 1, 'C');
        $this->SetXY(10, $acomodoY);
        $this->Cell(25, 40, '', 1, 0, 'C');
        $this->SetFont('Arial', '', 6);

        foreach (range(1, 5) as $day) {
            $x = 10 + 25 + (($day - 1) * 33);
            $y = $acomodoY;
            $this->Rect($x, $y, 33, 40);

            foreach (range(1, 6) as $acomodoIdx) {
                $this->SetXY($x + 0.5, $y + ($acomodoIdx - 1) * 6.5 + 0.5);
                $this->Cell(3);
                $this->drawCheckbox($this->plan->{"acomodo{$day}_{$acomodoIdx}"} === 'si', y: .5);
                $this->SetX($x + 4.5);
                if ($acomodoIdx == 6 && !empty($this->plan->{"otro{$day}"})) {
                    $this->Cell(28, 3, $this->plan->{"otro{$day}"});
                } else {
                    $this->MultiCell(28, 3, $acomodos[$acomodoIdx - 1]);
                }
            }
        }
    }
}
