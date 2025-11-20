<?php

namespace App\Pdfs\Plans;

use App\Models\Admin;
use App\Models\WorkPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class WorkPlan2PDF extends PDF implements PdfInterface
{
    public function __construct(private WorkPlan $workPlan)
    {
        parent::__construct();
        $this->SetTitle(__("Plan de trabajo 2") . " - " . $this->workPlan->plan, true);
        $this->Fill();
    }

    public function generate(): void
    {

        $this->AddPage();

        $this->renderHeader();
        $this->renderBasicInfo();
        $this->renderFocus();
        $this->renderStandardsAndExpectations();
        $this->renderDepthLevel();
        $this->renderUnitChapter();
        $this->renderObjectives();
        $this->renderActivitiesAndEvaluation();
        $this->renderSelfEvaluation();        
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, __("PLAN DE TRABAJO"), 0, 1, 'C');
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __("PLAN DE") . ": " . ($this->workPlan->plan ?? ''), 0, 1);
        $this->SetFont('Arial', '', 10);

        if ($this->workPlan->estandares == 'Si') {
            $this->Cell(0, 5, __("Estándares de Contenido") . ": Si", 0, 1);
        }
        $this->Ln(2);
    }

    private function renderBasicInfo(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, __("Grado") . ":", 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->workPlan->grado ?? '', 0, 1);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, __("Asignatura Específica") . ":", 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->workPlan->asignatura ?? '', 0, 1);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, __("Fecha/Semana") . ":", 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, __("mes") . ": " . ($this->workPlan->mes ?? '') . " " . __("día") . " " . ($this->workPlan->dia1 ?? '') . " " . __("al día") . " " . ($this->workPlan->dia2 ?? ''), 0, 1);

        // Temas (plan2 tiene hasta 5)
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, __("Temas") . ":", 0, 1);
        $this->SetFont('Arial', '', 10);
        if ($this->workPlan->tema1) $this->Cell(0, 4, $this->workPlan->tema1, 0, 1);
        if ($this->workPlan->tema2) $this->Cell(0, 4, $this->workPlan->tema2, 0, 1);
        if ($this->workPlan->tema3) $this->Cell(0, 4, $this->workPlan->tema3, 0, 1);
        if ($this->workPlan->tema4) $this->Cell(0, 4, $this->workPlan->tema4, 0, 1);
        if ($this->workPlan->tema5) $this->Cell(0, 4, $this->workPlan->tema5, 0, 1);
        $this->Ln(2);
    }

    private function renderFocus(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, "1. " . __("Enfocar") . "   2. " . __("Explorar") . "   3. " . __("Reflexionar") . "   4. " . __("Aplicación"), 1, 1, 'C', true);
        $this->Ln(2);
    }

    private function renderStandardsAndExpectations(): void
    {
        if ($this->workPlan->espectativas) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Estándares y Espectativas") . ":", 0, 1);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(0, 5, $this->workPlan->espectativas);
            $this->Ln(2);
        }
    }

    private function renderDepthLevel(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __("Nivel de Profundidad de Conocimiento") . ":", 0, 1);
        $this->SetFont('Arial', '', 9);

        $niveles = [];
        if ($this->workPlan->np1 == 'Si') $niveles[] = __("Memorístico");
        if ($this->workPlan->np2 == 'Si') $niveles[] = __("Procesamiento");
        if ($this->workPlan->np3 == 'Si') $niveles[] = __("Estratégico");
        if ($this->workPlan->np4 == 'Si') $niveles[] = __("Extendido");
        if ($this->workPlan->np5 == 'Si') $niveles[] = "Nivel 5";

        if (!empty($niveles)) {
            $this->Cell(0, 5, implode(", ", $niveles), 0, 1);
        }
        $this->Ln(2);
    }

    private function renderUnitChapter(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, __("Unidad"), 1, 0, 'C', true);
        $this->Cell(95, 6, __("Capítulo"), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        $this->Cell(95, 5, $this->workPlan->tema ?? '', 1, 0);
        $this->Cell(95, 5, $this->workPlan->pre1 ?? '', 1, 1);
        $this->Ln(2);
    }

    private function renderObjectives(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, __("Objetivo"), 1, 1, 'C', true);

        // Conceptual
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __("Conceptual") . ":", 0, 1);
        $this->SetFont('Arial', '', 8);
        if ($this->workPlan->obj1) $this->Cell(0, 4, $this->workPlan->obj1, 0, 1);
        if ($this->workPlan->ent1) $this->Cell(0, 4, $this->workPlan->ent1, 0, 1);
        if ($this->workPlan->ent2) $this->Cell(0, 4, $this->workPlan->ent2, 0, 1);
        if ($this->workPlan->ent3) $this->Cell(0, 4, $this->workPlan->ent3, 0, 1);
        if ($this->workPlan->ent4) $this->Cell(0, 4, $this->workPlan->ent4, 0, 1);
        $this->Ln(2);

        // Procedimental
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __("Procedimental") . ":", 0, 1);
        $this->SetFont('Arial', '', 8);
        if ($this->workPlan->obj2) $this->Cell(0, 4, $this->workPlan->obj2, 0, 1);
        if ($this->workPlan->ent5) $this->Cell(0, 4, $this->workPlan->ent5, 0, 1);
        if ($this->workPlan->ent6) $this->Cell(0, 4, $this->workPlan->ent6, 0, 1);
        if ($this->workPlan->ent7) $this->Cell(0, 4, $this->workPlan->ent7, 0, 1);
        if ($this->workPlan->ent8) $this->Cell(0, 4, $this->workPlan->ent8, 0, 1);
        $this->Ln(2);

        // Actitudinal
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, __("Actitudinal") . ":", 0, 1);
        $this->SetFont('Arial', '', 8);
        if ($this->workPlan->obj3) $this->Cell(0, 4, $this->workPlan->obj3, 0, 1);
        if ($this->workPlan->ent9) $this->Cell(0, 4, $this->workPlan->ent9, 0, 1);
        if ($this->workPlan->ent10) $this->Cell(0, 4, $this->workPlan->ent10, 0, 1);
        if ($this->workPlan->ent11) $this->Cell(0, 4, $this->workPlan->ent11, 0, 1);
        if ($this->workPlan->ent12) $this->Cell(0, 4, $this->workPlan->ent12, 0, 1);
        $this->Ln(2);

        // Integración
        if ($this->workPlan->integracion) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(45, 5, __("Integración") . ":", 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->integracion);
            $this->Ln(2);
        }
    }

    private function renderActivitiesAndEvaluation(): void
    {
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, __("SECUENCIA DE ACTIVIDADES"), 1, 0, 'C', true);
        $this->Cell(95, 6, __("EVALUACION INFORMATIVA"), 1, 1, 'C', true);

        $leftContent = $this->prepareLeftColumnContent();
        $rightContent = $this->prepareRightColumnContent();

        $maxRows = max(count($leftContent), count($rightContent));

        for ($i = 0; $i < $maxRows; $i++) {
            $leftItem = $leftContent[$i] ?? null;
            $rightItem = $rightContent[$i] ?? null;

            $height = 4;
            if (($leftItem && $leftItem['type'] == 'header') || ($rightItem && $rightItem['type'] == 'header')) {
                $height = 5;
            }
            if (($leftItem && $leftItem['type'] == 'section') || ($rightItem && $rightItem['type'] == 'section')) {
                $height = 5;
            }

            $this->SetX(10);
            if ($leftItem) {
                if ($leftItem['type'] == 'header' || $leftItem['type'] == 'section') {
                    $this->SetFont('Arial', 'B', 9);
                } else {
                    $this->SetFont('Arial', '', 8);
                }
                $this->Cell(95, $height, $leftItem['text'], 1, 0);
            } else {
                $this->Cell(95, $height, '', 1, 0);
            }

            if ($rightItem) {
                if ($rightItem['type'] == 'header' || $rightItem['type'] == 'section') {
                    $this->SetFont('Arial', 'B', 9);
                } else {
                    $this->SetFont('Arial', '', 8);
                }
                $this->Cell(95, $height, $rightItem['text'], 1, 1);
            } else {
                $this->Cell(95, $height, '', 1, 1);
            }
        }
    }

    private function prepareLeftColumnContent(): array
    {
        $leftContent = [];

        // Actividad
        $actividades = [];
        if ($this->workPlan->act1 == 'Si') $actividades[] = __("Actividad");
        if ($this->workPlan->act2 == 'Si') $actividades[] = __("Exploración");
        if ($this->workPlan->act3 == 'Si') $actividades[] = __("Conceptualización");
        if ($this->workPlan->act4 == 'Si') $actividades[] = __("Aplicación");
        if (!empty($actividades)) {
            $leftContent[] = ['type' => 'header', 'text' => __("Actividad") . ": " . implode(", ", $actividades)];
        }

        // Inicio
        $inicio = [];
        if ($this->workPlan->ini2 == 'Si') $inicio[] = __("Repaso clase anterior");
        if ($this->workPlan->ini3 == 'Si') $inicio[] = __("Corrección de asignación");
        if ($this->workPlan->ini4 == 'Si') $inicio[] = __("Refuerzo");
        if ($this->workPlan->ini5 == 'Si') $inicio[] = __("Introducción al tema");
        if ($this->workPlan->ini6 == 'Si') $inicio[] = __("Torbellino de ideas");
        if ($this->workPlan->ini7 == 'Si') $inicio[] = __("Uso de manipulativo");

        if (!empty($inicio)) {
            $leftContent[] = ['type' => 'section', 'text' => "1. " . __("Inicio") . ":"];
            foreach ($inicio as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        // Desarrollo
        $desarrollo = [];
        if ($this->workPlan->des2 == 'Si') $desarrollo[] = __("Presentación de la temática");
        if ($this->workPlan->des3 == 'Si') $desarrollo[] = __("Definición y presentación de los conceptos");
        if ($this->workPlan->des4 == 'Si') $desarrollo[] = __("Listados o requisitos de un vocabulario");
        if ($this->workPlan->des5 == 'Si') $desarrollo[] = __("Listados de propiedades, características, detalles, etc.");
        if ($this->workPlan->des6 == 'Si') $desarrollo[] = __("Ejemplicación sobre los procesos");
        if ($this->workPlan->des7 == 'Si') $desarrollo[] = __("Uso de la tecnología");

        if (!empty($desarrollo)) {
            $leftContent[] = ['type' => 'section', 'text' => "2. " . __("Desarrollo") . ":"];
            foreach ($desarrollo as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        // Cierre
        $cierre = [];
        if ($this->workPlan->cie2 == 'Si') $cierre[] = __("Resumir materias discutido");
        if ($this->workPlan->cie3 == 'Si') $cierre[] = __("Aclarar dudas");
        if ($this->workPlan->cie4 == 'Si') $cierre[] = __("Llegar a conclusiones");
        if ($this->workPlan->cie5 == 'Si') $cierre[] = __("Discusión del trabajo asignados");

        if (!empty($cierre)) {
            $leftContent[] = ['type' => 'section', 'text' => "3. " . __("Cierre") . ":"];
            foreach ($cierre as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        return $leftContent;
    }

    private function prepareRightColumnContent(): array
    {
        $rightContent = [];

        // Aplicaciones
        $aplicaciones = [];
        if ($this->workPlan->eva2 == 'Si') $aplicaciones[] = __("Texto");
        if ($this->workPlan->eva3 == 'Si') $aplicaciones[] = __("Cuaderno");
        if ($this->workPlan->eva4 == 'Si') $aplicaciones[] = __("Fichas");

        if (!empty($aplicaciones)) {
            $rightContent[] = ['type' => 'header', 'text' => "4. " . __("Aplicaciones") . ": " . implode(", ", $aplicaciones)];
        }

        // Tabla de prácticas y asignación
        if ($this->workPlan->tab1 || $this->workPlan->tab3 || $this->workPlan->tab5 || $this->workPlan->tab7) {
            $rightContent[] = ['type' => 'section', 'text' => __("Prácticas") . ": " . __("Pág.") . " " . ($this->workPlan->tab1 ?? '') . " " . __("Ejercicios") . " " . ($this->workPlan->tab3 ?? '')];
            if ($this->workPlan->tab5) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Impares") . ": " . $this->workPlan->tab5];
            if ($this->workPlan->tab7) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Pares") . ": " . $this->workPlan->tab7];
        }

        if ($this->workPlan->tab2 || $this->workPlan->tab4 || $this->workPlan->tab6 || $this->workPlan->tab8) {
            $rightContent[] = ['type' => 'section', 'text' => __("Asignación") . ": " . __("Pág.") . " " . ($this->workPlan->tab2 ?? '') . " " . __("Ejercicios") . " " . ($this->workPlan->tab4 ?? '')];
            if ($this->workPlan->tab6) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Impares") . ": " . $this->workPlan->tab6];
            if ($this->workPlan->tab8) $rightContent[] = ['type' => 'item', 'text' => "  " . __("Pares") . ": " . $this->workPlan->tab8];
        }

        // Selección
        $rightContent[] = ['type' => 'section', 'text' => "* " . __("seleccionados")];

        $seleccion = [];
        if ($this->workPlan->sel1 == 'Si') $seleccion[] = __("Quiz");
        if ($this->workPlan->sel2 == 'Si') $seleccion[] = __("Examen");
        if ($this->workPlan->sel3 == 'Si') $seleccion[] = __("Informes");
        if ($this->workPlan->sel4 == 'Si') {
            $proyecto = __("Proyecto del día");
            if ($this->workPlan->pro1 || $this->workPlan->pro2) {
                $proyecto .= ": " . ($this->workPlan->pro1 ?? '') . " " . __("al") . " " . ($this->workPlan->pro2 ?? '') . " " . __("del mes");
            }
            $seleccion[] = $proyecto;
        }
        if ($this->workPlan->sel5 == 'Si' && $this->workPlan->otro) {
            $seleccion[] = __("Otros") . ": " . $this->workPlan->otro;
        }

        foreach ($seleccion as $item) {
            $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
        }

        // Assessment
        $assessment = [];
        if ($this->workPlan->as2 == 'Si') $assessment[] = __("Lista de cotejo");
        if ($this->workPlan->as3 == 'Si') $assessment[] = __("Tirilla cómica");
        if ($this->workPlan->as4 == 'Si') $assessment[] = __("Diario reflexivo");
        if ($this->workPlan->as5 == 'Si') $assessment[] = __("Mapa de concepto");
        if ($this->workPlan->as6 == 'Si') $assessment[] = __("Organizador gráfico");
        if ($this->workPlan->as7 == 'Si') $assessment[] = __("Aprendizaje cooperativo");
        if ($this->workPlan->as8 == 'Si') $assessment[] = __("Porfolio");

        if (!empty($assessment)) {
            $rightContent[] = ['type' => 'section', 'text' => "5. " . __("Assessment") . ":"];
            foreach ($assessment as $item) {
                $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        return $rightContent;
    }

    private function renderSelfEvaluation(): void
    {
        if ($this->workPlan->autoeva) {
            $this->Ln(3);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Autoevaluación o observaciones") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 4, $this->workPlan->autoeva);
        }
    }
}
