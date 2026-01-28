<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\Vital;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/vitals/index.php');
    exit;
}

// Get and validate required fields
$ss = $_POST['ss'] ?? '';
$id = $_POST['id'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';

if (empty($ss) || empty($id)) {
    Session::set('error', __('Falta información del estudiante'));
    Route::redirect('/users/infirmary/vitals/index.php');
    exit;
}

if (empty($fecha) || empty($hora)) {
    Session::set('error', __('La fecha y hora son requeridas'));
    Route::redirect('/users/infirmary/vitals/index.php?ss=' . $ss);
    exit;
}

// Check if this is an update (old_fecha and old_hora present) or insert
$oldFecha = $_POST['old_fecha'] ?? '';
$oldHora = $_POST['old_hora'] ?? '';
$isUpdate = !empty($oldFecha) && !empty($oldHora);

// Prepare data
$data = [
    'id' => $id,
    'ss' => $ss,
    'fecha' => $fecha,
    'hora' => $hora,
    'bp' => $_POST['bp'] ?? '',
    'p' => $_POST['p'] ?? '',
    'r' => $_POST['r'] ?? '',
    't' => $_POST['t'] ?? '',
    'dxt' => $_POST['dxt'] ?? '',
];

try {
    if ($isUpdate) {
        // Update existing record
        $success = Vital::updateByCompositeKey($id, $ss, $oldFecha, $oldHora, $data);

        if ($success) {
            Session::set('success', __('Registro actualizado exitosamente'));
        } else {
            Session::set('error', __('No se encontró el registro para actualizar'));
        }
    } else {
        // Check if record already exists
        $existing = Vital::findByCompositeKey($ss, $fecha, $hora);

        if ($existing) {
            Session::set('error', __('Ya existe un registro con esa fecha y hora'));
        } else {
            // Insert new record
            Vital::create($data);
            Session::set('success', __('Registro guardado exitosamente'));
        }
    }
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
}

Route::redirect('/users/infirmary/vitals/index.php?ss=' . $ss);
