<?php

use App\Models\Admin;
use Classes\Route;
use Classes\Session;



include '../../../app.php';

Session::is_logged();

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $admin = Admin::primaryAdmin()->first();
    $pdf = $_POST['pdf'] ?? null;
    $red = intval(substr($pdf, 1, 2), 16);
    $green = intval(substr($pdf, 3, 2), 16);
    $blue = intval(substr($pdf, 5, 2), 16);

    $admin->update([
        'pdf' => json_encode([
            'red' => $red,
            'green' => $green,
            'blue' => $blue,
        ]),
    ]);
    Session::set('pdf', __('Colore del pdf actualizado correctamente'));

    Route::redirect('/information');
} else {
    Route::error();
}
