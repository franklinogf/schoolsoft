<?php

namespace App\Pdfs\Plans;

use App\Models\WorkPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class WorkPlan1PDF extends PDF implements PdfInterface
{


    public function __construct(private WorkPlan $workPlan)
    {
        parent::__construct();
        $this->SetTitle(__("Plan de trabajo") . " - " . $this->workPlan->plan, true);
        $this->Fill();
        $this->SetAutoPageBreak(true, 15);
    }

    public function generate(): void
    {

        $this->AddPage();
        $this->renderHeader();
        $this->renderBasicInfo();
        $this->renderFocus();
        $this->renderStandardsAndExpectations();
        $this->renderDepthLevel();
        $this->renderEduSystem();
        $this->renderObjectives();
        $this->renderActivitiesAndEvaluation();
        $this->renderReasonableAccommodations();
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, __("PLAN DE TRABAJO"), 0, 1, 'C');
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, __("Plan de") . ": " . ($this->workPlan->plan ?? ''), 0, 1);
        $this->SetFont('Arial', '', 10);

        $this->Cell(0, 5, __("Estándares de Contenido") . ": {$this->workPlan->estandares}", 0, 1);
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

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, __("Temas") . ":", 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 5, ($this->workPlan->tema1 ?? '') . ($this->workPlan->tema2 ? "\n" . $this->workPlan->tema2 : ''));
        $this->Ln(2);
    }

    private function renderFocus(): void
    {
        $enfocar = __("Enfocar");
        $explorar = __("Explorar");
        $reflexionar = __("Reflexionar");
        $aplicacion = __("Aplicación");

        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, "1. $enfocar   2. $explorar   3. $reflexionar   4. $aplicacion", 1, 1, 'C', true);
        $this->Ln(2);
    }

    private function renderStandardsAndExpectations(): void
    {
        if ($this->workPlan->espectativas) {
            $estandaresLabel = __("Estándares y Espectativas");
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, $estandaresLabel . ":", 0, 1);
            $this->SetFont('Arial', '', 10);
            $this->MultiCell(0, 5, $this->workPlan->espectativas);
            $this->Ln(2);
        }
    }

    private function renderDepthLevel(): void
    {
        $nivelLabel = __("Nivel de Profundidad de Conocimiento");
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, $nivelLabel . ":", 0, 1);
        $this->SetFont('Arial', '', 9);

        $niveles = [];
        if ($this->workPlan->np1 == 'Si') $niveles[] = __("Memorístico");
        if ($this->workPlan->np2 == 'Si') $niveles[] = __("Procesamiento");
        if ($this->workPlan->np3 == 'Si') $niveles[] = __("Estratégico");
        if ($this->workPlan->np4 == 'Si') $niveles[] = __("Extendido");

        $this->Cell(0, 5, implode(", ", $niveles), 0, 1);
        $this->Ln(2);
    }

    private function renderEduSystem(): void
    {
        $temaLabel = __("Tema");
        $unidadLabel = __("Unidad");
        $leccionLabel = __("Lección");
        $codigoLabel = __("Código");
        $preRequisitoLabel = __("Pre-requisito");

        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, "EduSystem", 1, 0, 'C', true);
        $this->Cell(95, 6, $temaLabel, 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        $this->Cell(45, 5, $unidadLabel . ":", 1, 0);
        $this->Cell(50, 5, $this->workPlan->unidad ?? '', 1, 0);
        $this->Cell(95, 5, $this->workPlan->tema ?? '', 1, 1);

        $this->Cell(45, 5, $leccionLabel . ":", 1, 0);
        $this->Cell(50, 5, $this->workPlan->leccion ?? '', 1, 0);
        $this->Cell(95, 5, $preRequisitoLabel . ": " . ($this->workPlan->pre1 ?? ''), 1, 1);

        $this->Cell(45, 5, $codigoLabel . ":", 1, 0);
        $this->Cell(50, 5, $this->workPlan->codigo ?? '', 1, 0);
        $this->Cell(95, 5, '', 1, 1);
        $this->Ln(2);
    }

    private function renderObjectives(): void
    {
        $objetivoLabel = __("Objetivo");
        $conceptualLabel = __("Conceptual");
        $procedimentalLabel = __("Procedimental");
        $actitudinalLabel = __("Actitudinal");
        $integracionLabel = __("Integración");

        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(0, 6, $objetivoLabel, 1, 1, 'C', true);

        if ($this->workPlan->obj1) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(45, 5, $conceptualLabel . ":", 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->obj1);
        }

        if ($this->workPlan->obj2) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(45, 5, $procedimentalLabel . ":", 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->obj2);
        }

        if ($this->workPlan->obj3) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(45, 5, $actitudinalLabel . ":", 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->obj3);
        }

        if ($this->workPlan->integracion) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(45, 5, $integracionLabel . ":", 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(0, 5, $this->workPlan->integracion);
        }
        $this->Ln(2);
    }

    private function renderActivitiesAndEvaluation(): void
    {
        $secuenciaLabel = __("SECUENCIA DE ACTIVIDADES");
        $evaluacionLabel = __("EVALUACION INFORMATIVA");

        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(95, 6, $secuenciaLabel, 1, 0, 'C', true);
        $this->Cell(95, 6, $evaluacionLabel, 1, 1, 'C', true);

        // Preparar datos de columna izquierda
        $leftContent = $this->prepareLeftColumnContent();

        // Preparar datos de columna derecha
        $rightContent = $this->prepareRightColumnContent();

        // Renderizar ambas columnas fila por fila
        $maxRows = max(count($leftContent), count($rightContent));

        for ($i = 0; $i < $maxRows; $i++) {
            $leftItem = $leftContent[$i] ?? null;
            $rightItem = $rightContent[$i] ?? null;

            // Determinar altura de la fila
            $height = 4;
            if (($leftItem && $leftItem['type'] == 'header') || ($rightItem && $rightItem['type'] == 'header')) {
                $height = 5;
            }
            if (($leftItem && $leftItem['type'] == 'section') || ($rightItem && $rightItem['type'] == 'section')) {
                $height = 5;
            }

            // Columna izquierda
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

            // Columna derecha
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
        $actividadLabel = __("Actividad");
        $exploracionLabel = __("Exploración");
        $conceptualizacionLabel = __("Conceptualización");
        $aplicacionLabel = __("Aplicación");
        $inicioLabel = __("Inicio");
        $desarrolloLabel = __("Desarrollo");
        $cierreLabel = __("Cierre");

        $leftContent = [];

        // Actividad
        $actividades = [];
        if ($this->workPlan->act1 == 'Si') $actividades[] = $actividadLabel;
        if ($this->workPlan->act2 == 'Si') $actividades[] = $exploracionLabel;
        if ($this->workPlan->act3 == 'Si') $actividades[] = $conceptualizacionLabel;
        if ($this->workPlan->act4 == 'Si') $actividades[] = $aplicacionLabel;
        if (!empty($actividades)) {
            $leftContent[] = ['type' => 'header', 'text' => $actividadLabel . ": " . implode(", ", $actividades)];
        }

        // Inicio
        $inicio = [];
        if ($this->workPlan->ini1 == 'Si') $inicio[] = "✓";
        if ($this->workPlan->ini2 == 'Si') $inicio[] = __("Repaso clase anterior");
        if ($this->workPlan->ini3 == 'Si') $inicio[] = __("Exploración conocimientos previos");
        if ($this->workPlan->ini4 == 'Si') $inicio[] = __("Presentación de objetivos");
        if ($this->workPlan->ini5 == 'Si') $inicio[] = __("Motivación o inicio");
        if ($this->workPlan->ini6 == 'Si') $inicio[] = __("Presentación vocabulario");
        if ($this->workPlan->ini7 == 'Si') $inicio[] = __("Presentación del tema");

        if (!empty($inicio)) {
            $leftContent[] = ['type' => 'section', 'text' => "1. " . $inicioLabel . ":"];
            foreach ($inicio as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        // Desarrollo
        $desarrollo = [];
        if ($this->workPlan->des1 == 'Si') $desarrollo[] = __("Estrategia ECA");
        if ($this->workPlan->des2 == 'Si') $desarrollo[] = __("Trabajo cooperativo");
        if ($this->workPlan->des3 == 'Si') $desarrollo[] = __("Laboratorio");
        if ($this->workPlan->des4 == 'Si') $desarrollo[] = __("Discusión socializada");
        if ($this->workPlan->des5 == 'Si') $desarrollo[] = __("Centros de aprendizaje");
        if ($this->workPlan->des6 == 'Si') $desarrollo[] = __("Conferencia");
        if ($this->workPlan->des7 == 'Si') $desarrollo[] = __("Otra");

        if (!empty($desarrollo)) {
            $leftContent[] = ['type' => 'section', 'text' => "2. " . $desarrolloLabel . ":"];
            foreach ($desarrollo as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        // Cierre
        $cierre = [];
        if ($this->workPlan->cie1 == 'Si') $cierre[] = __("Repaso clase anterior");
        if ($this->workPlan->cie2 == 'Si') $cierre[] = __("Trabajo cooperativo");
        if ($this->workPlan->cie3 == 'Si') $cierre[] = __("Discusión socializada");
        if ($this->workPlan->cie4 == 'Si') $cierre[] = __("Centros de aprendizaje");
        if ($this->workPlan->cie5 == 'Si') $cierre[] = __("Otra");

        if (!empty($cierre)) {
            $leftContent[] = ['type' => 'section', 'text' => "3. " . $cierreLabel . ":"];
            foreach ($cierre as $item) {
                $leftContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        return $leftContent;
    }

    private function prepareRightColumnContent(): array
    {
        $aplicacionesLabel = __("Aplicaciones");
        $textoLabel = __("Texto");
        $cuadernoLabel = __("Cuaderno");
        $fichasLabel = __("Fichas");
        $pruebasLabel = __("Pruebas cortas o Pruebas");
        $proyectosLabel = __("Proyectos o tareas de desempeño");
        $tareasLabel = __("Tareas");
        $portafolioLabel = __("Portafolio");
        $seleccionLabel = __("Selección");
        $llenarBlancosLabel = __("Llenar blancos");
        $pareoLabel = __("Pareo");
        $ciertoFalsoLabel = __("Cierto falso");
        $informesLabel = __("Informes orales o escritos");
        $otroLabel = __("Otro");
        $prontuariosLabel = __("Prontuarios");
        $diariosLabel = __("Diarios reflexivos");
        $autoevaluacionLabel = __("Autoevaluación");

        $rightContent = [];

        // Aplicaciones
        $aplicaciones = [];
        if ($this->workPlan->eva1 == 'Si') $aplicaciones[] = $textoLabel;
        if ($this->workPlan->eva2 == 'Si') $aplicaciones[] = $cuadernoLabel;
        if ($this->workPlan->eva3 == 'Si') $aplicaciones[] = $fichasLabel;

        if (!empty($aplicaciones)) {
            $rightContent[] = ['type' => 'header', 'text' => "4. " . $aplicacionesLabel . ": " . implode(", ", $aplicaciones)];
        }

        if ($this->workPlan->tab1 == 'Si' || $this->workPlan->tab2) {
            $rightContent[] = ['type' => 'item', 'text' => $pruebasLabel . ": " . ($this->workPlan->tab2 ?? '')];
        }

        if ($this->workPlan->tab3 == 'Si' || $this->workPlan->tab4) {
            $rightContent[] = ['type' => 'item', 'text' => $proyectosLabel . ": " . ($this->workPlan->tab4 ?? '')];
        }

        if ($this->workPlan->tab5 == 'Si' || $this->workPlan->tab6) {
            $rightContent[] = ['type' => 'item', 'text' => $tareasLabel . ": " . ($this->workPlan->tab6 ?? '')];
        }

        if ($this->workPlan->tab7 == 'Si' || $this->workPlan->tab8) {
            $rightContent[] = ['type' => 'item', 'text' => $portafolioLabel . ": " . ($this->workPlan->tab8 ?? '')];
        }

        // Selección
        $seleccion = [];
        if ($this->workPlan->sel1 == 'Si') $seleccion[] = $llenarBlancosLabel;
        if ($this->workPlan->sel2 == 'Si') $seleccion[] = $pareoLabel;
        if ($this->workPlan->sel3 == 'Si') $seleccion[] = $ciertoFalsoLabel;
        if ($this->workPlan->sel4 == 'Si') $seleccion[] = $informesLabel;
        if ($this->workPlan->sel5 == 'Si') $seleccion[] = $otroLabel;

        if (!empty($seleccion)) {
            $rightContent[] = ['type' => 'section', 'text' => $seleccionLabel . ":"];
            foreach ($seleccion as $item) {
                $rightContent[] = ['type' => 'item', 'text' => "  " . $item];
            }
        }

        if ($this->workPlan->pro1 == 'Si') {
            $rightContent[] = ['type' => 'item', 'text' => $prontuariosLabel];
        }

        if ($this->workPlan->pro2 == 'Si') {
            $rightContent[] = ['type' => 'item', 'text' => $diariosLabel];
        }

        if ($this->workPlan->otro) {
            $rightContent[] = ['type' => 'item', 'text' => $otroLabel . ": " . $this->workPlan->otro];
        }

        if ($this->workPlan->autoeva) {
            $rightContent[] = ['type' => 'item', 'text' => $autoevaluacionLabel . ": " . $this->workPlan->autoeva];
        }

        return $rightContent;
    }

    private function renderReasonableAccommodations(): void
    {
        $this->Ln(5);

        $acomodos = array_filter([
            $this->workPlan->as1,
            $this->workPlan->as2,
            $this->workPlan->as3,
            $this->workPlan->as4,
            $this->workPlan->as5,
            $this->workPlan->as6,
            $this->workPlan->as7,
            $this->workPlan->as8
        ]);

        if (!empty($acomodos)) {
            $acomodosLabel = __("Acomodos razonables");
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 5, $acomodosLabel . ":", 0, 1);
            $this->SetFont('Arial', '', 8);

            // Dividir en dos columnas
            $totalAcomodos = count($acomodos);
            $mitad = ceil($totalAcomodos / 2);
            $acomodosArray = array_values($acomodos);

            for ($i = 0; $i < $mitad; $i++) {
                // Columna izquierda
                $this->SetX(10);
                $this->Cell(95, 4, '- ' . $acomodosArray[$i], 0, 0);

                // Columna derecha (si existe)
                if (isset($acomodosArray[$i + $mitad])) {
                    $this->Cell(95, 4, '- ' . $acomodosArray[$i + $mitad], 0, 1);
                } else {
                    $this->Ln();
                }
            }
        }
    }
}
