<?php

use App\Models\Payment;
use Classes\Route;

require_once __DIR__ . '/../../../../app.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $id = $_POST['id'];

    try {
        Payment::find($id)->delete();
    } catch (Exception $e) {
        throw $e;
    }

    echo json_encode(['success' => true]);
} else {
    Route::error();
}
