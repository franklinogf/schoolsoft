<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\InfirmaryVisit;
use Classes\Route;
use Classes\Session;

Session::is_logged();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/visits/index.php');
    exit;
}

// Get and validate required fields
$ss = $_POST['ss'] ?? '';
$id = $_POST['id'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$razon = $_POST['razon'] ?? '';

if (empty($ss) || empty($id)) {
    Session::set('error', __('Falta información del estudiante'));
    Route::redirect('/users/infirmary/visits/index.php');
    exit;
}

if (empty($fecha) || empty($hora) || empty($razon)) {
    Session::set('error', __('La fecha, hora y razón son requeridas'));
    Route::redirect('/users/infirmary/visits/index.php?ss=' . $ss);
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
    'razon' => $razon,
    'tratamiento' => $_POST['tratamiento'] ?? '',
    'notif_padres' => $_POST['notif_padres'] ?? 'No',
    'recomendacion' => $_POST['recomendacion'] ?? '',
    'padre_contacto' => $_POST['padre_contacto'] ?? '',
    'telefono' => $_POST['telefono'] ?? '',
    'observaciones' => $_POST['observaciones'] ?? '',
];

try {
    if ($isUpdate) {
        // Update existing record
        $success = InfirmaryVisit::updateByCompositeKey($id, $ss, $oldFecha, $oldHora, $data);

        if ($success) {
            Session::set('success', __('Registro actualizado exitosamente'));
        } else {
            Session::set('error', __('No se encontró el registro para actualizar'));
        }
    } else {
        // Check if record already exists
        $existing = InfirmaryVisit::findByCompositeKey($ss, $fecha, $hora);

        if ($existing) {
            Session::set('error', __('Ya existe un registro con esa fecha y hora'));
        } else {
            // Insert new record
            InfirmaryVisit::create($data);
            Session::set('success', __('Registro guardado exitosamente'));
        }
    }
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
}

Route::redirect('/users/infirmary/visits/index.php?ss=' . $ss);
