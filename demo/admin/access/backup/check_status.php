<?php
header('Content-Type: application/json');

$dir = __DIR__ . '/tmp';
$id = $_GET['id'] ?? null;

if (!$id) {
    // Show last backup
    $files = glob("{$dir}/*.status");
    rsort($files);
    if (empty($files)) {
        echo json_encode(['status' => 'none']);
        exit;
    }


    $latest = $files[0];
    $content = json_decode(file_get_contents($latest), true);
    echo json_encode(['backup_id' => basename($latest, '.status')] + $content);
    exit;
}


$file = "{$dir}/{$id}.status";

if (!file_exists($file)) {
    echo json_encode(['status' => 'none']);
    exit;
}

$content = json_decode(file_get_contents($file), true);
echo json_encode($content + ['backup_id' => $id]);
