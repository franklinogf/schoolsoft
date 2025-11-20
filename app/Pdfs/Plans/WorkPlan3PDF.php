<?php

namespace App\Pdfs\Plans;

use App\Models\Admin;
use App\Models\WorkPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class WorkPlan3PDF extends PDF implements PdfInterface
{

    public function __construct(private WorkPlan $workPlan)
    {
        parent::__construct();
        $this->SetTitle(__("Plan de trabajo") . " - " . $this->workPlan->plan, true);
        $this->Fill();
    }

    public function generate(): void
    {
        $this->renderPage1();
        $this->renderPage2();
    }

    private function renderPage1(): void
    {
        $this->AddPage();
        // Título
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, __("PLAN DE TRABAJO 3"), 0, 1, 'C');
        $this->Ln(3);

        // Plan de
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __("Plan de") . ": " . ($this->workPlan->plan ?? ''), 0, 1);
        $this->SetFont('Arial', '', 10);

        $this->Cell(0, 5, __("Estándares de Contenido") . ": {$this->workPlan->estandares}", 0, 1);
        $this->Ln(2);

        // Información básica
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
        $this->Ln(2);

        // Enfoque
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, "1. " . __("Enfocar") . "   2. " . __("Explorar") . "   3. " . __("Reflexionar") . "   4. " . __("Aplicación"), 1, 1, 'C', true);
        $this->Ln(2);

        // Estándares y Expectativas
        if ($this->workPlan->espectativas) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Estándares y Espectativas") . ":", 0, 1);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(0, 5, $this->workPlan->espectativas);
            $this->Ln(2);
        }

        // Nivel de Profundidad
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, __("Nivel de Profundidad de Conocimiento") . ":", 0, 1);
        $this->SetFont('Arial', '', 9);

        $niveles = [];
        if ($this->workPlan->np1 == 'Si') $niveles[] = __("Memorístico");
        if ($this->workPlan->np2 == 'Si') $niveles[] = __("Procesamiento");
        if ($this->workPlan->np3 == 'Si') $niveles[] = __("Estratégico");
        if ($this->workPlan->np4 == 'Si') $niveles[] = __("Extendido");
        if ($this->workPlan->np5 == 'Si') $niveles[] = "Nivel 5";

        $this->Cell(0, 5, implode(", ", $niveles), 0, 1);
        $this->Ln(2);

        // Tema y Pre-requisito
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, __("Tema"), 1, 0, 'C', true);
        $this->Cell(95, 6, __("Pre-requisito"), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        $this->Cell(95, 5, $this->workPlan->tema ?? '', 1, 0);
        $this->Cell(95, 5, $this->workPlan->pre1 ?? '', 1, 1);
        $this->Ln(2);

        // Objetivos
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, __("Objetivo"), 1, 1, 'C', true);

        // Conceptual
        if ($this->workPlan->obj1 || $this->workPlan->ent1 || $this->workPlan->ent2 || $this->workPlan->ent3 || $this->workPlan->ent4) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Conceptual") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);

            if ($this->workPlan->obj1) $this->MultiCell(0, 5, "- " . $this->workPlan->obj1);
            if ($this->workPlan->ent1) $this->MultiCell(0, 5, "- " . $this->workPlan->ent1);
            if ($this->workPlan->ent2) $this->MultiCell(0, 5, "- " . $this->workPlan->ent2);
            if ($this->workPlan->ent3) $this->MultiCell(0, 5, "- " . $this->workPlan->ent3);
            if ($this->workPlan->ent4) $this->MultiCell(0, 5, "- " . $this->workPlan->ent4);
            $this->Ln(1);
        }

        // Procedimental
        if ($this->workPlan->obj2 || $this->workPlan->ent5 || $this->workPlan->ent6 || $this->workPlan->ent7 || $this->workPlan->ent8) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Procedimental") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);

            if ($this->workPlan->obj2) $this->MultiCell(0, 5, "- " . $this->workPlan->obj2);
            if ($this->workPlan->ent5) $this->MultiCell(0, 5, "- " . $this->workPlan->ent5);
            if ($this->workPlan->ent6) $this->MultiCell(0, 5, "- " . $this->workPlan->ent6);
            if ($this->workPlan->ent7) $this->MultiCell(0, 5, "- " . $this->workPlan->ent7);
            if ($this->workPlan->ent8) $this->MultiCell(0, 5, "- " . $this->workPlan->ent8);
            $this->Ln(1);
        }

        // Actitudinal
        if ($this->workPlan->obj3 || $this->workPlan->ent9 || $this->workPlan->ent10 || $this->workPlan->ent11 || $this->workPlan->ent12) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Actitudinal") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);

            if ($this->workPlan->obj3) $this->MultiCell(0, 5, "- " . $this->workPlan->obj3);
            if ($this->workPlan->ent9) $this->MultiCell(0, 5, "- " . $this->workPlan->ent9);
            if ($this->workPlan->ent10) $this->MultiCell(0, 5, "- " . $this->workPlan->ent10);
            if ($this->workPlan->ent11) $this->MultiCell(0, 5, "- " . $this->workPlan->ent11);
            if ($this->workPlan->ent12) $this->MultiCell(0, 5, "- " . $this->workPlan->ent12);
            $this->Ln(1);
        }

        // Integración
        if ($this->workPlan->integracion) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Integración") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->integracion);
        }
    }

    private function renderPage2(): void
    {
        $this->AddPage();
        // Encabezados de las columnas
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, __("SECUENCIA DE ACTIVIDADES"), 1, 0, 'C', true);
        $this->Cell(95, 6, __("EVALUACION INFORMATIVA"), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 8);

        // Actividad
        if ($this->workPlan->act1 == 'Si') {
            $this->Cell(95, 4, __("Actividad"), 0, 0);
            $this->Cell(95, 4, "", 0, 1);
        }

        // Actividades Exploración/Conceptualización/Aplicación
        $actividades = [];
        if ($this->workPlan->act2 == 'Si') $actividades[] = __("Exploración");
        if ($this->workPlan->act3 == 'Si') $actividades[] = __("Conceptualización");
        if ($this->workPlan->act4 == 'Si') $actividades[] = __("Aplicación");
        if (!empty($actividades)) {
            $this->Cell(95, 4, implode(", ", $actividades), 0, 0);
            $this->Cell(95, 4, "", 0, 1);
        }

        // 1. Inicio (columna izquierda) | 4. Aplicaciones (columna derecha)
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(95, 4, "1. " . __("Inicio") . ":", 0, 0);
        $this->Cell(95, 4, "4. " . __("Aplicaciones") . ":", 0, 1);
        $this->SetFont('Arial', '', 8);

        // Primera línea de aplicaciones
        $aplicaciones = [];
        if ($this->workPlan->eva1 == 'Si') $aplicaciones[] = __("Aplicaciones");
        if ($this->workPlan->eva2 == 'Si') $aplicaciones[] = __("Texto");
        if ($this->workPlan->eva3 == 'Si') $aplicaciones[] = __("Cuaderno");
        if ($this->workPlan->eva4 == 'Si') $aplicaciones[] = __("Fichas");

        $lineaAplicaciones = !empty($aplicaciones) ? implode(", ", $aplicaciones) : "";

        // Items de Inicio
        $inicios = [];
        if ($this->workPlan->ini1 == 'Si') $inicios[] = "  - " . __("Inicio");
        if ($this->workPlan->ini2 == 'Si') $inicios[] = "  - " . __("Repaso clase anterior");
        if ($this->workPlan->ini3 == 'Si') $inicios[] = "  - " . __("Corrección de asignación");
        if ($this->workPlan->ini4 == 'Si') $inicios[] = "  - " . __("Refuerzo");
        if ($this->workPlan->ini5 == 'Si') $inicios[] = "  - " . __("Introducción al tema");
        if ($this->workPlan->ini6 == 'Si') $inicios[] = "  - " . __("Torbellino de ideas");
        if ($this->workPlan->ini7 == 'Si') $inicios[] = "  - " . __("Uso de manipulativo");

        $inicioIndex = 0;

        // Primera línea de inicio con aplicaciones
        if ($inicioIndex < count($inicios)) {
            $this->Cell(95, 4, $inicios[$inicioIndex], 0, 0);
            $inicioIndex++;
        } else {
            $this->Cell(95, 4, "", 0, 0);
        }
        $this->Cell(95, 4, $lineaAplicaciones, 0, 1);

        // Tabla de Prácticas (columna derecha) - 3 líneas
        for ($i = 0; $i < 3; $i++) {
            if ($inicioIndex < count($inicios)) {
                $this->Cell(95, 3, $inicios[$inicioIndex], 0, 0);
                $inicioIndex++;
            } else {
                $this->Cell(95, 3, "", 0, 0);
            }

            // Tabla derecha
            $this->SetFont('Arial', 'B', 7);
            if ($i == 0) {
                $this->Cell(19, 3, "", 1, 0, 'C');
                $this->Cell(15, 3, __("Pág."), 1, 0, 'C');
                $this->Cell(25, 3, __("Ejercicios"), 1, 0, 'C');
                $this->Cell(18, 3, __("Impares"), 1, 0, 'C');
                $this->Cell(18, 3, __("Pares"), 1, 1, 'C');
            } else if ($i == 1) {
                $this->SetFont('Arial', '', 7);
                $this->Cell(19, 3, __("Prácticas"), 1, 0);
                $this->Cell(15, 3, $this->workPlan->tab1 ?? '', 1, 0);
                $this->Cell(25, 3, $this->workPlan->tab3 ?? '', 1, 0);
                $this->Cell(18, 3, $this->workPlan->tab5 ?? '', 1, 0);
                $this->Cell(18, 3, $this->workPlan->tab7 ?? '', 1, 1);
            } else {
                $this->SetFont('Arial', '', 7);
                $this->Cell(19, 3, __("Asignación"), 1, 0);
                $this->Cell(15, 3, $this->workPlan->tab2 ?? '', 1, 0);
                $this->Cell(25, 3, $this->workPlan->tab4 ?? '', 1, 0);
                $this->Cell(18, 3, $this->workPlan->tab6 ?? '', 1, 0);
                $this->Cell(18, 3, $this->workPlan->tab8 ?? '', 1, 1);
            }
            $this->SetFont('Arial', '', 8);
        }

        // Continuar con Inicio y Selección
        $this->renderInicioAndSeleccion($inicios, $inicioIndex);

        // 2. Desarrollo | 5. Assessment
        $this->renderDesarrolloAndAssessment();

        // 3. Cierre
        $this->renderCierre();

        // Autoevaluación
        if ($this->workPlan->autoeva) {
            $this->Ln(3);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, __("Autoevaluación o observaciones") . ":", 0, 1);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->autoeva);
        }
    }

    private function renderInicioAndSeleccion(array $inicios, int $inicioIndex): void
    {
        $selecciones = [];
        if ($this->workPlan->sel1 == 'Si') $selecciones[] = "- " . __("Quiz");
        if ($this->workPlan->sel2 == 'Si') $selecciones[] = "- " . __("Examen");
        if ($this->workPlan->sel3 == 'Si') $selecciones[] = "- " . __("Informes");
        if ($this->workPlan->sel4 == 'Si') {
            $selecciones[] = "- " . __("Proyecto del día") . " " . ($this->workPlan->pro1 ?? '') . " " . __("al") . " " . ($this->workPlan->pro2 ?? '') . " " . __("del mes");
        }
        if ($this->workPlan->sel5 == 'Si' && $this->workPlan->otro) {
            $selecciones[] = "- " . __("Otros") . ": " . $this->workPlan->otro;
        }

        $seleccionIndex = 0;
        $maxLines = max(count($inicios) - $inicioIndex, count($selecciones));

        for ($i = 0; $i < $maxLines; $i++) {
            if ($inicioIndex < count($inicios)) {
                $this->Cell(95, 4, $inicios[$inicioIndex], 0, 0);
                $inicioIndex++;
            } else {
                $this->Cell(95, 4, "", 0, 0);
            }

            if ($seleccionIndex < count($selecciones)) {
                $this->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
                $seleccionIndex++;
            } else {
                $this->Cell(95, 4, "", 0, 1);
            }
        }

        // Otros - Inicio
        if ($this->workPlan->ot1 == 'Si') {
            $this->Cell(95, 4, "  - " . __("Otros"), 0, 0);
            if ($seleccionIndex < count($selecciones)) {
                $this->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
                $seleccionIndex++;
            } else {
                $this->Cell(95, 4, "", 0, 1);
            }

            if ($this->workPlan->otr1) {
                $this->Cell(95, 4, "    " . $this->workPlan->otr1, 0, 0);
                if ($seleccionIndex < count($selecciones)) {
                    $this->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
                    $seleccionIndex++;
                } else {
                    $this->Cell(95, 4, "", 0, 1);
                }
            }
            if ($this->workPlan->otr2) {
                $this->Cell(95, 4, "    " . $this->workPlan->otr2, 0, 0);
                if ($seleccionIndex < count($selecciones)) {
                    $this->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
                    $seleccionIndex++;
                } else {
                    $this->Cell(95, 4, "", 0, 1);
                }
            }
        }

        // Completar selecciones restantes
        while ($seleccionIndex < count($selecciones)) {
            $this->Cell(95, 4, "", 0, 0);
            $this->Cell(95, 4, $selecciones[$seleccionIndex], 0, 1);
            $seleccionIndex++;
        }
    }

    private function renderDesarrolloAndAssessment(): void
    {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(95, 4, "2. " . __("Desarrollo") . ":", 0, 0);
        $this->Cell(95, 4, "5. " . __("Assessment") . ":", 0, 1);
        $this->SetFont('Arial', '', 8);

        $desarrollos = [];
        if ($this->workPlan->des1 == 'Si') $desarrollos[] = "  - " . __("Desarrollo");
        if ($this->workPlan->des2 == 'Si') $desarrollos[] = "  - " . __("Presentación de la temática");
        if ($this->workPlan->des3 == 'Si') $desarrollos[] = "  - " . __("Definición y presentación de los conceptos");
        if ($this->workPlan->des4 == 'Si') $desarrollos[] = "  - " . __("Listados o requisitos de un vocabulario");
        if ($this->workPlan->des5 == 'Si') $desarrollos[] = "  - " . __("Listados de propiedades, características, detalles, etc.");
        if ($this->workPlan->des6 == 'Si') $desarrollos[] = "  - " . __("Ejemplicación sobre los procesos");
        if ($this->workPlan->des7 == 'Si') $desarrollos[] = "  - " . __("Uso de la tecnología");

        $assessments = [];
        if ($this->workPlan->as1 == 'Si') $assessments[] = "  - " . __("Assessment");
        if ($this->workPlan->as2 == 'Si') $assessments[] = "  - " . __("Lista de cotejo");
        if ($this->workPlan->as3 == 'Si') $assessments[] = "  - " . __("Tirilla cómica");
        if ($this->workPlan->as4 == 'Si') $assessments[] = "  - " . __("Diario reflexivo");
        if ($this->workPlan->as5 == 'Si') $assessments[] = "  - " . __("Mapa de concepto");
        if ($this->workPlan->as6 == 'Si') $assessments[] = "  - " . __("Organizador gráfico");
        if ($this->workPlan->as7 == 'Si') $assessments[] = "  - " . __("Aprendizaje cooperativo");
        if ($this->workPlan->as8 == 'Si') $assessments[] = "  - " . __("Porfolio");

        $maxLines = max(count($desarrollos), count($assessments));

        for ($i = 0; $i < $maxLines; $i++) {
            $left = $i < count($desarrollos) ? $desarrollos[$i] : "";
            $right = $i < count($assessments) ? $assessments[$i] : "";
            $this->Cell(95, 4, $left, 0, 0);
            $this->Cell(95, 4, $right, 0, 1);
        }

        // Otros - Desarrollo y Assessment
        $otrosDesarrollo = [];
        if ($this->workPlan->ot2 == 'Si') {
            $otrosDesarrollo[] = "  - " . __("Otros");
            if ($this->workPlan->otr3) $otrosDesarrollo[] = "    " . $this->workPlan->otr3;
            if ($this->workPlan->otr4) $otrosDesarrollo[] = "    " . $this->workPlan->otr4;
        }

        $otrosAssessment = [];
        if ($this->workPlan->ot4 == 'Si') {
            $otrosAssessment[] = "  - " . __("Otros");
            if ($this->workPlan->otr7) $otrosAssessment[] = "    " . $this->workPlan->otr7;
            if ($this->workPlan->otr8) $otrosAssessment[] = "    " . $this->workPlan->otr8;
        }

        $maxLines = max(count($otrosDesarrollo), count($otrosAssessment));

        for ($i = 0; $i < $maxLines; $i++) {
            $left = $i < count($otrosDesarrollo) ? $otrosDesarrollo[$i] : "";
            $right = $i < count($otrosAssessment) ? $otrosAssessment[$i] : "";
            $this->Cell(95, 4, $left, 0, 0);
            $this->Cell(95, 4, $right, 0, 1);
        }
    }

    private function renderCierre(): void
    {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(95, 4, "3. " . __("Cierre") . ":", 0, 0);
        $this->Cell(95, 4, "", 0, 1);
        $this->SetFont('Arial', '', 8);

        $cierres = [];
        if ($this->workPlan->cie1 == 'Si') $cierres[] = "  - " . __("Cierre");
        if ($this->workPlan->cie2 == 'Si') $cierres[] = "  - " . __("Resumir materias discutido");
        if ($this->workPlan->cie3 == 'Si') $cierres[] = "  - " . __("Aclarar dudas");
        if ($this->workPlan->cie4 == 'Si') $cierres[] = "  - " . __("Llegar a conclusiones");
        if ($this->workPlan->cie5 == 'Si') $cierres[] = "  - " . __("Discusión del trabajo asignados");

        foreach ($cierres as $cierre) {
            $this->Cell(95, 4, $cierre, 0, 0);
            $this->Cell(95, 4, "", 0, 1);
        }

        // Otros - Cierre
        if ($this->workPlan->ot3 == 'Si') {
            $this->Cell(95, 4, "  - " . __("Otros"), 0, 0);
            $this->Cell(95, 4, "", 0, 1);

            if ($this->workPlan->otr5) {
                $this->Cell(95, 4, "    " . $this->workPlan->otr5, 0, 0);
                $this->Cell(95, 4, "", 0, 1);
            }
            if ($this->workPlan->otr6) {
                $this->Cell(95, 4, "    " . $this->workPlan->otr6, 0, 0);
                $this->Cell(95, 4, "", 0, 1);
            }
        }
    }
}
