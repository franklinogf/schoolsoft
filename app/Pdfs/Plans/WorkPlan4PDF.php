<?php

namespace App\Pdfs\Plans;

use App\Models\Admin;
use App\Models\WorkPlan4;
use App\Pdfs\PdfInterface;
use Classes\PDF;

class WorkPlan4PDF extends PDF implements PdfInterface
{

    public function __construct(private WorkPlan4 $plan)
    {
        parent::__construct();
        $this->SetTitle('Plan de Artes Visuales', true);
        $this->Fill(0, 0, 0);
    }

    public function generate(): void
    {
        $this->renderPage1();
        $this->renderPage2();
    }

    private function renderPage1(): void
    {
        $this->AddPage();
        // Header
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'PLAN DE TRABAJO - ARTES VISUALES', 0, 1, 'C');
        $this->Ln(5);

        // Basic Information
        $this->renderBasicInfo();
        $this->Ln(5);

        // Fase and Niveles
        $this->renderFaseAndNiveles();
        $this->Ln(5);

        // Estándares and Expectativas
        $this->renderEstandaresAndExpectativas();
        $this->Ln(10);

        // Objetivos and Avalúo
        $this->renderObjetivosAndAvaluo();
    }

    private function renderBasicInfo(): void
    {
        $this->SetFont('Arial', '', 10);

        $unidadLabel = 'Unidad: ';
        $fechaLabel = 'Fecha: ';
        $temasLabel = 'Temas: ';

        $this->Cell($this->GetStringWidth($unidadLabel) + 2, 6, $unidadLabel);
        $this->Cell(100, 5, $this->plan->unidad ?? '', 'B');
        $this->Cell(20);
        $this->Cell($this->GetStringWidth($fechaLabel) + 2, 6, $fechaLabel);
        $this->Cell(50, 5, ($this->plan->fecha1 != '0000-00-00' && $this->plan->fecha1) ? $this->plan->fecha1 : '', 'B', 1, 'C');

        $this->Cell($this->GetStringWidth($temasLabel) + 2, 6, $temasLabel);
        $this->Cell(85, 5, $this->plan->temas ?? '', 'B');
        $this->Cell(35);
        $this->Cell($this->GetStringWidth($fechaLabel) + 2, 6, $fechaLabel);
        $this->Cell(50, 5, ($this->plan->fecha2 != '0000-00-00' && $this->plan->fecha2) ? $this->plan->fecha2 : '', 'B', 1, 'C');

        // Additional dates
        $spaceBefore = $this->GetStringWidth($temasLabel) + 2 + 85 + 35;
        for ($i = 3; $i <= 5; $i++) {
            $this->Cell($spaceBefore);
            $this->Cell($this->GetStringWidth($fechaLabel) + 2, 6, $fechaLabel);
            $dateField = "fecha{$i}";
            $this->Cell(50, 5, ($this->plan->$dateField != '0000-00-00' && $this->plan->$dateField) ? $this->plan->$dateField : '', 'B', 1, 'C');
        }
    }

    private function renderFaseAndNiveles(): void
    {
        $this->SetFont('Arial', '', 12);

        // Left box - Fase
        $this->Rect($this->GetX(), $this->GetY(), 90, 25);
        $f = $this->GetStringWidth('Fase: ');
        $y = $this->GetY();
        $this->Cell($f, 5, 'Fase: ');
        $this->Cell(1);

        $faseLabels = ['Exploración', 'Antes', 'Enfocar'];
        for ($i = 1; $i <= 3; $i++) {
            if ($i > 1) {
                $this->Cell($f);
                $this->Cell(1);
            }
            $checked = ($this->plan->{"fase$i"} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
            $this->Cell(2);
            $this->Cell(87 - $f, 5, $faseLabels[$i - 1], 0, 1);
        }

        // Right box - Niveles
        $this->Rect($this->GetX(), $y, 90, 25);
        $this->SetY($y);
        $this->Cell(90);
        $this->Cell(90, 5, 'Niveles de profundidad de Conocimiento', 0, 1, 'C');
        $this->Ln(5);

        $nivelesLabels = ['Memorístico', 'Estratégico', 'Procesamiento', 'Extendido'];
        $this->Cell(95);
        $checked = ($this->plan->niveles1 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
        $this->Cell(2);
        $this->Cell(35, 5, $nivelesLabels[0], 0, 0);
        $this->Cell(5);
        $checked = ($this->plan->niveles2 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
        $this->Cell(2);
        $this->Cell(35, 5, $nivelesLabels[1], 0, 1);

        $this->Cell(95);
        $checked = ($this->plan->niveles3 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
        $this->Cell(2);
        $this->Cell(35, 5, $nivelesLabels[2], 0, 0);
        $this->Cell(5);
        $checked = ($this->plan->niveles4 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
        $this->Cell(2);
        $this->Cell(35, 5, $nivelesLabels[3], 0, 1);
    }

    private function renderEstandaresAndExpectativas(): void
    {
        // Left box - Estándares
        $this->Rect($this->GetX(), $this->GetY(), 90, 40);
        $y = $this->GetY();
        $this->Cell(90, 5, 'Estándares:', 0, 1);
        $this->Ln(5);

        $estandaresLabels = [
            'Educación Estética',
            'Investigación Histórica, Social y Cultura',
            'Expresión Creativa',
            'Juicio Estético'
        ];

        for ($i = 1; $i <= 4; $i++) {
            $this->Cell(5);
            $checked = ($this->plan->{"estandares$i"} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
            $this->Cell(2);
            $this->Cell(83, 5, $estandaresLabels[$i - 1], 0, 1);
        }

        // Right box - Expectativas (códigos por día)
        $this->Rect($this->GetX(), $y, 90, 40);
        $this->SetY($y);
        $this->Cell(90);
        $this->Cell(90, 5, 'Estándares y Expectativas Códigos', 0, 1, 'C');
        $this->Ln(5);

        $dias = ['Lunes:', 'Martes:', 'Miércoles:', 'Jueves:', 'Viernes:'];
        for ($i = 1; $i <= 5; $i++) {
            $this->Cell(95);
            $this->Cell(20, 5, $dias[$i - 1]);
            $this->Cell(62, 5, $this->plan->{"expectativas$i"} ?? '', 'B', 1);
        }
    }

    private function renderObjetivosAndAvaluo(): void
    {
        // Headers
        $this->Cell(5);
        $this->Cell(90, 5, 'Objetivos');
        $this->Cell(5);
        $x = $this->GetX();
        $this->Cell(90, 5, 'Avalúo', 0, 1);

        $this->Rect($this->GetX(), $this->GetY(), 95, 120);
        $this->Rect($this->GetX() + 95, $this->GetY(), 95, 120);
        $this->SetFont('Arial', '', 10);
        $y = $this->GetY();

        // Left side: Conceptual
        $this->Cell(95, 5, 'Conceptual (Conceptos, Principios, datos, hechos):', 0, 1);
        $this->MultiCell(95, 5, $this->plan->conceptual ?? '', 0, 'L');

        // Right side: Conceptual avalúo items
        $this->SetXY($x, $y);
        $this->renderAvaluoCheckboxes($x, [
            ['avaluo1', 'Preguntas y Respuestas'],
            ['avaluo2', 'Preguntas abiertas'],
            ['avaluo3', 'Rúbrica'],
            ['avaluo4', 'Portafolio'],
            ['avaluo5', 'Ensayo'],
            ['avaluo6', 'Tareas de ejecución'],
            ['avaluo7', 'Tareas escritas']
        ]);
        $this->Ln(5);

        // Procedimental section
        $y2 = $this->GetY();
        $this->SetX(10);
        $this->SetFont('Arial', '', 9.5);
        $this->Cell(95, 5, 'Procedimental (Procesos, habilidades, estrategias, destrezas):', 0, 1);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(95, 5, $this->plan->procedimental ?? '', 0, 'L');

        // Procedimental avalúo
        $this->SetXY($x, $y2);
        $this->renderAvaluoCheckboxes($x, [
            ['avaluo8', 'Prueba Diagnóstica'],
            ['avaluo9', 'Prueba Corta'],
            ['avaluo10', 'Examen'],
            ['avaluo11', 'Propuesta de la Investigación'],
            ['avaluo12', 'Proyecto'],
            ['avaluo13', 'Informe oral o escrito']
        ]);
        $this->Ln(5);

        // Actitudinal section
        $y3 = $this->GetY();
        $this->SetX(10);
        $this->Cell(95, 5, 'Actitudinal (Actitudes, Valores, Normas):', 0, 1);
        $this->MultiCell(95, 5, $this->plan->actitudinal ?? '', 0, 'L');

        // Actitudinal avalúo
        $this->SetXY($x, $y3);
        $this->renderAvaluoCheckboxes($x, [
            ['avaluo14', 'Dibujo'],
            ['avaluo15', 'Pintura'],
            ['avaluo16', 'Escultura'],
            ['avaluo17', 'Grabado'],
            ['avaluo18', 'Fotografía'],
            ['avaluo19', 'Tirilla cómica']
        ]);

        $this->SetX($x);
        $checked = ($this->plan->avaluo20 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
        $this->Cell(2);
        $this->Cell(10, 5, 'Otros:');
        $this->Cell(75, 5, $this->plan->avaluo201 ?? '', 'B', 1);
    }

    private function renderAvaluoCheckboxes(float $x, array $items): void
    {
        foreach ($items as $item) {
            $this->SetX($x);
            $checked = ($this->plan->{$item[0]} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX(), $this->GetY() + 1.2, 2, 2, $checked);
            $this->Cell(2);
            $this->Cell(90, 5, $item[1], 0, 1);
        }
    }

    private function renderPage2(): void
    {
        $this->AddPage();
        // Metodologías Section
        $this->renderMetodologias();
        $this->Ln(3);

        // Actividades Section
        $this->renderActividades();
        $this->Ln(2);

        // Acomodo Razonable Section
        $this->renderAcomodoRazonable();
        $this->Ln(2);

        // Materiales Section
        $this->renderMateriales();
    }

    private function renderMetodologias(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'Metodologías:', 0, 1);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(47.5, 5, 'Comprensión Lectora', 1, 0, 'C');
        $this->Cell(47.5, 5, 'Aprendizaje Cooperativo', 1, 0, 'C');
        $this->Cell(47.5, 5, 'Aprendizaje Basado en Problema', 1, 0, 'C');
        $this->Cell(47.5, 5, 'Integración Curricular', 1, 1, 'C');

        $this->SetFont('Arial', '', 8);
        $cellHeight = 4;
        $startY = $this->GetY();

        // Column 1: Comprensión Lectora
        $comprensionItems = [
            'Lectura en voz alta',
            'Lectura Dirigida',
            'Lectura compartida',
            'Escritura Interactiva'
        ];

        $this->SetXY(10, $startY);
        foreach ($comprensionItems as $i => $item) {
            $fieldNum = $i + 1;
            $checked = ($this->plan->{"comprension$fieldNum"} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
            $this->Cell(2.5);
            $this->Cell(44, $cellHeight, $item, 0, 1);
            $this->SetX(10);
        }

        // Column 2: Aprendizaje Cooperativo
        $this->SetXY(57.5, $startY);
        $checked = ($this->plan->aprendizaje == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
        $this->Cell(2.5);
        $this->Cell(44, $cellHeight, 'Tutoría entre pares', 0, 0);

        // Column 3: Aprendizaje Basado en Problema
        $this->SetXY(105, $startY);
        $checked = ($this->plan->aprendizaje_problema == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
        $this->Cell(2.5);
        $this->MultiCell(44, $cellHeight, 'Enseñanza Contextualizada', 0, 'L');

        // Column 4: Integración Curricular
        $this->renderIntegracionCurricular($startY);

        // Otras field
        $this->SetX(10);
        $checked = ($this->plan->integracion8 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
        $this->Cell(2.5);
        $this->Cell(10, 4, 'Otras:', 0, 0);
        $this->Cell(177.5, 4, $this->plan->integracion81 ?? '', 'B', 1);
    }

    private function renderIntegracionCurricular(float $startY): void
    {
        $integrationItems = [
            ['label' => 'Integración a la tecnología', 'field' => 'integracion1'],
            ['label' => 'Integración Curricular', 'field' => 'integracion2'],
            ['label' => 'Español', 'field' => 'integracion3'],
            ['label' => 'Matemáticas', 'field' => 'integracion4'],
            ['label' => 'Inglés', 'field' => 'integracion5'],
            ['label' => 'Ciencias', 'field' => 'integracion6'],
            ['label' => 'Estudios Sociales', 'field' => 'integracion7']
        ];

        $this->SetXY(152.5, $startY);
        $cellHeight = 4;

        foreach ($integrationItems as $i => $item) {
            if ($i == 2) {
                // Español and Matemáticas on same row
                $checked = ($this->plan->{$item['field']} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(20, $cellHeight, $item['label'], 0, 0);

                // Matemáticas
                $item = $integrationItems[3];
                $checked = ($this->plan->{$item['field']} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(20, $cellHeight, $item['label'], 0, 1);
                $this->SetX(152.5);
            } elseif ($i == 4) {
                // Inglés and Ciencias on same row
                $checked = ($this->plan->{$item['field']} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(20, $cellHeight, $item['label'], 0, 0);

                // Ciencias
                $item = $integrationItems[5];
                $checked = ($this->plan->{$item['field']} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(20, $cellHeight, $item['label'], 0, 1);
                $this->SetX(152.5);
            } elseif ($i < 2 || $i == 6) {
                // Normal single item rows
                $checked = ($this->plan->{$item['field']} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(44, $cellHeight, $item['label'], 0, 1);
                $this->SetX(152.5);
            }
        }
    }

    private function renderActividades(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'Actividades:', 0, 1);
        $this->Ln(1);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(63, 5, 'Inicio', 1, 0, 'C');
        $this->Cell(63, 5, 'Desarrollo', 1, 0, 'C');
        $this->Cell(64, 5, 'Cierre', 1, 1, 'C');

        $this->SetFont('Arial', '', 8);

        $inicioItems = [
            'Actividad de Rutina (Bienvenida, Saludo, Canción)',
            'Presentación del tema',
            'Discusión de Asignación',
            'Canción',
            'Juego',
            'Problema del Día',
            'Adivinanza',
            'Discusión de Noticia, Asignación o tema de actualidad',
            'Repaso Conceptos Discutidos',
            'Observación y Estudio',
            'Reflexión'
        ];

        $desarrolloItems = [
            'Introducción de Vocabulario',
            'Estudio Supervisado o dirigido',
            'Práctica',
            'Lectura y análisis',
            'Informe Oral',
            'Formular y/o contestar preguntas',
            'Resolver ejercicios de práctica',
            'Prueba Corta',
            'Competencia',
            'Debate',
            'Examen'
        ];

        $cierreItems = [
            'Resumir Material Estudio',
            'Discusión del trabajo asignado',
            'Trabajo en grupo',
            'Corrección y evaluación del trabajo',
            'Aclarar dudas de la destreza',
            'Copiar material en la libreta',
            'Ejercicios o actividades para comprobar aprendizaje'
        ];

        $maxActividadRows = max(count($inicioItems) + 1, count($desarrolloItems) + 1, count($cierreItems) + 1);
        $startY = $this->GetY();
        $currentY = 0;

        for ($i = 0; $i < $maxActividadRows; $i++) {
            $rowHeight = 4;

            // Column 1: Inicio
            $this->SetXY(10, $startY + $currentY);
            if ($i < count($inicioItems)) {
                $fieldNum = $i + 1;
                $checked = ($this->plan->{"inicio$fieldNum"} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                if ($this->GetStringWidth($inicioItems[$i]) > 55) {
                    $this->MultiCell(60, 4, $inicioItems[$i], 0, 'L');
                    $rowHeight = 8;
                } else {
                    $this->Cell(60, 4, $inicioItems[$i], 0, 0);
                }
            } elseif ($i == count($inicioItems)) {
                $checked = ($this->plan->inicio12 == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(10, 4, 'Otro:', 0, 0);
                $this->Cell(50, 4, $this->plan->inicio121 ?? '', 'B', 0);
            }

            // Column 2: Desarrollo
            $this->SetXY(73, $startY + $currentY);
            if ($i < count($desarrolloItems)) {
                $fieldNum = $i + 1;
                $checked = ($this->plan->{"desarrollo$fieldNum"} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                if ($this->GetStringWidth($desarrolloItems[$i]) > 55) {
                    $this->MultiCell(60, 4, $desarrolloItems[$i], 0, 'L');
                } else {
                    $this->Cell(60, 4, $desarrolloItems[$i], 0, 0);
                }
            } elseif ($i == count($desarrolloItems)) {
                $checked = ($this->plan->desarrollo12 == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(10, 4, 'Otro:', 0, 0);
                $this->Cell(50, 4, $this->plan->desarrollo121 ?? '', 'B', 0);
            }

            // Column 3: Cierre
            $this->SetXY(136, $startY + $currentY);
            if ($i < count($cierreItems)) {
                $fieldNum = $i + 1;
                $checked = ($this->plan->{"cierre$fieldNum"} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                if ($this->GetStringWidth($cierreItems[$i]) > 55) {
                    $this->MultiCell(61, 4, $cierreItems[$i], 0, 'L');
                } else {
                    $this->Cell(61, 4, $cierreItems[$i], 0, 0);
                }
            } elseif ($i == count($cierreItems)) {
                $checked = ($this->plan->cierre8 == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX() + 1, $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(10, 4, 'Otro:', 0, 0);
                $this->Cell(51, 4, $this->plan->cierre81 ?? '', 'B', 0);
            }

            $currentY += $rowHeight;
        }

        $this->SetY($startY + $currentY + 3);
    }

    private function renderAcomodoRazonable(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'Acomodo Razonable:', 0, 1);
        $this->SetFont('Arial', '', 8);

        $acomodoItems = [
            'Tiempo y Medio',
            'Ubicación Pupitre',
            'Adaptar a la institución',
            'Servicio Suplementario de Apoyo',
            'Fragmentar trabajos y/o exámenes'
        ];

        for ($i = 1; $i <= count($acomodoItems); $i++) {
            $checked = ($this->plan->{"acomodo$i"} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
            $this->Cell(2.5);
            $this->Cell(90, 4, $acomodoItems[$i - 1], 0, 1);
        }

        $checked = ($this->plan->acomodo6 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
        $this->Cell(2.5);
        $this->Cell(10, 4, 'Otro:', 0, 0);
        $this->Cell(177.5, 4, $this->plan->acomodo61 ?? '', 'B', 1);
    }

    private function renderMateriales(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'Materiales:', 0, 1);
        $this->SetFont('Arial', '', 8);

        $materialesItems = [
            'Libreta',
            'Crayolas',
            'Tijera',
            'Pega',
            'Delantal',
            'Pinceles',
            'Tempera',
            'Cartulinas de colores',
            'Lápices #2B o 2B-6B',
            'Saca puntas',
            'Borra',
            'Libreta de dibujo multiuso',
            'Libreta de acuarela',
            'Carboncillo comprimido',
            'Lápices de colores',
            'Pasteles a color con aceite y sin aceite'
        ];

        $halfCount = ceil(count($materialesItems) / 2);
        $startY = $this->GetY();

        for ($i = 0; $i < $halfCount; $i++) {
            // Left column
            $this->SetXY(10, $startY + ($i * 4));
            $fieldNum = $i + 1;
            $checked = ($this->plan->{"materiales$fieldNum"} == 'Si') ? 'DF' : 'D';
            $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
            $this->Cell(2.5);
            $this->Cell(92.5, 4, $materialesItems[$i], 0, 0);

            // Right column
            if (($i + $halfCount) < count($materialesItems)) {
                $fieldNum = $i + $halfCount + 1;
                $checked = ($this->plan->{"materiales$fieldNum"} == 'Si') ? 'DF' : 'D';
                $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
                $this->Cell(2.5);
                $this->Cell(92.5, 4, $materialesItems[$i + $halfCount], 0, 1);
            } else {
                $this->Ln(4);
            }
        }

        // Otros field
        $checked = ($this->plan->materiales17 == 'Si') ? 'DF' : 'D';
        $this->Rect($this->GetX(), $this->GetY() + 0.8, 2, 2, $checked);
        $this->Cell(2.5);
        $this->Cell(10, 4, 'Otro:', 0, 0);
        $this->Cell(177.5, 4, $this->plan->materiales171 ?? '', 'B', 1);
    }
}
