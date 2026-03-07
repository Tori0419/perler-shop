<?php

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
