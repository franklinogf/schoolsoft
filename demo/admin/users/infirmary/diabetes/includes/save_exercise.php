<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\DiabetesExercise;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/diabetes/index.php');
    exit;
}

$id = $_POST['id'] ?? '';
$ss = $_POST['ss'] ?? '';

if (empty($id) || empty($ss)) {
    Session::set('error', __('Falta información del estudiante'));
    Route::redirect('/users/infirmary/diabetes/index.php');
    exit;
}

try {
    $data = [
        'id' => $id,
        'ss' => $ss,
        'carb' => $_POST['carb'] ?? '',
        'actividad' => $_POST['actividad'] ?? '',
        'glucosa_min' => $_POST['glucosa_min'] ?? '',
        'glucosa_max' => $_POST['glucosa_max'] ?? '',
        'sintomas_hipo' => $_POST['sintomas_hipo'] ?? '',
        'tratamiento_hipo' => $_POST['tratamiento_hipo'] ?? '',
        'dosis' => $_POST['dosis'] ?? '',
        'sintomas_hiper' => $_POST['sintomas_hiper'] ?? '',
        'tratamiento_hiper' => $_POST['tratamiento_hiper'] ?? '',
        'azucar' => $_POST['azucar'] ?? '',
    ];

    DiabetesExercise::updateOrCreateRecord($id, $ss, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=exercise');
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=exercise');
}
