<?php
require_once __DIR__ . '/../../../../../app.php';

use App\Models\Infirmary;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Session::set('error', __("Método no permitido"));
    Route::redirect('/users/infirmary/basic_information/index.php');
}

// Validate required fields
$ss = $_POST['ss'] ?? '';
$id = $_POST['id'] ?? '';

if (empty($ss)) {
    Session::set('error', __("Falta información del estudiante"));
    Route::redirect('/users/infirmary/basic_information/index.php');
}

// Validate numeric fields
if (!empty($_POST['peso']) && !is_numeric($_POST['peso'])) {
    Session::set('error', __("El peso debe ser un valor numérico"));
    Route::redirect('/users/infirmary/basic_information/edit.php?ss=' . urlencode($ss));
}

// Build family history string from checkboxes (Checkbox1 through Checkbox45)
$familyHistory = Infirmary::buildFamilyHistoryString($_POST);

// Prepare data for insert/update
$data = [
    'id' => $id,
    'ss' => $ss,
    'va_dia' => $_POST['vacdia'] ?? '',
    'vac1' => $_POST['dtp'] ?? '',
    'vac2' => $_POST['polio'] ?? '',
    'vac3' => $_POST['mmr'] ?? '',
    'vac4' => $_POST['hib'] ?? '',
    'vac5' => $_POST['ppd'] ?? '',
    'vac6' => $_POST['varicela'] ?? '',
    'vac7' => $_POST['vac7'] ?? '',
    'vac8' => $_POST['vac8'] ?? '',
    'vac9' => $_POST['vac9'] ?? '',
    'vac10' => $_POST['vac10'] ?? '',
    'vac11' => $_POST['vac11'] ?? '',
    'vac12' => $_POST['vac12'] ?? '',
    'vac13' => $_POST['vac13'] ?? '',
    'vac14' => $_POST['vac14'] ?? '',
    'vac15' => $_POST['vac15'] ?? '',
    'vac16' => $_POST['vac16'] ?? '',
    'vac17' => $_POST['vac17'] ?? '',
    'vac18' => $_POST['vac18'] ?? '',
    'refuerzos' => $_POST['refuerzos'] ?? '',
    'peso' => $_POST['peso'] ?? '',
    'estatura' => $_POST['estatura'] ?? '',
    'cond_salud' => $_POST['condicion'] ?? '',
    'med_usodi' => $_POST['medicamento'] ?? '',
    'dosis' => $_POST['dosis'] ?? '',
    'frec' => $_POST['frecuencia'] ?? '',
    'piel1' => $_POST['piel1'] ?? '',
    'piel2' => $_POST['piel2'] ?? '',
    'piel3' => $_POST['piel3'] ?? '',
    'piel4' => $_POST['piel4'] ?? '',
    'piel5' => $_POST['piel5'] ?? '',
    'cicatrices' => $_POST['cicatrices'] ?? '',
    'quemaduras' => $_POST['quemaduras'] ?? '',
    'vision' => $_POST['vision'] ?? '',
    'audicion' => $_POST['audicion'] ?? '',
    'nasal' => $_POST['congestion'] ?? '',
    'espejuelos' => $_POST['espejuelos'] ?? '',
    'dentadura' => $_POST['dentadura'] ?? '',
    'respiracion' => $_POST['respiracion'] ?? '',
    'asma' => $_POST['asma'] ?? '',
    'condritis' => $_POST['condritis'] ?? '',
    'espasmos' => $_POST['espasmo'] ?? '',
    'ab_edema' => $_POST['edema'] ?? '',
    'ab_herida' => $_POST['herida'] ?? '',
    'ab_deformidad' => $_POST['deformidad'] ?? '',
    'desc1' => $_POST['descrip1'] ?? '',
    'ex_edema' => $_POST['exedema'] ?? '',
    'ex_herida' => $_POST['exherida'] ?? '',
    'ex_deformidad' => $_POST['exdeform'] ?? '',
    'ex_protesis' => $_POST['exprotesis'] ?? '',
    'desc2' => $_POST['descrip2'] ?? '',
    'historial' => $familyHistory,
    'com1' => $_POST['com1'] ?? '',
    'com2' => $_POST['com2'] ?? '',
    'com3' => $_POST['com3'] ?? '',
    'com4' => $_POST['com4'] ?? '',
];

try {
    // Use updateOrCreate to handle both insert and update
    Infirmary::updateOrCreate(
        ['ss' => $ss],
        $data
    );

    Session::set('success', __("Registro guardado exitosamente"));
    Route::redirect('/users/infirmary/basic_information/edit.php?ss=' . urlencode($ss));
} catch (\Exception $e) {
    Session::set('error', __("Error al guardar el registro") . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/basic_information/edit.php?ss=' . urlencode($ss));
}
