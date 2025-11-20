<?php

namespace App\Pdfs\Plans;

use App\Models\ClassPlan;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class ClassPlanPDF extends PDF implements PdfInterface
{
    public function __construct(private ClassPlan $plan)
    {
        parent::__construct();
        $this->SetAutoPageBreak(true, 5);
        $this->useFooter(false);
        $this->SetTitle('Planificación por Unidad - ' . $plan->tema, true);
    }

    public function generate(): void
    {
        $this->renderPage1();
        $this->renderPage2();
        $this->renderPage3();
        $this->renderPage4();
        $this->renderPage5();
        $this->renderPage6();
    }

    private function renderPage1(): void
    {
        $this->AddPage('L');
        $this->renderHeader();
        $this->renderMainTable();
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'PLANIFICACIÓN POR UNIDAD', 0, 1, "C");
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 280, $this->GetY());
        $this->Ln();

        $materiaLabel = 'Materia:';
        $this->Cell($this->GetStringWidth($materiaLabel) + 3, 5, $materiaLabel);
        $this->Cell(20, 5, $this->plan->materia, "B");
        $gradoLabel = 'Grado:';
        $this->Cell($this->GetStringWidth($gradoLabel) + 3, 5, $gradoLabel);
        $this->Cell(10, 5, '', "B", 0, 'C');
        $maestroLabel = 'Maestro(a):';
        $this->Cell($this->GetStringWidth($maestroLabel) + 3, 5, $maestroLabel);
        $this->Cell(90, 5, $this->plan->profesor, 'B', 1);
        $this->Ln(2);

        $temaLabel = 'Tema de la unidad:';
        $this->Cell($this->GetStringWidth($temaLabel) + 3, 5, $temaLabel);
        $this->Cell(0, 5, $this->plan->tema, 'B', 1);
        $this->Ln(2);

        $fechaLabel = 'Fecha:';
        $this->Cell($this->GetStringWidth($fechaLabel) + 3, 5, $fechaLabel);
        $this->Cell(25, 5, $this->plan->fecha, "B", 0, 'C');
        $duracionLabel = 'Duración:';
        $this->Cell($this->GetStringWidth($duracionLabel) + 3, 5, $duracionLabel);
        $this->Cell(10, 5, $this->plan->duracion, "B", 0, 'C');
        $this->Cell(10, 5, 'semanas', 0, 1);
        $this->Ln(2);

        $estrategiaLabel = 'Estrategia';
        $this->Cell($this->GetStringWidth($estrategiaLabel) + 3, 5, $estrategiaLabel);
        $this->Cell(0, 5, $this->plan->estrategia, 'B', 1);
        $this->Ln();
    }

    private function renderMainTable(): void
    {
        $this->SetFont('Arial', '', 10);
        $col1 = 30;
        $col2 = 120;
        $col3 = 130;

        // Headers
        $this->Rect($this->GetX(), $this->GetY(), $col1, 5);
        $this->Cell($col1, 5, 'Etapa', 0, 0, 'C');
        $this->Rect($this->GetX(), $this->GetY(), $col2, 5);
        $this->Cell($col2, 5, 'Actividades para el logro de las tareas de desempeño (Visión Macro)', 0, 0, 'C');
        $this->Rect($this->GetX(), $this->GetY(), $col3, 5);
        $this->Cell($col3, 5, 'Tareas de Desempeño u otra evidencia', 0, 1, 'C');

        $y = $this->GetY();
        $x = $col1 + $col2 + 10;

        // Antes
        $this->renderStage('Antes', $this->plan->antes, $col1, $col2, 30);

        // Durante
        $this->renderStage('Durante', $this->plan->durante, $col1, $col2, 30);

        // Después
        $this->renderStage('Después', $this->plan->despues, $col1, $col2, 30);

        // Tasks column
        $this->renderTasksColumn($x, $y, $col3);
    }

    private function renderStage(string $label, ?string $content, int $col1, int $col2, int $height): void
    {
        $this->Rect($this->GetX(), $this->GetY(), $col1, $height);
        $this->Cell($col1, $height, $label, 0, 0, 'C');
        $this->Rect($this->GetX(), $this->GetY(), $col2, $height);
        $this->MultiCell($col2, 5, $content ?? '', 0, 'L');
        $this->Ln($height - 5);
    }

    private function renderTasksColumn(float $x, float $y, int $col3): void
    {
        $this->SetXY($x, $y);
        $this->Rect($this->GetX(), $this->GetY(), $col3, 90);
        $this->Cell(5);

        $tasks = [
            ['field' => 'tarea1', 'label' => 'Prueba'],
            ['field' => 'tarea2', 'label' => 'Quizz'],
            ['field' => 'tarea3', 'label' => 'Proyecto'],
            ['field' => 'tarea4', 'label' => 'Mapa de conceptos'],
            ['field' => 'tarea5', 'label' => 'Organizador gráfico', 'extra' => 't5'],
            ['field' => 'tarea6', 'label' => 'Ejercicios de práctica'],
            ['field' => 'tarea7', 'label' => 'Tirilla cómica'],
            ['field' => 'tarea8', 'label' => 'Pregunta abierta'],
            ['field' => 'tarea9', 'label' => 'Laboratorio'],
            ['field' => 'tarea10', 'label' => 'Construcción de modelos'],
            ['field' => 'tarea11', 'label' => 'Debate'],
            ['field' => 'tarea12', 'label' => 'Dibujo'],
            ['field' => 'tarea13', 'label' => 'Trabajo Creativo'],
            ['field' => 'tarea14', 'label' => 'Otros', 'extra' => 't14'],
        ];

        foreach ($tasks as $task) {
            $this->drawCheckbox($this->plan->{$task['field']} == 'si', y: 1.3);
            $this->Cell(2);

            if (isset($task['extra'])) {
                $this->Cell($this->GetStringWidth($task['label']) + 2, 5, $task['label']);
                $this->Cell(0, 5, $this->plan->{$task['extra']} ?? '', 'B', 1);
                $this->SetX($x);
                $this->Cell(5);
            } else {
                $this->Cell(0, 5, $task['label'], 0, 1);
                $this->SetX($x);
                $this->Cell(5);
            }
        }
    }

    private function renderPage2(): void
    {
        $this->AddPage('L');
        $this->renderWeeklyGuideHeader();
        $this->renderWeeklyTable();
    }

    private function renderWeeklyGuideHeader(): void
    {
        $this->SetFont('Arial', '', 10);
        $this->Rect($this->GetX(), $this->GetY(), 280, 5);
        $this->Cell(0, 5, 'Guía Semanal del Maestro', 0, 1, 'C');

        $x = $this->GetX();
        $this->Rect($this->GetX(), $this->GetY(), 280, 5);
        $this->Cell(0, 5, "Fecha: {$this->plan->fechaG}");
        $this->SetX($x);
        $this->Cell(0, 5, "Duración: {$this->plan->duracionG} semanas", 0, 0, 'C');
        $this->SetX($x);
        $this->Cell(0, 5, "Valor de la semana: {$this->plan->valorG}", 0, 1, 'R');
    }

    private function renderWeeklyTable(): void
    {
        $col1 = 30;
        $cols = 50;
        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        // Days header
        $this->Rect($this->GetX(), $this->GetY(), $col1, 5);
        $this->Cell($col1, 5, "Días de la semana", 0, 0, 'C');
        foreach ($days as $day) {
            $this->Rect($this->GetX(), $this->GetY(), $cols, 5);
            $this->Cell($cols, 5, $day, 0, 0, 'C');
        }
        $this->Ln();

        // Estándares
        $this->renderWeeklyRow('Estándares (PRCS)', 'estandares', $col1, $cols, 20);

        // Expectativa
        $this->renderWeeklyRow('Expectativa', 'expectativa', $col1, $cols, 20);

        // Objetivos
        $this->renderWeeklyRow('Objetivos', 'objetivos', $col1, $cols, 20);

        // Nivel de pensamiento
        $this->renderThinkingLevels($col1, $cols);

        // Antes
        $this->renderWeeklyRow('Antes', 'antes', $col1, $cols, 20);

        // Durante
        $this->renderWeeklyRow('Durante', 'durante', $col1, $cols, 20);

        // Después
        $this->renderWeeklyRow('Después', 'despues', $col1, $cols, 20);
    }

    private function renderWeeklyRow(string $label, string $fieldPrefix, int $col1, int $cols, int $height): void
    {
        $this->Rect($this->GetX(), $this->GetY(), $col1, $height);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 8, $label, 0, 'C');

        $x += $col1;
        for ($i = 1; $i <= 5; $i++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, $height);
            $this->MultiCell($cols, 5, $this->plan->{"{$fieldPrefix}{$i}"} ?? '', 0, 'L');
            $x += $cols;
        }

        $this->SetY($y + $height);
    }

    private function renderThinkingLevels(int $col1, int $cols): void
    {
        $this->Rect($this->GetX(), $this->GetY(), $col1, 25);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Cell($col1, 25, "Semana", 0, 0, 'C');
        $x += $col1;

        $levels = [
            '1 Memorístico',
            '2 Procesamiento',
            '3 Estratégico',
            '4 Extendido'
        ];

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 25);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 5);
            $this->Cell($cols, 5, 'Actividades de Aprendizaje', 0, 1, 'C');

            foreach ($levels as $idx => $level) {
                $levelNum = $idx + 1;
                $this->SetX($x);
                $this->Cell(3);
                $this->drawCheckbox($this->plan->{"pensamiento{$day}_{$levelNum}"} == 'si', y: 1.2);
                $this->Cell($cols - 2, 5, $level, 0, 1, 'C');
            }

            $x += $cols;
        }

        $this->SetY($y + 25);
    }

    private function renderPage3(): void
    {
        $this->AddPage('L');
        $this->renderAcademicStrategies();
        $this->renderValues();
        $this->renderDifferentiatedEducation();
    }

    private function renderAcademicStrategies(): void
    {
        $col1 = 30;
        $cols = 50;

        $this->Rect($this->GetX(), $this->GetY(), $col1, 25);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 9, "Estrategia académica", 0, 'C');
        $x += $col1;

        $strategies = [
            'Aprendizaje basado en problemas',
            'Trabajo cooperativo',
            'Ciclos de aprendizaje',
            'ECA'
        ];

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 25);

            foreach ($strategies as $idx => $strategy) {
                $stratNum = $idx + 1;
                $this->SetX($x);
                $this->Cell(3);
                $this->drawCheckbox($this->plan->{"estrategia_a{$day}_{$stratNum}"} == 'si', y: 1.2);
                $this->Cell(4);

                if ($stratNum === 4) {
                    $this->Cell($this->GetStringWidth('ECA:'), 5, 'ECA:', 0, 0, 'L');
                    $this->Cell($cols - $this->GetStringWidth('ECA:') - 4, 4, $this->plan->{"estrategia_a{$day}_41"} ?? '', 'B', 1, 'C');
                } else {
                    $this->MultiCell($cols - 4, 5, $strategy, 0, 'L');
                }
            }

            $x += $cols;
        }

        $this->SetY($y + 25);
        $this->Ln(1);
    }

    private function renderValues(): void
    {
        $col1 = 30;
        $cols = 50;

        $this->Rect($this->GetX(), $this->GetY(), $col1, 20);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Cell($col1, 20, "Valores", 0, 0, 'C');
        $x += $col1;

        for ($i = 1; $i <= 5; $i++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 20);
            $this->MultiCell($cols, 5, $this->plan->{"valores{$i}"} ?? '', 0, 'L');
            $x += $cols;
        }

        $this->SetY($y + 20);
    }

    private function renderDifferentiatedEducation(): void
    {
        $col1 = 30;
        $cols = 50;

        $educationStrategies = [
            'Tiempo adicional',
            'Ubicación de pupitre',
            'Fragmentar trabajos',
            'Trabajo ind / grupal',
            'Material complementario',
            'Traducción de material',
            'Investigación grupal',
            'Tareas de inteligencias múltiples',
            'Diccionario',
            'Diversos modos de expresión',
            'Intrucciones claras y precisas',
            'Proveer ejemplos',
            'Otros'
        ];

        $this->Rect($this->GetX(), $this->GetY(), $col1, 70);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Estrategia de educación diferenciada", 0, 'C');
        $x += $col1;

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 70);

            foreach ($educationStrategies as $i => $estrategia) {
                $colIndex = $i + 1;
                $this->SetX($x);
                $field = "estrategia_e{$day}_{$colIndex}";
                $this->Cell(3);
                $this->drawCheckbox($this->plan->$field == 'si', y: 1.2);
                $this->Cell(2.5);

                if ($colIndex === 13) {
                    $this->Cell($this->GetStringWidth('Otros:'), 5, 'Otros:', 0, 0, 'L');
                    $this->Cell($cols - $this->GetStringWidth('Otros:') - 4, 4, $this->plan->{"estrategia_e{$day}_131"} ?? '', 'B', 1, 'L');
                } elseif ($this->GetStringWidth($estrategia) > 50) {
                    $this->MultiCell($cols - 4, 5, $estrategia, 0, 'L');
                } else {
                    $this->Cell($cols - 4, 5, $estrategia, 0, 1, 'L');
                }
            }

            $x += $cols;
        }
    }

    private function renderPage4(): void
    {
        $this->AddPage('L');
        $this->renderConcepts();
        $this->renderCrossThemes();
    }

    private function renderConcepts(): void
    {
        $col1 = 30;
        $cols = 50;

        $concepts = [
            'Formula preguntas y define problemas.',
            'Desarrolla y usa modelos.',
            'Planifica y lleva a cabo experimentos e investigaciones.',
            'Analiza e interpreta datos.',
            'Usa pensamiento matemático y computacional.',
            'Propone explicaciones y diseña soluciones.',
            'Expone argumentos a partir de evidencia confiable.',
            'Obtiene, evalúa y comunica información.'
        ];

        $this->Rect($this->GetX(), $this->GetY(), $col1, 80);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Conceptos y destrezas", 0, 'C');
        $x += $col1;

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 80);

            foreach ($concepts as $i => $concepto) {
                $colIndex = $i + 1;
                $this->SetX($x);
                $field = "conceptos{$day}_{$colIndex}";
                $this->Cell(3);
                $this->drawCheckbox($this->plan->$field == 'si', y: 1.2);
                $this->Cell(2.5);

                if ($this->GetStringWidth($concepto) > 50) {
                    $this->MultiCell($cols - 4, 5, $concepto, 0, 'L');
                } else {
                    $this->Cell($cols - 4, 5, $concepto, 0, 1, 'L');
                }
            }

            $x += $cols;
        }
    }

    private function renderCrossThemes(): void
    {
        $col1 = 30;
        $cols = 50;

        $crossThemes = [
            'Patrones.',
            'Causa y efecto',
            'Escala, proporción y cantidad',
            'Sistemas y modelos de sistemas',
            'Energía y materia',
            'Estructura y función',
            'Estabilidad y cambio',
            'Ética y valores en las ciencias'
        ];

        $this->Rect($this->GetX(), $this->GetY(), $col1, 55);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Temas transversales", 0, 'C');
        $x += $col1;

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 55);

            foreach ($crossThemes as $i => $tema) {
                $colIndex = $i + 1;
                $this->SetX($x);
                $field = "temas{$day}_{$colIndex}";
                $this->Cell(3);
                $this->drawCheckbox($this->plan->$field == 'si', y: 1.2);
                $this->Cell(2.5);

                if ($this->GetStringWidth($tema) > 50) {
                    $this->MultiCell($cols - 4, 5, $tema, 0, 'L');
                } else {
                    $this->Cell($cols - 4, 5, $tema, 0, 1, 'L');
                }
            }

            $x += $cols;
        }
    }

    private function renderPage5(): void
    {
        $this->AddPage('L');
        $this->renderMaterials();
        $this->renderAssignments();
        $this->renderReflection();
    }

    private function renderMaterials(): void
    {
        $col1 = 30;
        $cols = 50;

        $materials = [
            ['label' => 'Computadora y Proyector.', 'hasInput' => false],
            ['label' => 'Material Fotocopiado', 'hasInput' => false],
            ['label' => 'Libro:', 'hasInput' => true],
            ['label' => 'Equipo de:', 'hasInput' => true],
            ['label' => 'Video / Película:', 'hasInput' => true],
            ['label' => 'Manipulativos', 'hasInput' => false],
            ['label' => 'Otros:', 'hasInput' => true]
        ];

        $this->Rect($this->GetX(), $this->GetY(), $col1, 60);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Materiales o recursos", 0, 'C');
        $x += $col1;

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 60);

            foreach ($materials as $i => $material) {
                $colIndex = $i + 1;
                $this->SetX($x);
                $field = "materiales{$day}_{$colIndex}";
                $this->Cell(3);
                $this->drawCheckbox($this->plan->$field == 'si', y: 1.2);
                $this->Cell(2.5);
                $this->Cell($cols - 4, 5, $material['label'], 0, 1, 'L');

                if ($material['hasInput']) {
                    $this->SetX($x);
                    $textField = "materiales{$day}_{$colIndex}1";
                    $width = ($colIndex == 5) ? $cols : ($cols - 4);
                    $this->Cell($width, 5, $this->plan->$textField ?? '', 'B', 1, 'L');
                }
            }

            $x += $cols;
        }

        $this->Ln(5);
    }

    private function renderAssignments(): void
    {
        $col1 = 30;
        $cols = 50;

        $this->Rect($this->GetX(), $this->GetY(), $col1, 35);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Asignaciones / Tareas especiales (opcional)", 0, 'C');
        $x += $col1;

        $assignments = ['Práctica:', 'Preparación', 'Elaboración:'];

        for ($day = 1; $day <= 5; $day++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 35);

            foreach ($assignments as $i => $assignment) {
                $assignNum = $i + 1;
                $this->SetX($x);
                $this->Cell(3);
                $this->drawCheckbox($this->plan->{"tareas{$day}_{$assignNum}"} == 'si', y: 1.2);
                $this->Cell(2.5);
                $this->Cell($cols - 4, 5, $assignment, 0, 1, 'L');
                $this->SetX($x);
                $this->Cell($cols - 4, 5, $this->plan->{"tareas{$day}_{$assignNum}1"} ?? '', 'B', 1, 'L');
            }

            $x += $cols;
        }

        $this->Ln(5);
    }

    private function renderReflection(): void
    {
        $col1 = 30;
        $cols = 50;

        $this->Rect($this->GetX(), $this->GetY(), $col1, 35);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->MultiCell($col1, 6, "Reflexión sobre la praxis", 0, 'C');
        $x += $col1;

        $reflexionText = 'Se cumplió con el plan Necesidad de re-enseñanza Maestra ausente Reunión profesional Inclemencias del tiempo';

        for ($i = 1; $i <= 5; $i++) {
            $this->SetXY($x, $y);
            $this->Rect($this->GetX(), $this->GetY(), $cols, 35);
            $this->MultiCell($cols, 5, $reflexionText, 0, 'L');
            $x += $cols;
        }
    }

    private function renderPage6(): void
    {
        $this->AddPage('L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'Actividades', 0, 1, 'C');
        $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 280, $this->GetY());
        $this->Ln(2);

        $this->renderActivitiesSection('Antes', $this->getBeforeActivities());
        $this->Ln(2);
        $this->renderActivitiesSection('Durante', $this->getDuringActivities());
        $this->Ln(2);
        $this->renderActivitiesSection('Después', $this->getAfterActivities());
    }

    private function renderActivitiesSection(string $title, array $activities): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, $title, 0, 1);
        $this->SetFont('Arial', '', 10);

        foreach ($activities as $activity) {
            $this->drawCheckbox($this->plan->{$activity['field']} == 'si', y: 1.3);
            $this->Cell(4);

            if (isset($activity['extra'])) {
                $this->Cell($this->GetStringWidth($activity['label']), 5, $activity['label']);
                $this->Cell(3);
                $extraValue = $this->plan->{$activity['extra']} ?? '';
                $this->Cell($this->GetStringWidth($extraValue) + 5, 5, $extraValue, 'B', 1);
            } else {
                $this->Cell(0, 5, $activity['label'], 0, 1);
            }
        }
    }

    private function getBeforeActivities(): array
    {
        return [
            ['field' => 'actividad_antes1', 'label' => 'Actividad de rutina / saludo'],
            ['field' => 'actividad_antes2', 'label' => 'Repasar la clase anterior', 'extra' => 'actividad_antes21'],
            ['field' => 'actividad_antes3', 'label' => 'Introducción al tema o destreza'],
            ['field' => 'actividad_antes4', 'label' => 'Discusión de la asignación'],
            ['field' => 'actividad_antes5', 'label' => 'Discusión de:', 'extra' => 'actividad_antes51'],
            ['field' => 'actividad_antes6', 'label' => 'Preguntas abiertas'],
            ['field' => 'actividad_antes7', 'label' => 'Reflexión'],
            ['field' => 'actividad_antes8', 'label' => 'Otro:', 'extra' => 'actividad_antes81']
        ];
    }

    private function getDuringActivities(): array
    {
        return [
            ['field' => 'actividad_durante1', 'label' => 'Discusión detallada del tema en Power Point.'],
            ['field' => 'actividad_durante2', 'label' => 'Contestación y discusión de ejercicios de práctica.'],
            ['field' => 'actividad_durante3', 'label' => 'Lectura y discusión de lectura'],
            ['field' => 'actividad_durante4', 'label' => 'Trabajo en grupo'],
            ['field' => 'actividad_durante5', 'label' => 'Presentación de un informe oral por el estudiante'],
            ['field' => 'actividad_durante6', 'label' => 'Laboratorio'],
            ['field' => 'actividad_durante7', 'label' => 'Debate'],
            ['field' => 'actividad_durante8', 'label' => 'Prueba o Quizz'],
            ['field' => 'actividad_durante9', 'label' => 'Otro:', 'extra' => 'actividad_durante91']
        ];
    }

    private function getAfterActivities(): array
    {
        return [
            ['field' => 'actividad_despues1', 'label' => 'Resumen de la clase'],
            ['field' => 'actividad_despues2', 'label' => 'Discusión de ejercicios'],
            ['field' => 'actividad_despues3', 'label' => 'Trabajo en la libreta'],
            ['field' => 'actividad_despues4', 'label' => 'Instrucciones para el día siguiente'],
            ['field' => 'actividad_despues5', 'label' => 'Técnica de avaluó'],
            ['field' => 'actividad_despues6', 'label' => 'Aclarar dudas'],
            ['field' => 'actividad_despues7', 'label' => 'Otro:', 'extra' => 'actividad_despues71']
        ];
    }
}
