<?php
require_once '../../../../app.php';

use App\Models\Admin;
use App\Models\ClassPlan;
use App\Models\Teacher;
use Classes\Util;

$teacherId = $_SESSION['id1'];

// Determine action
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'createPlan':
            createClassPlan($teacherId);
            break;

        case 'updatePlan':
            updateClassPlan($teacherId);
            break;

        case 'deletePlan':
            deleteClassPlan($teacherId);
            break;

        case 'getPlan':
            getClassPlan($teacherId);
            break;

        default:
            Util::toJson([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
} catch (Exception $e) {
    echo Util::toJson([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Create a new class plan
 */
function createClassPlan($teacherId): void
{
    // Get teacher's full name for legacy field
    $teacher = Teacher::find($teacherId);

    if (!$teacher) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Maestro no encontrado'
        ]);
        return;
    }

    $admin = Admin::primaryAdmin();

    $maestro = trim($teacher->nombre . ' ' . $teacher->apellidos);

    // Prepare data
    $data = [
        'id_profesor' => $teacherId,
        'profesor' => $maestro,
        'materia' => $_POST['materia'] ?? '',
        'tema' => $_POST['tema'] ?? '',
        'fecha' => $_POST['fecha'] ?? '',
        'duracion' => $_POST['duracion'] ?? 1,
        'estrategia' => $_POST['estrategia'] ?? '',
        'antes' => $_POST['antes'] ?? '',
        'durante' => $_POST['durante'] ?? '',
        'despues' => $_POST['despues'] ?? '',
        'fechaG' => $_POST['fechaG'] ?? '',
        'duracionG' => $_POST['duracionG'] ?? 1,
        'valorG' => $_POST['valorG'] ?? 100,
        't5' => $_POST['t5'] ?? '',
        't14' => $_POST['t14'] ?? '',
        'year' => $admin->year2,
    ];

    mergeData($data);

    $plan = ClassPlan::create($data);

    echo Util::toJson([
        'success' => true,
        'message' => 'Plan de clase creado exitosamente',
        'planId' => $plan->id
    ]);
}

/**
 * Update existing class plan
 */
function updateClassPlan($teacherId): void
{
    $planId = $_POST['planId'] ?? null;

    if (!$planId) {
        echo Util::toJson([
            'success' => false,
            'message' => 'ID de plan no proporcionado'
        ]);
        return;
    }

    $plan = ClassPlan::byTeacher($teacherId)->find($planId);
    if (!$plan) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Plan no encontrado'
        ]);
        return;
    }

    // Prepare update data (same structure as create)
    $data = [
        'materia' => $_POST['materia'] ?? '',
        'tema' => $_POST['tema'] ?? '',
        'fecha' => $_POST['fecha'] ?? '',
        'duracion' => $_POST['duracion'] ?? 1,
        'estrategia' => $_POST['estrategia'] ?? '',
        'antes' => $_POST['antes'] ?? '',
        'durante' => $_POST['durante'] ?? '',
        'despues' => $_POST['despues'] ?? '',
        'fechaG' => $_POST['fechaG'] ?? '',
        'duracionG' => $_POST['duracionG'] ?? 1,
        'valorG' => $_POST['valorG'] ?? 100,
        't5' => $_POST['t5'] ?? '',
        't14' => $_POST['t14'] ?? '',
    ];


    // Add all checkbox and dynamic fields (same as create)
    mergeData($data);
    // var_dump($data);
    // exit;

    $plan->update($data);


    echo Util::toJson([
        'success' => true,
        'message' => 'Plan de clase actualizado exitosamente'
    ]);
}

/**
 * Delete a class plan
 */
function deleteClassPlan($teacherId)
{
    $planId = $_POST['planId'] ?? null;

    if (!$planId) {
        Util::toJson([
            'success' => false,
            'message' => 'ID de plan no proporcionado'
        ]);
        return;
    }

    $plan = ClassPlan::byTeacher($teacherId)->find($planId);

    if (!$plan) {
        Util::toJson([
            'success' => false,
            'message' => 'Plan no encontrado'
        ]);
        return;
    }

    $plan->delete();

    echo Util::toJson([
        'success' => true,
        'message' => 'Plan de clase eliminado exitosamente'
    ]);
}

/**
 * Get a class plan
 */
function getClassPlan($teacherId)
{
    $planId = $_POST['planId'] ?? null;

    if (!$planId) {
        echo  Util::toJson([
            'success' => false,
            'message' => 'ID de plan no proporcionado'
        ]);
        return;
    }

    $plan = ClassPlan::byTeacher($teacherId)->find($planId);


    if (!$plan) {
        echo Util::toJson([
            'success' => false,
            'message' => 'Plan no encontrado'
        ]);
        return;
    }

    echo Util::toJson([
        'success' => true,
        'data' => $plan->toArray()
    ]);
}

function mergeData(&$data): void
{

    // Tareas
    for ($i = 1; $i <= 14; $i++) {
        $data["tarea{$i}"] = isset($_POST["tarea{$i}"]) ? 'si' : '';
    }

    // Text fields
    foreach (['estandares', 'expectativa', 'objetivos', 'valores'] as $field) {
        for ($i = 1; $i <= 5; $i++) {
            $data["{$field}{$i}"] = $_POST["{$field}{$i}"] ?? '';
        }
    }

    foreach (['antes', 'durante', 'despues'] as $stage) {
        for ($i = 1; $i <= 5; $i++) {
            $data["{$stage}{$i}"] = $_POST["{$stage}{$i}"] ?? '';
        }
    }

    // Checkboxes
    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 4; $j++) {
            $data["pensamiento{$i}_{$j}"] = isset($_POST["pensamiento{$i}_{$j}"]) ? 'si' : '';
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 4; $j++) {
            $data["estrategia_a{$i}_{$j}"] = isset($_POST["estrategia_a{$i}_{$j}"]) ? 'si' : '';
            if ($j === 4) {
                $data["estrategia_a{$i}_{$j}1"] = $_POST["estrategia_a{$i}_{$j}1"] ?? '';
            }
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 13; $j++) {
            $data["estrategia_e{$i}_{$j}"] = isset($_POST["estrategia_e{$i}_{$j}"]) ? 'si' : '';
            if ($j === 13) {
                $data["estrategia_e{$i}_{$j}1"] = $_POST["estrategia_e{$i}_{$j}1"] ?? '';
            }
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 8; $j++) {
            $data["conceptos{$i}_{$j}"] = isset($_POST["conceptos{$i}_{$j}"]) ? 'si' : '';
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 8; $j++) {
            $data["temas{$i}_{$j}"] = isset($_POST["temas{$i}_{$j}"]) ? 'si' : '';
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 7; $j++) {
            $data["materiales{$i}_{$j}"] = isset($_POST["materiales{$i}_{$j}"]) ? 'si' : '';
            if ($j === 3  || $j === 4  || $j === 5 || $j === 7) {
                $data["materiales{$i}_{$j}1"] = $_POST["materiales{$i}_{$j}1"] ?? '';
            }
        }
    }

    for ($i = 1; $i <= 5; $i++) {
        for ($j = 1; $j <= 3; $j++) {
            $data["tareas{$i}_{$j}"] = isset($_POST["tareas{$i}_{$j}"]) ? 'si' : '';
            $data["tareas{$i}_{$j}1"] = $_POST["tareas{$i}_{$j}1"] ?? '';
        }
    }

    for ($i = 1; $i <= 8; $i++) {
        $data["actividad_antes{$i}"] = isset($_POST["actividad_antes{$i}"]) ? 'si' : '';
        if ($i === 8) {
            $data["actividad_antes{$i}1"] = $_POST["actividad_antes{$i}1"] ?? '';
        }
    }

    for ($i = 1; $i <= 9; $i++) {
        $data["actividad_durante{$i}"] = isset($_POST["actividad_durante{$i}"]) ? 'si' : '';
        if ($i === 9) {
            $data["actividad_durante{$i}1"] = $_POST["actividad_durante{$i}1"] ?? '';
        }
    }

    for ($i = 1; $i <= 7; $i++) {
        $data["actividad_despues{$i}"] = isset($_POST["actividad_despues{$i}"]) ? 'si' : '';
        if ($i === 7) {
            $data["actividad_despues{$i}1"] = $_POST["actividad_despues{$i}1"] ?? '';
        }
    }
}
