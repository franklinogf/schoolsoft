<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\UnitPlan;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year;

// Get unit plan
if (isset($_POST['getUnitPlan'])) {
    $planId = $_POST['getUnitPlan'];
    $unitPlan = UnitPlan::find($planId);

    if ($unitPlan && $unitPlan->id_profesor == $teacher->id) {
        echo Util::toJson($unitPlan);
    } else {
        http_response_code(404);
        echo Util::toJson(['error' => __('Plan no encontrado')]);
    }
    exit;
}

// Create unit plan
if (isset($_POST['createUnitPlan'])) {
    try {
        // Prepare data
        $data = [
            'id_profesor' => $teacher->id,
            'year' => $year,
            'profesor' => $teacher->nombre . ' ' . $teacher->apellidos,
            'materia' => $_POST['materia'] ?? '',
            'titulo' => $_POST['titulo'] ?? '',
            'fecha' => $_POST['fecha'] ?? null,
            'duracion' => $_POST['duracion'] ?? 0,
            'meta' => $_POST['meta'] ?? '',
            'resumen' => $_POST['resumen'] ?? '',
            'objetivo_general' => $_POST['objetivo_general'] ?? '',
            'objetivo_adquisicion' => $_POST['objetivo_adquisicion'] ?? '',
            'tareas' => $_POST['tareas'] ?? '',
            'otra' => $_POST['otra'] ?? '',
            'actividades' => $_POST['actividades'] ?? '',
            'tareas_observaciones' => $_POST['tareas_observaciones'] ?? '',
            'otra_observaciones' => $_POST['otra_observaciones'] ?? '',
            'actividades_observaciones' => $_POST['actividades_observaciones'] ?? '',
            'expectativa' => $_POST['expectativa'] ?? '',
            'estrategia' => $_POST['estrategia'] ?? '',
            'objetivos' => $_POST['objetivos'] ?? '',
        ];

        // Transversal themes (1-7)
        for ($i = 1; $i <= 7; $i++) {
            $data["transversal{$i}"] = isset($_POST["transversal{$i}"]) ? 'si' : '';
        }

        // Integration subjects (1-9)
        for ($i = 1; $i <= 9; $i++) {
            $data["integracion{$i}"] = isset($_POST["integracion{$i}"]) ? 'si' : '';
        }

        // Standards (1-2)
        for ($i = 1; $i <= 2; $i++) {
            $data["estandar{$i}"] = $_POST["estandar{$i}"] ?? '';
        }

        // Essential questions and lasting understanding (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["pe{$i}"] = $_POST["pe{$i}"] ?? '';
            $data["ed{$i}"] = $_POST["ed{$i}"] ?? '';
        }

        // Dates for each day (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["fecha{$i}"] = !empty($_POST["fecha{$i}"]) ? $_POST["fecha{$i}"] : null;
        }

        // Depth levels (5 days x 4 levels)
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $data["nivel{$i}_{$j}"] = isset($_POST["nivel{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Inicio, Desarrollo, Cierre (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["inicio{$i}"] = $_POST["inicio{$i}"] ?? '';
            $data["desarrollo{$i}"] = $_POST["desarrollo{$i}"] ?? '';
            $data["cierre{$i}"] = $_POST["cierre{$i}"] ?? '';
        }

        // Accommodations (5 days x 6 types)
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 6; $j++) {
                $data["acomodo{$i}_{$j}"] = isset($_POST["acomodo{$i}_{$j}"]) ? 'si' : '';
            }
            $data["otro{$i}"] = $_POST["otro{$i}"] ?? '';
        }

        $unitPlan = UnitPlan::create($data);

        echo Util::toJson([
            'success' => true,
            'message' => __('Plan de unidad creado exitosamente'),
            'unitPlanId' => $unitPlan->id
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo Util::toJson([
            'success' => false,
            'error' => __('Error al crear el plan: ') . $e->getMessage()
        ]);
    }
    exit;
}

// Update unit plan
if (isset($_POST['updateUnitPlan'])) {
    try {
        $planId = $_POST['unitPlanId'];
        $unitPlan = UnitPlan::find($planId);

        if (!$unitPlan || $unitPlan->id_profesor != $teacher->id) {
            http_response_code(404);
            echo Util::toJson(['error' => __('Plan no encontrado')]);
            exit;
        }

        // Prepare data
        $data = [
            'materia' => $_POST['materia'] ?? '',
            'titulo' => $_POST['titulo'] ?? '',
            'fecha' => $_POST['fecha'] ?? null,
            'duracion' => $_POST['duracion'] ?? 0,
            'meta' => $_POST['meta'] ?? '',
            'resumen' => $_POST['resumen'] ?? '',
            'objetivo_general' => $_POST['objetivo_general'] ?? '',
            'objetivo_adquisicion' => $_POST['objetivo_adquisicion'] ?? '',
            'tareas' => $_POST['tareas'] ?? '',
            'otra' => $_POST['otra'] ?? '',
            'actividades' => $_POST['actividades'] ?? '',
            'tareas_observaciones' => $_POST['tareas_observaciones'] ?? '',
            'otra_observaciones' => $_POST['otra_observaciones'] ?? '',
            'actividades_observaciones' => $_POST['actividades_observaciones'] ?? '',
            'expectativa' => $_POST['expectativa'] ?? '',
            'estrategia' => $_POST['estrategia'] ?? '',
            'objetivos' => $_POST['objetivos'] ?? '',
        ];

        // Transversal themes (1-7)
        for ($i = 1; $i <= 7; $i++) {
            $data["transversal{$i}"] = isset($_POST["transversal{$i}"]) ? 'si' : '';
        }

        // Integration subjects (1-9)
        for ($i = 1; $i <= 9; $i++) {
            $data["integracion{$i}"] = isset($_POST["integracion{$i}"]) ? 'si' : '';
        }

        // Standards (1-2)
        for ($i = 1; $i <= 2; $i++) {
            $data["estandar{$i}"] = $_POST["estandar{$i}"] ?? '';
        }

        // Essential questions and lasting understanding (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["pe{$i}"] = $_POST["pe{$i}"] ?? '';
            $data["ed{$i}"] = $_POST["ed{$i}"] ?? '';
        }

        // Dates for each day (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["fecha{$i}"] = !empty($_POST["fecha{$i}"]) ? $_POST["fecha{$i}"] : null;
        }

        // Depth levels (5 days x 4 levels)
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 4; $j++) {
                $data["nivel{$i}_{$j}"] = isset($_POST["nivel{$i}_{$j}"]) ? 'si' : '';
            }
        }

        // Inicio, Desarrollo, Cierre (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $data["inicio{$i}"] = $_POST["inicio{$i}"] ?? '';
            $data["desarrollo{$i}"] = $_POST["desarrollo{$i}"] ?? '';
            $data["cierre{$i}"] = $_POST["cierre{$i}"] ?? '';
        }

        // Accommodations (5 days x 6 types)
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 6; $j++) {
                $data["acomodo{$i}_{$j}"] = isset($_POST["acomodo{$i}_{$j}"]) ? 'si' : '';
            }
            $data["otro{$i}"] = $_POST["otro{$i}"] ?? '';
        }

        $unitPlan->update($data);

        echo Util::toJson([
            'success' => true,
            'message' => __('Plan de unidad actualizado exitosamente')
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo Util::toJson([
            'success' => false,
            'error' => __('Error al actualizar el plan: ') . $e->getMessage()
        ]);
    }
    exit;
}

// Delete unit plan
if (isset($_POST['deleteUnitPlan'])) {
    try {
        $planId = $_POST['deleteUnitPlan'];
        $unitPlan = UnitPlan::find($planId);

        if (!$unitPlan || $unitPlan->id_profesor != $teacher->id) {
            http_response_code(404);
            echo Util::toJson(['error' => __('Plan no encontrado')]);
            exit;
        }

        $unitPlan->delete();

        echo Util::toJson([
            'success' => true,
            'message' => __('Plan de unidad eliminado exitosamente')
        ]);
    } catch (\Exception $e) {
        http_response_code(500);
        echo Util::toJson([
            'success' => false,
            'error' => __('Error al eliminar el plan: ') . $e->getMessage()
        ]);
    }
    exit;
}
