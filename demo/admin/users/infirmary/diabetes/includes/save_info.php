<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\DiabetesInfo;
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
        'fecha1' => $_POST['fecha1'] ?? null,
        'fecha2' => $_POST['fecha2'] ?? null,
        'fecha3' => $_POST['fecha3'] ?? null,
        'diabetes' => $_POST['diabetes'] ?? '',
        'doctor' => $_POST['doctor'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'calle' => $_POST['calle'] ?? '',
        'pueblo' => $_POST['pueblo'] ?? '',
        'postal' => $_POST['postal'] ?? '',
        'tel_doc' => $_POST['tel_doc'] ?? '',
        'tel_emer' => $_POST['tel_emer'] ?? '',
        'notificacion' => $_POST['notificacion'] ?? '',
    ];

    DiabetesInfo::updateOrCreateRecord($id, $ss, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=info');
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=info');
}
