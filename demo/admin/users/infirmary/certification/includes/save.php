<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\InfirmaryCertification;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/certification/index.php');
    exit;
}

$ss = $_POST['ss'] ?? '';
$curso = $_POST['curso'] ?? '';
$year = $_POST['year'] ?? '';

if (empty($ss) || empty($curso) || empty($year)) {
    Session::set('error', __('Falta información del estudiante'));
    Route::redirect('/users/infirmary/certification/index.php');
    exit;
}

try {
    $data = [
        'ss' => $ss,
        'curso' => $curso,
        'year' => $year,
        'cert1' => $_POST['cert1'] ?? '',
        'cert2' => $_POST['cert2'] ?? '',
        'dec1' => $_POST['dec1'] ?? '',
        'dec2' => $_POST['dec2'] ?? '',
        'dec3' => $_POST['dec3'] ?? '',
        'dec4' => $_POST['dec4'] ?? '',
        'dec5' => $_POST['dec5'] ?? '',
        'dec6' => $_POST['dec6'] ?? '',
        'dec7' => $_POST['dec7'] ?? '',
        'dec8' => $_POST['dec8'] ?? '',
        'tes1' => $_POST['tes1'] ?? '',
        'tes2' => $_POST['tes2'] ?? '',
        'tes3' => $_POST['tes3'] ?? '',
        'tes4' => $_POST['tes4'] ?? '',
        'tes5' => !empty($_POST['tes5']) ? (int)$_POST['tes5'] : null,
        'tes6' => $_POST['tes6'] ?? '',
        'tes7' => $_POST['tes7'] ?? '',
    ];

    InfirmaryCertification::updateOrCreateCertification($ss, $curso, $year, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/certification/index.php?curso=' . urlencode($curso) . '&ss=' . urlencode($ss));
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/certification/index.php?curso=' . urlencode($curso) . '&ss=' . urlencode($ss));
}
