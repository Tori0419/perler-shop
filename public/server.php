<?php

// Enable error display for debugging on Render
if (getenv('RENDER') === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if ($uri === '/_health') {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(200);
    echo 'ok';
    return true;
}

if ($uri !== '/' && file_exists(__DIR__.$uri)) {
    return false;
}

require_once __DIR__.'/index.php';
