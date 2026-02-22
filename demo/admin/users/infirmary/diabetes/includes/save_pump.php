<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\DiabetesInsulinPump;
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
        'tbomba' => $_POST['tbomba'] ?? '',
        'tinsulina' => $_POST['tinsulina'] ?? '',
        'infusion' => $_POST['infusion'] ?? '',
        'equipo' => $_POST['equipo'] ?? '',
        'racion' => $_POST['racion'] ?? '',
        'factor' => $_POST['factor'] ?? '',
        'carb' => $_POST['carb'] ?? '',
        'bcarb' => $_POST['bcarb'] ?? '',
        'bcorrec' => $_POST['bcorrec'] ?? '',
        'pbasales' => $_POST['pbasales'] ?? '',
        'btemp' => $_POST['btemp'] ?? '',
        'dbomba' => $_POST['dbomba'] ?? '',
        'einfu' => $_POST['einfu'] ?? '',
        'tubos' => $_POST['tubos'] ?? '',
        'intinf' => $_POST['intinf'] ?? '',
        'alarmas' => $_POST['alarmas'] ?? '',
        'med' => $_POST['med'] ?? '',
        'hmed' => $_POST['hmed'] ?? '',
        'omed' => $_POST['omed'] ?? '',
        'ohmed' => $_POST['ohmed'] ?? '',
    ];

    // Add basal1-11
    for ($i = 1; $i <= 11; $i++) {
        $data['basal' . $i] = $_POST['basal' . $i] ?? '';
    }

    DiabetesInsulinPump::updateOrCreateRecord($id, $ss, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=pump');
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=pump');
}
