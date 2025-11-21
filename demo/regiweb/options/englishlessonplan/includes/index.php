<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\EnglishLessonPlan;
use App\Models\Teacher;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$teacher = Teacher::find(Session::id());

if (isset($_POST['getLessonPlan'])) {
    $planId = $_POST['getLessonPlan'];
    $plan = EnglishLessonPlan::find($planId);

    if ($plan && $plan->id_profesor == $teacher->id) {
        echo Util::toJson($plan);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan not found']);
    }
} else if (isset($_POST['createLessonPlan'])) {
    $school = Admin::primaryAdmin();
    $year = $school->year;

    $data = [
        'id_profesor' => $teacher->id,
        'year' => $year,
        'profesor' => $_POST['maestro'],
        'materia' => $_POST['materia'] ?? '',
        'titulo' => $_POST['titulo'] ?? '',
        'fecha' => $_POST['fecha'] ?: null,
        'duracion' => $_POST['duracion'] ?: null,
        'resumen' => $_POST['resumen'] ?? null,
        'objetivo_general' => $_POST['objetivo_general'] ?? null,
        'tareas' => $_POST['tareas'] ?? null,
        'otra' => $_POST['otra'] ?? null,
        'expectativa' => $_POST['expectativa'] ?? null,
        'estrategia' => $_POST['estrategia'] ?? null,
        'objetivos' => $_POST['objetivos'] ?? null,
    ];

    // Transversal themes (1-7)
    for ($i = 1; $i <= 7; $i++) {
        $data["transversal{$i}"] = $_POST["transversal{$i}"] ?? '';
    }

    // Integration (1-9)
    for ($i = 1; $i <= 9; $i++) {
        $data["integracion{$i}"] = $_POST["integracion{$i}"] ?? '';
    }

    // Essential Questions and Understandings (1-5)
    for ($i = 1; $i <= 5; $i++) {
        $data["pe{$i}"] = $_POST["pe{$i}"] ?? '';
        $data["ed{$i}"] = $_POST["ed{$i}"] ?? '';
    }

    // Weekly dates and activities (1-5)
    for ($i = 1; $i <= 5; $i++) {
        $data["fecha{$i}"] = $_POST["fecha{$i}"] ?: null;
        $data["actividades{$i}"] = $_POST["actividades{$i}"] ?? null;

        // Modifications for each day (1-6)
        for ($a = 1; $a <= 6; $a++) {
            $data["acomodo{$i}_{$a}"] = $_POST["acomodo{$i}_{$a}"] ?? '';
        }
        $data["otro{$i}"] = $_POST["otro{$i}"] ?? '';
    }

    $plan = EnglishLessonPlan::create($data);
    echo Util::toJson(['success' => true, 'id' => $plan->id]);
} else if (isset($_POST['updateLessonPlan'])) {
    $planId = $_POST['planId'];
    $plan = EnglishLessonPlan::find($planId);

    if ($plan && $plan->id_profesor == $teacher->id) {
        $updateData = [
            'materia' => $_POST['materia'] ?? '',
            'titulo' => $_POST['titulo'] ?? '',
            'fecha' => $_POST['fecha'] ?: null,
            'duracion' => $_POST['duracion'] ?: null,
            'resumen' => $_POST['resumen'] ?? null,
            'objetivo_general' => $_POST['objetivo_general'] ?? null,
            'tareas' => $_POST['tareas'] ?? null,
            'otra' => $_POST['otra'] ?? null,
            'expectativa' => $_POST['expectativa'] ?? null,
            'estrategia' => $_POST['estrategia'] ?? null,
            'objetivos' => $_POST['objetivos'] ?? null,
        ];

        // Transversal themes (1-7)
        for ($i = 1; $i <= 7; $i++) {
            $updateData["transversal{$i}"] = $_POST["transversal{$i}"] ?? '';
        }

        // Integration (1-9)
        for ($i = 1; $i <= 9; $i++) {
            $updateData["integracion{$i}"] = $_POST["integracion{$i}"] ?? '';
        }

        // Essential Questions and Understandings (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $updateData["pe{$i}"] = $_POST["pe{$i}"] ?? '';
            $updateData["ed{$i}"] = $_POST["ed{$i}"] ?? '';
        }

        // Weekly dates and activities (1-5)
        for ($i = 1; $i <= 5; $i++) {
            $updateData["fecha{$i}"] = $_POST["fecha{$i}"] ?: null;
            $updateData["actividades{$i}"] = $_POST["actividades{$i}"] ?? null;

            // Modifications for each day (1-6)
            for ($a = 1; $a <= 6; $a++) {
                $updateData["acomodo{$i}_{$a}"] = $_POST["acomodo{$i}_{$a}"] ?? '';
            }
            $updateData["otro{$i}"] = $_POST["otro{$i}"] ?? '';
        }

        $plan->update($updateData);
        echo Util::toJson(['success' => true, 'id' => $plan->id]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan not found']);
    }
} else if (isset($_POST['deleteLessonPlan'])) {
    $planId = $_POST['deleteLessonPlan'];
    $plan = EnglishLessonPlan::find($planId);

    if ($plan && $plan->id_profesor == $teacher->id) {
        $plan->delete();
        echo Util::toJson(['success' => true]);
    } else {
        echo Util::toJson(['success' => false, 'message' => 'Plan not found']);
    }
}
