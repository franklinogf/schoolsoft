<?php

use App\Models\Admin;

require_once '../../../app.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $school = Admin::primaryAdmin();
    if (!$school) {
        http_response_code(404);
        echo json_encode(null, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $data = ["pinCode" => $school->adminpin];
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
