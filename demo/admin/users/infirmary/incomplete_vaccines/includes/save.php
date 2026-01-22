<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\IncompleteVaccine;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/incomplete_vaccines/index.php');
    exit;
}

$ss = $_POST['ss'] ?? '';
$curso = $_POST['curso'] ?? '';
$year = $_POST['year'] ?? '';

if (empty($ss) || empty($curso) || empty($year)) {
    Session::set('error', __('Falta información del estudiante'));
    Route::redirect('/users/infirmary/incomplete_vaccines/index.php');
    exit;
}

try {
    $data = [
        'ss' => $ss,
        'curso' => $curso,
        'year' => $year,
        'vacuna1' => isset($_POST['vacuna1']) ? 'x' : '',
        'vacuna2' => isset($_POST['vacuna2']) ? 'x' : '',
        'vacuna3' => isset($_POST['vacuna3']) ? 'x' : '',
        'vacuna4' => isset($_POST['vacuna4']) ? 'x' : '',
        'vacuna5' => isset($_POST['vacuna5']) ? 'x' : '',
        'vacuna6' => isset($_POST['vacuna6']) ? 'x' : '',
        'vacuna7' => isset($_POST['vacuna7']) ? 'x' : '',
        'vacuna8' => isset($_POST['vacuna8']) ? 'x' : '',
        'vacuna9' => isset($_POST['vacuna9']) ? 'x' : '',
        'vacuna10' => isset($_POST['vacuna10']) ? 'x' : '',
        'vacuna11' => isset($_POST['vacuna11']) ? 'x' : '',
        'vacuna12' => isset($_POST['vacuna12']) ? 'x' : '',
        'vacuna13' => isset($_POST['vacuna13']) ? 'x' : '',
        'cert1' => $_POST['cert1'] ?? '',
        'cert2' => isset($_POST['cert2']) ? 'x' : '',
        'cert3' => isset($_POST['cert3']) ? 'x' : '',
        'pvac' => isset($_POST['pvac']) ? 'x' : '',
        'comentario' => $_POST['comentario'] ?? '',
    ];

    IncompleteVaccine::updateOrCreateRecord($ss, $curso, $year, $data);

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/incomplete_vaccines/index.php?curso=' . urlencode($curso) . '&ss=' . urlencode($ss));
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/incomplete_vaccines/index.php?curso=' . urlencode($curso) . '&ss=' . urlencode($ss));
}
