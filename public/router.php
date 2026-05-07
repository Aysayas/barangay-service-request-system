<?php
/*
| -------------------------------------------------------------------
| Local PHP Server Router
| -------------------------------------------------------------------
| Use this only for local testing with:
| php -S 0.0.0.0:3000 -t public public/router.php
|
| It lets clean URLs like /login go through public/index.php.
*/

$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH) ?: '/';

if (preg_match('#^/index\.php/(.*)$#i', $path, $matches)) {
    $target = '/' . $matches[1];
    $query = parse_url($request_uri, PHP_URL_QUERY);

    header('Location: ' . $target . ($query ? '?' . $query : ''), true, 302);
    exit;
}

$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
