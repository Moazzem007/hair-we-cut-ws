<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Strip the base subdirectory from the URI if present
$uri = preg_replace('#^/portal#', '', $uri);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    $path = __DIR__.'/public'.$uri;
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    
    $mimeTypes = [
        'css' => 'text/css',
        'js'  => 'application/javascript',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg'=> 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff'=> 'font/woff',
        'woff2'=>'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject'
    ];

    if (array_key_exists(strtolower($extension), $mimeTypes)) {
        header('Content-Type: ' . $mimeTypes[strtolower($extension)]);
    } else {
        header('Content-Type: ' . mime_content_type($path));
    }
    
    readfile($path);
    exit;
}

require_once __DIR__.'/public/index.php';
