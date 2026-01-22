<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\DiabetesInsulin;
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
        'rango' => $_POST['rango'] ?? '',
        'horas' => $_POST['horas'] ?? '',
        'ejer1' => isset($_POST['ejer1']) ? '1' : '',
        'ejer2' => isset($_POST['ejer2']) ? '1' : '',
        'hiper' => isset($_POST['hiper']) ? '1' : '',
        'hipo' => isset($_POST['hipo']) ? '1' : '',
        'otro' => isset($_POST['otro']) ? '1' : '',
        'otro2' => $_POST['otro2'] ?? '',
        'gluc1' => isset($_POST['gluc1']) ? '1' : '',
        'gluc2' => isset($_POST['gluc2']) ? '1' : '',
        'gluc3' => isset($_POST['gluc3']) ? '1' : '',
        'exc1' => isset($_POST['exc1']) ? '1' : '',
        'exc2' => $_POST['exc2'] ?? '',
        'gluc_med' => $_POST['gluc_med'] ?? '',
        'ins1' => isset($_POST['ins1']) ? '1' : '',
        'ins1_n' => $_POST['ins1_n'] ?? '',
        'ins1_u' => $_POST['ins1_u'] ?? '',
        'ins2' => isset($_POST['ins2']) ? '1' : '',
        'ins2_n' => $_POST['ins2_n'] ?? '',
        'ins2_u' => $_POST['ins2_u'] ?? '',
        'ins3' => isset($_POST['ins3']) ? '1' : '',
        'ins3_n' => $_POST['ins3_n'] ?? '',
        'ins3_u' => $_POST['ins3_u'] ?? '',
        'insulina' => $_POST['insulina'] ?? '',
    ];

    // Add insuni1-15 and insu1-9
    for ($i = 1; $i <= 15; $i++) {
        $data['insuni' . $i] = $_POST['insuni' . $i] ?? '';
    }
    for ($i = 1; $i <= 9; $i++) {
        $data['insu' . $i] = isset($_POST['insu' . $i]) ? '1' : '';
    }

    DiabetesInsulin::updateOrCreateRecord($id, $ss, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=insulin');
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/diabetes/index.php?ss=' . urlencode($ss) . '&tab=insulin');
}
