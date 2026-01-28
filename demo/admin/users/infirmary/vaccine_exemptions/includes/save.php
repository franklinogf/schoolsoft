<?php

require_once __DIR__ . '/../../../../../app.php';

use App\Models\Admin;
use App\Models\School;
use App\Models\VaccineExemption;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __('Método no permitido'));
    Route::redirect('/users/infirmary/vaccine_exemptions/index.php');
    exit;
}

$ss = $_POST['ss'] ?? '';

if (empty($ss)) {
    Session::set('error', __('No se ha especificado un estudiante'));
    Route::redirect('/users/infirmary/vaccine_exemptions/index.php');
    exit;
}

// Get current school year
$school = Admin::primaryAdmin();
$currentYear = $school->year ?? '';

if (empty($currentYear)) {
    Session::set('error', __('No se pudo determinar el año escolar'));
    Route::redirect('/users/infirmary/vaccine_exemptions/index.php');
    exit;
}

try {
    $vaccines = ['Vacunas P-VAC-3', 'Vacunas COVID-19'];

    foreach ($vaccines as $vaccine) {
        $excencion = $_POST['excencion_' . md5($vaccine)] ?? '';
        $fechaEntrega = $_POST['fecha_entrega_' . md5($vaccine)] ?? '';
        $fechaExpiracion = $_POST['fecha_expiracion_' . md5($vaccine)] ?? '';

        // Only save if at least one field has data
        if (!empty($excencion) || !empty($fechaEntrega) || !empty($fechaExpiracion)) {
            VaccineExemption::updateOrCreateExemption($ss, $vaccine, $currentYear, [
                'ss' => $ss,
                'vacuna' => $vaccine,
                'year' => $currentYear,
                'excencion' => $excencion,
                'fechaEntrega' => $fechaEntrega ?: null,
                'fechaExpiracion' => $fechaExpiracion ?: null,
            ]);
        }
    }

    Session::set('success', __('Registro guardado exitosamente'));
    Route::redirect('/users/infirmary/vaccine_exemptions/index.php?ss=' . urlencode($ss));
} catch (\Exception $e) {
    Session::set('error', __('Error al guardar el registro') . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/vaccine_exemptions/index.php?ss=' . urlencode($ss));
}
