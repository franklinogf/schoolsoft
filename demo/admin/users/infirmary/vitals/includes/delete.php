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

if (empty($ss) || empty($id) || empty($fecha) || empty($hora)) {
    Session::set('error', __('Falta información para eliminar el registro'));
    Route::redirect('/users/infirmary/vitals/index.php');
    exit;
}

try {
    $deleted = Vital::deleteByCompositeKey($id, $ss, $fecha, $hora);

    if ($deleted) {
        Session::set('success', __('Registro eliminado exitosamente'));
    } else {
        Session::set('error', __('No se encontró el registro'));
    }
} catch (\Exception $e) {
    Session::set('error', __('Error al eliminar el registro') . ': ' . $e->getMessage());
}

Route::redirect('/users/infirmary/vitals/index.php?ss=' . $ss);