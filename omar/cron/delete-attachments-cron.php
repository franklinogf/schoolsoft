<?php
$attachmentsDir = "../../cdls/admin/correo/attachments/";
$files = scandir($attachmentsDir);
foreach ($files as $file) {

    if ($file == '.' || $file == '..') {
        continue;
    }
    $fileDir = "{$attachmentsDir}$file";

    if (file_exists($fileDir)) {
        if (unlink($fileDir)) {
            echo "Removed file: $file";
        } else {
            echo "Failed to remove file: $file";
            continue;
        };
    }
}
