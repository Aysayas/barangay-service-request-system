<?php
/*
| -------------------------------------------------------------------
| Local PHP Server Router
| -------------------------------------------------------------------
| Use this only for local testing with:
| php -S localhost:3000 -t public public/router.php
|
| It lets clean URLs like /login go through public/index.php.
*/

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
