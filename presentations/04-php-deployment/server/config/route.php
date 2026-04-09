<?php

use Webman\Route;

$slidesDir = '/slides';

Route::any('/[{path:.+}]', function ($request, $path = null) use ($slidesDir) {
    $filePath = $slidesDir . '/' . ltrim($path ?? 'index.html', '/');

    if (empty($path)) {
        $filePath = $slidesDir . '/index.html';
    }

    $real = realpath($filePath);

    if (!$real || !str_starts_with($real, realpath($slidesDir)) || !is_file($real)) {
        return response('Not Found', 404);
    }

    $mimes = [
        'html'  => 'text/html; charset=utf-8',
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'md'    => 'text/plain; charset=utf-8',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'eot'   => 'application/vnd.ms-fontobject',
        'ttf'   => 'font/ttf',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
    ];

    $ext  = strtolower(pathinfo($real, PATHINFO_EXTENSION));
    $mime = $mimes[$ext] ?? 'application/octet-stream';

    return response(file_get_contents($real), 200, ['Content-Type' => $mime]);
});
