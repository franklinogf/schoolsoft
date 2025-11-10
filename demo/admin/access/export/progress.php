<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

$progressId = $_GET['id'] ?? '';

if (empty($progressId)) {
    echo json_encode(['error' => 'No progress ID provided']);
    exit;
}

$progressFile = __DIR__ . '/exports/progress_' . basename($progressId) . '.json';

if (file_exists($progressFile)) {
    clearstatcache(true, $progressFile); // Clear file cache
    $data = json_decode(file_get_contents($progressFile), true);
    echo json_encode($data);

    // Clean up completed progress files
    if (isset($data['complete']) && $data['complete']) {
        // Delete after 5 seconds
        if (isset($data['completed_at']) && (time() - $data['completed_at']) > 5) {
            @unlink($progressFile);
        }
    }
} else {
    echo json_encode([
        'progress' => 0,
        'message' => 'Iniciando...',
        'complete' => false
    ]);
}
