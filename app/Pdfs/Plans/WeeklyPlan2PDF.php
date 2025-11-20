<?php

namespace App\Pdfs\Plans;

use App\Models\Teacher;
use App\Models\WeeklyPlan2;
use Classes\PDF;

class WeeklyPlan2PDF extends PDF
{

    public function __construct(private WeeklyPlan2 $plan)
    {
        parent::__construct();

        $this->SetTitle(__("Plan Semanal 2") . " - " . $plan->tema, true);
        $this->SetAutoPageBreak(true, 15);
        $this->Fill();
    }

    public function generate(): void
    {
        $this->AddPage();
        $this->renderHeader();
        $this->renderGeneralInfo();
        $this->renderStandards();
        $this->renderObjectives();
        $this->renderSkills();
        $this->renderCommonStandards();
        $this->renderSupports();

        $this->AddPage();
        $this->renderAssessment();
        $this->renderValues();
        $this->renderAccommodations();
        $this->renderWeeklyWork();
        $this->renderReview();
    }

    private function renderHeader(): void
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, __("PLAN DE LECCIÓN BISEMANAL"), 0, 1, 'C');
        $this->Ln(3);
    }

    private function renderGeneralInfo(): void
    {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("INFORMACIÓN GENERAL"), 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        $this->Cell(63.33, 6, __("Nombre") . ": " . $this->plan->teacher->nombre . ', ' . $this->plan->teacher->apellidos, 1, 0);
        $this->Cell(63.33, 6, __("Asignatura") . ": " . $this->plan->asignatura, 1, 0);
        $this->Cell(63.33, 6, __("Grado") . ": " . $this->plan->grado, 1, 1);

        $this->Cell(63.33, 6, __("Fecha") . ": " . $this->plan->fecha, 1, 0);
        $this->Cell(63.33, 6, __("Desde") . ": " . $this->plan->desde, 1, 0);
        $this->Cell(63.33, 6, __("Hasta") . ": " . $this->plan->hasta, 1, 1);

        $this->Ln(2);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 6, __("Tema") . ":", 0, 0);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, $this->plan->tema, 'B', 1);

        $this->Ln(2);
    }

    private function renderStandards(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, __("ESTÁNDARES"), 0, 1);
        $this->SetFont('Arial', '', 8);

        for ($i = 1; $i <= 3; $i++) {
            $field = "estandares{$i}";
            if ($this->plan->$field) {
                $this->Cell(0, 5, "{$i}. " . $this->plan->$field, 'B', 1);
            }
        }

        $this->Ln(2);
    }

    private function renderObjectives(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, __("OBJETIVOS"), 0, 1);
        $this->SetFont('Arial', '', 8);

        for ($i = 1; $i <= 4; $i++) {
            $field = "objetivos{$i}";
            if ($this->plan->$field) {
                $this->Cell(0, 5, "{$i}. " . $this->plan->$field, 'B', 1);
            }
        }

        $this->Ln(2);
    }

    private function renderSkills(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 6, __("DESTREZAS"), 0, 1);
        $this->SetFont('Arial', '', 8);

        // Row 1
        if ($this->plan->destrezas1) {
            $this->Cell(90, 5, "1. " . $this->plan->destrezas1, 'B', 0);
        }
        $this->Cell(5);
        if ($this->plan->destrezas3) {
            $this->Cell(90, 5, "3. " . $this->plan->destrezas3, 'B', 1);
        } else {
            $this->Ln();
        }

        // Row 2
        if ($this->plan->destrezas2) {
            $this->Cell(90, 5, "2. " . $this->plan->destrezas2, 'B', 0);
        }
        $this->Cell(5);
        if ($this->plan->destrezas4) {
            $this->Cell(90, 5, "4. " . $this->plan->destrezas4, 'B', 1);
        } else {
            $this->Ln();
        }

        $this->Ln(3);
    }

    private function checkBox(string $value)
    {
        $this->drawCheckbox($value === 'Si', y: 1.7);
    }

    private function renderCommonStandards(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(45, 6, __("ESTÁNDARES COMUNES"), 'LTR', 0);
        $this->Rect($this->GetX(), $this->GetY(), 60, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->estand_comun1);
        $this->Cell(57, 6, 'Vida laboral y universitaria');
        $this->Rect($this->GetX(), $this->GetY(), 60, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->estand_comun2);
        $this->Cell(57, 6, 'Rigurosidad académica', 0, 1);

        $this->Cell(45, 6, 'Common Core Standards', 'LBR', 0);
        $this->Rect($this->GetX(), $this->GetY(), 60, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->estand_comun3);
        $this->Cell(57, 6, 'Integración internacional',);
        $this->Rect($this->GetX(), $this->GetY(), 60, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->estand_comun4);
        $this->Cell(57, 6, 'Investigación basada en evidencia', 0, 1);

        $this->Ln(3);
    }

    private function renderSupports(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 12, __("APOYO DIDÁCTICO"), 1, 0, "C");
        $this->Cell(50, 12, __("INTEGRACIÓN"), 1, 0, "C");
        $this->Cell(50, 6, __("ESTRATEGIAS DE"), 'LTR', 1, "C");
        $this->Cell(140);
        $this->Cell(50, 6, __("ENSEÑANZA - APRENDIZAJE"), 'LBR', 1, "C");

        $this->SetFont('Arial', '', 8);

        $data = $this->getSupportsData();
        $supportY = $this->GetY();
        $apoyoCount = \count(array_chunk($data['apoyo'], ceil(count($data['apoyo']) / 2))[0]);
        $maxRows = max($apoyoCount, \count($data['integracion']), \count($data['estrategias']));
        // $this->SetY($supportY);
        $count = 1;
        foreach ($data['apoyo'] as $key => $label) {
            $this->Rect($this->GetX(), $this->GetY(), 45, 6, 'D');
            $this->Cell(3);
            $this->checkBox($this->plan->{$key});

            $this->Cell(42, 6, $label, 0, $count === 2 ? 1 : 0);

            $count = ($count === 2) ? 1 : $count + 1;
        }
        for ($i = $apoyoCount - 1; $i <= $maxRows; $i++) {
            $this->Rect($this->GetX(), $this->GetY(), 45, 6, 'D');
            $this->Cell(3);
            $this->Cell(42, 6, '', 0, $i % 2 === 1 ? 1 : 0);
        }

        $this->SetY($supportY);
        foreach ($data['integracion'] as $key => $label) {
            $this->SetX(100);
            $this->Rect($this->GetX(), $this->GetY(), 50, 6, 'D');
            $this->Cell(3);
            $this->checkBox($this->plan->{$key});

            $this->Cell(47, 6, $label, 0, 1);
        }

        for ($i = count($data['integracion']); $i < $maxRows; $i++) {
            $this->SetX(100);
            $this->Rect($this->GetX(), $this->GetY(), 50, 6, 'D');
            $this->Cell(3);
            $this->Cell(47, 6, '', 0, 1);
        }

        $this->SetY($supportY);
        foreach ($data['estrategias'] as $key => $label) {
            $this->SetX(150);
            $this->Rect($this->GetX(), $this->GetY(), 50, 6, 'D');
            $this->Cell(3);
            $this->checkBox($this->plan->{$key});

            $this->Cell(47, 6, $label, 0, 1);
        }
        for ($i = count($data['estrategias']); $i < $maxRows; $i++) {
            $this->SetX(150);
            $this->Rect($this->GetX(), $this->GetY(), 50, 6, 'D');
            $this->Cell(3);
            $this->Cell(47, 6, '', 0, 1);
        }

        $this->Ln(3);
    }

    private function renderAssessment(): void
    {
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(190, 6, __("AVALÚO Y EVALUACIÓN REALIZADO POR LOS ESTUDIANTES"), 1, 1, 'C');
        $this->Cell(47.5, 6, 'Portafolio', 1, 0, 'C');
        $this->Cell(47.5, 6, 'Prueba Corta', 1, 0, 'C');
        $this->Cell(47.5, 6, 'Proyecto Especial', 1, 0, 'C');
        $this->Cell(47.5, 6, 'Contestar Preguntas', 1, 1, 'C');

        $this->SetFont('Arial', '', 8);

        // Row 1
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->portafolio1);
        $this->Cell(44.5, 6, 'Examen Escrito');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->prueba1);
        $this->Cell(44.5, 6, 'Informe Escrito');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->proyecto1);
        $this->Cell(44.5, 6, 'Mapa Conceptual');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->contestar1);
        $this->Cell(44.5, 6, 'Trabajo Cooperativo', 0, 1);

        // Row 2
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->portafolio2);
        $this->Cell(44.5, 6, 'Examen Oral');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->prueba2);
        $this->Cell(44.5, 6, 'Informe Oral');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->proyecto2);
        $this->Cell(44.5, 6, 'Diario Reflexivo');
        $this->Rect($this->GetX(), $this->GetY(), 47.5, 6, 'D');
        $this->Cell(3);
        $this->checkBox($this->plan->contestar2);
        $this->Cell(44.5, 6, 'Discusión Socializada', 0, 1);

        $this->Ln(3);
    }

    private function renderValues(): void
    {
        if ($this->GetY() > 200) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(95, 6, __("VALORES"), 0, 0, 'C');
        $this->Cell(95, 5, __("ACOMODOS RAZONABLES"), 0, 1, 'C');
        $this->Cell(95);
        $this->SetFont('Arial', '', 7);
        $this->Cell(95, 5, 'Favor de referirse a tabla de acomodos razonables e indicar solo el número.', 0, 1, 'C');


        $this->SetFont('Arial', '', 8);

        $valoresData = $this->getValoresData();

        foreach ($valoresData as $row) {
            $firstKey = array_key_first($row);
            $this->checkBox($this->plan->{$firstKey});
            $this->Cell(37.5, 6, $row[$firstKey]);

            if (\count($row) > 1) {
                $secondKey = array_key_last($row);
                $this->checkBox($this->plan->{$secondKey});
                $this->Cell(37.5, 6, $row[$secondKey], 0, 1);
            } else {
                $this->Ln();
            }
        }
    }

    private function renderAccommodations(): void
    {
        $X = 95;
        $Y = $this->GetY() - (11 * 6); // Back to where valores started

        $this->SetY($Y);
        $this->SetX($X);

        $acomodosData = [
            'acomodo1' => 'Atención',
            'acomodo2' => 'Conducta',
            'acomodo3' => 'Presentación',
            'acomodo4' => 'Evaluación',
            'acomodo5' => 'Ambiente y Lugar',
            'acomodo6' => 'Tiempo e Itinerario',
        ];

        $keys = array_keys($acomodosData);
        $chunks = array_chunk($keys, 2);

        foreach ($chunks as $chunk) {
            $this->SetX($X);
            $this->checkBox($this->plan->{$chunk[0]});
            $this->Cell(37.5, 6, $acomodosData[$chunk[0]]);
            if (isset($chunk[1])) {
                $this->checkBox($this->plan->{$chunk[1]});
                $this->Cell(37.5, 6, $acomodosData[$chunk[1]], 0, 1);
            } else {
                $this->Ln();
            }

            $this->SetX($X);
            $this->Cell(45, 6, $this->plan->{$chunk[0] . '_1'}, 'B');
            $this->Cell(5);
            if (isset($chunk[1])) {
                $this->Cell(45, 6, $this->plan->{$chunk[1] . '_1'}, 'B', 1);
            } else {
                $this->Ln();
            }

            $this->SetX($X);
            $this->Cell(45, 6, $this->plan->{$chunk[0] . '_2'}, 'B');
            $this->Cell(5);
            if (isset($chunk[1])) {
                $this->Cell(45, 6, $this->plan->{$chunk[1] . '_2'}, 'B', 1);
            } else {
                $this->Ln();
            }

            $this->Ln(2);
        }

        $this->Ln(10);
    }

    private function renderWeeklyWork(): void
    {
        $weekDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, __("TRABAJO SEMANAL"), 0, 1);

        $this->SetFont('Arial', '', 9);

        for ($i = 1; $i <= 10; $i++) {
            $diaIndex = ($i - 1) % 5;

            if ($this->GetY() > 250) {
                $this->AddPage();
            }

            $this->SetFont('Arial', 'B', 9);
            $this->Cell(63.3, 6, $weekDays[$diaIndex], 1, 0);
            $this->Cell(63.3, 6, "Fase:", 1, 0);
            $this->Cell(63.3, 6, "Acomodo:", 1, 1);

            $this->SetFont('Arial', '', 8);
            $this->Cell(63.3, 6, "", 1, 0);
            $this->Cell(63.3, 6, $this->plan->{"semanal{$i}_1"}, 'TRB', 0);
            $this->Cell(63.3, 6, $this->plan->{"semanal{$i}_2"}, 1, 1);

            $this->Cell(190, 6, "Tarea: " . $this->plan->{"semanal{$i}_3"}, 1, 1);
        }

        $this->Ln(3);
    }

    private function renderReview(): void
    {
        $this->SetFont('Arial', '', 9);
        $this->Cell(63.3, 6, "Revisado por: " . $this->plan->revisado1, 1, 0);
        $this->Cell(63.3, 6, "Fecha: " . $this->plan->revisado2, 1, 0);
        $this->Cell(17, 6, "Aprobado Si", 'LTB', 0);

        $this->Rect($this->GetX() + 3, $this->GetY() + 1.5, 3, 3, ($this->plan->revisado3 == 'Si') ? 'DF' : '');
        $this->Cell(10, 6, '', 'TB', 0);
        $this->Cell(5, 6, 'No', 'TB', 0);
        $this->Rect($this->GetX() + 3, $this->GetY() + 1.5, 3, 3, ($this->plan->revisado4 == 'Si') ? 'DF' : '');
        $this->Cell(10, 6, '', 'TB', 0);
        $this->Cell(21.3, 6, '', 'TBR', 1);

        $this->Cell(190, 6, "Comentario: " . $this->plan->revisado5, 1);
    }

    private function getSupportsData(): array
    {
        return [
            'apoyo' => [
                'apoyo1' => 'Calculadora',
                'apoyo2' => 'DVD/VCR',
                'apoyo3' => 'Dibujo',
                'apoyo4' => 'Radio CD/Ipod',
                'apoyo5' => 'Diccionario',
                'apoyo6' => 'Tecnología',
                'apoyo7' => 'Filminas',
                'apoyo8' => 'Computadora',
                'apoyo9' => 'Grabadoras',
                'apoyo10' => 'Teatro',
                'apoyo11' => 'Láminas',
                'apoyo12' => 'Biblioteca',
                'apoyo13' => 'Películas',
                'apoyo14' => 'Música',
                'apoyo15' => 'Biblia',
                'apoyo16' => 'Hoja Fotocopia',
                'apoyo17' => 'Pizarra - Electrónica',
                'apoyo18' => 'Mapas',
                'apoyo19' => 'Franjas',
                'apoyo20' => 'Power Point',
                'apoyo21' => 'Juegos',
                'apoyo22' => 'Excel',
                'apoyo23' => 'Texto',
                'apoyo24' => 'Word',
                'apoyo25' => 'Carteles',
                'apoyo26' => 'Publisher',
                'apoyo27' => 'Proyector',
            ],
            'integracion' => [
                'integracion1' => 'Artes',
                'integracion2' => 'Música',
                'integracion3' => 'Religión',
                'integracion4' => 'Español',
                'integracion5' => 'Ciencias',
                'integracion6' => 'Estudio Sociales',
                'integracion7' => 'Inglés',
                'integracion8' => 'Matemáticas',
                'integracion9' => 'Educación Física',
                'integracion10' => 'Teatro',
                'integracion11' => 'Salud',
                'integracion12' => 'Computadoras',
            ],
            'estrategias' => [
                'estrategias1' => 'Grupo Cooperativo',
                'estrategias2' => 'Informe Oral',
                'estrategias3' => 'Informe Escrito',
                'estrategias4' => 'Demostración',
                'estrategias5' => 'Conferencia',
                'estrategias6' => 'Proyecto de investigación',
                'estrategias7' => 'Mapa de Conceptos',
                'estrategias8' => 'Experiencia de Campo',
                'estrategias9' => 'Entrevista',
                'estrategias10' => 'Debate',
                'estrategias11' => 'Repaso',
                'estrategias12' => 'Canción',
                'estrategias13' => 'Laboratorio',
                'estrategias14' => 'Tirillas Cómicas',
                'estrategias15' => 'Observaciones',
            ],
        ];
    }

    private function getValoresData(): array
    {
        return [
            ['valores1' => 'Amor', 'valores12' => 'Solidaridad'],
            ['valores2' => 'Paz', 'valores13' => 'Entrega'],
            ['valores3' => 'Perdón', 'valores14' => 'Tolerancia'],
            ['valores4' => 'Respeto', 'valores15' => 'Justicia'],
            ['valores5' => 'Trabajo', 'valores16' => 'Generosidad'],
            ['valores6' => 'Fe', 'valores17' => 'Servicio'],
            ['valores7' => 'Armonía', 'valores18' => 'Esperanza'],
            ['valores8' => 'Honestidad', 'valores19' => 'Comunicación'],
            ['valores9' => 'Alegría', 'valores20' => 'Responsabilidad'],
            ['valores10' => 'Dignidad', 'valores21' => 'Caridad'],
            ['valores11' => 'Libertad', 'valores22' => 'Esfuerzo'],
        ];
    }
}
