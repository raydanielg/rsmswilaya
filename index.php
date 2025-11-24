<?php
// Front controller shim at project root to forward to Laravel's public/index.php
// This allows accessing the app when the web server points to the repo root.

$publicIndex = __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';
if (!file_exists($publicIndex)) {
    http_response_code(500);
    echo 'Public front controller not found.';
    exit;
}

require $publicIndex;
