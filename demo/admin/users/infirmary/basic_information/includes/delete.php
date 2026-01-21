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

// Validate required field
$ss = $_POST['ss'] ?? '';

if (empty($ss)) {
    Session::set('error', __("Falta información del estudiante"));
    Route::redirect('/users/infirmary/basic_information/index.php');
}

try {
    $infirmary = Infirmary::where('ss', $ss)->first();
    
    if ($infirmary) {
        $infirmary->delete();
        Session::set('success', __("Registro eliminado exitosamente"));
    } else {
        Session::set('error', __("No se encontró el registro"));
    }
    
    Route::redirect('/users/infirmary/basic_information/index.php');
} catch (\Exception $e) {
    Session::set('error', __("Error al eliminar el registro") . ': ' . $e->getMessage());
    Route::redirect('/users/infirmary/basic_information/edit.php?ss=' . urlencode($ss));
}
