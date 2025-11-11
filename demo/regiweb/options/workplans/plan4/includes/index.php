<?php
require_once '../../../../../app.php';

use App\Models\Admin;
use App\Models\Teacher;
use App\Models\WorkPlan4;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();

if (!Server::is_post(false)) {
    http_response_code(405);
    die();
}

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();

// Get work plan
if (isset($_POST['getWorkPlan'])) {
    $planId = $_POST['getWorkPlan'];
    $workPlan = WorkPlan4::find($planId);

    if ($workPlan && $workPlan->id_profesor == $teacher->id) {
        echo Util::toJson($workPlan);
    } else {
        http_response_code(404);
        echo Util::toJson(['error' => __('Plan no encontrado')]);
    }
    exit;
}

// Create work plan
if (isset($_POST['createWorkPlan'])) {
    $data = [
        'id_profesor' => $teacher->id,
        'year' => $school->year2,
        'unidad' => $_POST['unidad'] ?? '',
        'temas' => $_POST['temas'] ?? '',
        'fecha1' => $_POST['fecha1'] ?? null,
        'fecha2' => $_POST['fecha2'] ?? null,
        'fecha3' => $_POST['fecha3'] ?? null,
        'fecha4' => $_POST['fecha4'] ?? null,
        'fecha5' => $_POST['fecha5'] ?? null,
        'conceptual' => $_POST['conceptual'] ?? '',
        'procedimental' => $_POST['procedimental'] ?? '',
        'actitudinal' => $_POST['actitudinal'] ?? '',
        'aprendizaje' => $_POST['aprendizaje'] ?? '',
        'aprendizaje_problema' => $_POST['aprendizaje_problema'] ?? '',
    ];

    // Fase (3 campos)
    for ($i = 1; $i <= 3; $i++) {
        $data["fase{$i}"] = isset($_POST["fase{$i}"]) ? 'Si' : '';
    }

    // Niveles (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["niveles{$i}"] = isset($_POST["niveles{$i}"]) ? 'Si' : '';
    }

    // Estándares (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["estandares{$i}"] = isset($_POST["estandares{$i}"]) ? 'Si' : '';
    }

    // Expectativas (5 campos)
    for ($i = 1; $i <= 5; $i++) {
        $data["expectativas{$i}"] = $_POST["expectativas{$i}"] ?? '';
    }

    // Avalúo (20 campos, algunos con texto)
    for ($i = 1; $i <= 20; $i++) {
        $data["avaluo{$i}"] = isset($_POST["avaluo{$i}"]) ? 'Si' : '';
        if (isset($_POST["avaluo{$i}1"])) {
            $data["avaluo{$i}1"] = $_POST["avaluo{$i}1"];
        }
    }

    // Comprensión (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["comprension{$i}"] = isset($_POST["comprension{$i}"]) ? 'Si' : '';
    }

    // Integración (8 campos, algunos con texto)
    for ($i = 1; $i <= 8; $i++) {
        $data["integracion{$i}"] = isset($_POST["integracion{$i}"]) ? 'Si' : '';
        if (isset($_POST["integracion{$i}1"])) {
            $data["integracion{$i}1"] = $_POST["integracion{$i}1"];
        }
    }

    // Inicio (12 campos, algunos con texto)
    for ($i = 1; $i <= 12; $i++) {
        $data["inicio{$i}"] = isset($_POST["inicio{$i}"]) ? 'Si' : '';
        if (isset($_POST["inicio{$i}1"])) {
            $data["inicio{$i}1"] = $_POST["inicio{$i}1"];
        }
    }

    // Desarrollo (12 campos, algunos con texto)
    for ($i = 1; $i <= 12; $i++) {
        $data["desarrollo{$i}"] = isset($_POST["desarrollo{$i}"]) ? 'Si' : '';
        if (isset($_POST["desarrollo{$i}1"])) {
            $data["desarrollo{$i}1"] = $_POST["desarrollo{$i}1"];
        }
    }

    // Cierre (8 campos, algunos con texto)
    for ($i = 1; $i <= 8; $i++) {
        $data["cierre{$i}"] = isset($_POST["cierre{$i}"]) ? 'Si' : '';
        if (isset($_POST["cierre{$i}1"])) {
            $data["cierre{$i}1"] = $_POST["cierre{$i}1"];
        }
    }

    // Acomodo (6 campos, algunos con texto)
    for ($i = 1; $i <= 6; $i++) {
        $data["acomodo{$i}"] = isset($_POST["acomodo{$i}"]) ? 'Si' : '';
        if (isset($_POST["acomodo{$i}1"])) {
            $data["acomodo{$i}1"] = $_POST["acomodo{$i}1"];
        }
    }

    // Materiales (17 campos, algunos con texto)
    for ($i = 1; $i <= 17; $i++) {
        $data["materiales{$i}"] = isset($_POST["materiales{$i}"]) ? 'Si' : '';
        if (isset($_POST["materiales{$i}1"])) {
            $data["materiales{$i}1"] = $_POST["materiales{$i}1"];
        }
    }

    $workPlan = WorkPlan4::create($data);

    echo Util::toJson([
        'success' => true,
        'message' => __('Plan creado exitosamente'),
        'id' => $workPlan->id
    ]);
    exit;
}

// Update work plan
if (isset($_POST['updateWorkPlan'])) {
    $planId = $_POST['workPlanId'];
    $workPlan = WorkPlan4::find($planId);

    if (!$workPlan || $workPlan->id_profesor != $teacher->id) {
        http_response_code(404);
        echo Util::toJson(['error' => __('Plan no encontrado')]);
        exit;
    }

    $data = [
        'unidad' => $_POST['unidad'] ?? '',
        'temas' => $_POST['temas'] ?? '',
        'fecha1' => $_POST['fecha1'] ?? null,
        'fecha2' => $_POST['fecha2'] ?? null,
        'fecha3' => $_POST['fecha3'] ?? null,
        'fecha4' => $_POST['fecha4'] ?? null,
        'fecha5' => $_POST['fecha5'] ?? null,
        'conceptual' => $_POST['conceptual'] ?? '',
        'procedimental' => $_POST['procedimental'] ?? '',
        'actitudinal' => $_POST['actitudinal'] ?? '',
        'aprendizaje' => $_POST['aprendizaje'] ?? '',
        'aprendizaje_problema' => $_POST['aprendizaje_problema'] ?? '',
    ];

    // Fase (3 campos)
    for ($i = 1; $i <= 3; $i++) {
        $data["fase{$i}"] = isset($_POST["fase{$i}"]) ? 'Si' : '';
    }

    // Niveles (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["niveles{$i}"] = isset($_POST["niveles{$i}"]) ? 'Si' : '';
    }

    // Estándares (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["estandares{$i}"] = isset($_POST["estandares{$i}"]) ? 'Si' : '';
    }

    // Expectativas (5 campos)
    for ($i = 1; $i <= 5; $i++) {
        $data["expectativas{$i}"] = $_POST["expectativas{$i}"] ?? '';
    }

    // Avalúo (20 campos, algunos con texto)
    for ($i = 1; $i <= 20; $i++) {
        $data["avaluo{$i}"] = isset($_POST["avaluo{$i}"]) ? 'Si' : '';
        if (isset($_POST["avaluo{$i}1"])) {
            $data["avaluo{$i}1"] = $_POST["avaluo{$i}1"];
        }
    }

    // Comprensión (4 campos)
    for ($i = 1; $i <= 4; $i++) {
        $data["comprension{$i}"] = isset($_POST["comprension{$i}"]) ? 'Si' : '';
    }

    // Integración (8 campos, algunos con texto)
    for ($i = 1; $i <= 8; $i++) {
        $data["integracion{$i}"] = isset($_POST["integracion{$i}"]) ? 'Si' : '';
        if (isset($_POST["integracion{$i}1"])) {
            $data["integracion{$i}1"] = $_POST["integracion{$i}1"];
        }
    }

    // Inicio (12 campos, algunos con texto)
    for ($i = 1; $i <= 12; $i++) {
        $data["inicio{$i}"] = isset($_POST["inicio{$i}"]) ? 'Si' : '';
        if (isset($_POST["inicio{$i}1"])) {
            $data["inicio{$i}1"] = $_POST["inicio{$i}1"];
        }
    }

    // Desarrollo (12 campos, algunos con texto)
    for ($i = 1; $i <= 12; $i++) {
        $data["desarrollo{$i}"] = isset($_POST["desarrollo{$i}"]) ? 'Si' : '';
        if (isset($_POST["desarrollo{$i}1"])) {
            $data["desarrollo{$i}1"] = $_POST["desarrollo{$i}1"];
        }
    }

    // Cierre (8 campos, algunos con texto)
    for ($i = 1; $i <= 8; $i++) {
        $data["cierre{$i}"] = isset($_POST["cierre{$i}"]) ? 'Si' : '';
        if (isset($_POST["cierre{$i}1"])) {
            $data["cierre{$i}1"] = $_POST["cierre{$i}1"];
        }
    }

    // Acomodo (6 campos, algunos con texto)
    for ($i = 1; $i <= 6; $i++) {
        $data["acomodo{$i}"] = isset($_POST["acomodo{$i}"]) ? 'Si' : '';
        if (isset($_POST["acomodo{$i}1"])) {
            $data["acomodo{$i}1"] = $_POST["acomodo{$i}1"];
        }
    }

    // Materiales (17 campos, algunos con texto)
    for ($i = 1; $i <= 17; $i++) {
        $data["materiales{$i}"] = isset($_POST["materiales{$i}"]) ? 'Si' : '';
        if (isset($_POST["materiales{$i}1"])) {
            $data["materiales{$i}1"] = $_POST["materiales{$i}1"];
        }
    }

    $workPlan->update($data);

    echo Util::toJson([
        'success' => true,
        'message' => __('Plan actualizado exitosamente')
    ]);
    exit;
}

// Delete work plan
if (isset($_POST['deleteWorkPlan'])) {
    $planId = $_POST['deleteWorkPlan'];
    $workPlan = WorkPlan4::find($planId);

    if ($workPlan && $workPlan->id_profesor == $teacher->id) {
        $workPlan->delete();
        echo Util::toJson([
            'success' => true,
            'message' => __('Plan eliminado exitosamente')
        ]);
    } else {
        http_response_code(404);
        echo Util::toJson(['error' => __('Plan no encontrado')]);
    }
    exit;
}

http_response_code(400);
echo Util::toJson(['error' => __('Solicitud inválida')]);
