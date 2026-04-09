<?php

require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

$slidesDir = '/slides';

$mimeTypes = [
    'html' => 'text/html; charset=utf-8',
    'css'  => 'text/css',
    'js'   => 'application/javascript',
    'md'   => 'text/plain; charset=utf-8',
    'woff' => 'font/woff',
    'woff2'=> 'font/woff2',
    'eot'  => 'application/vnd.ms-fontobject',
    'ttf'  => 'font/ttf',
    'svg'  => 'image/svg+xml',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'ico'  => 'image/x-icon',
];

$worker = new Worker('http://0.0.0.0:8080');
$worker->count = 1;
$worker->name = 'presentation';

$worker->onMessage = function (TcpConnection $connection, Request $request) use ($slidesDir, $mimeTypes) {
    $path = $request->path();

    if ($path === '/') {
        $path = '/index.html';
    }

    $file = realpath($slidesDir . $path);

    if ($file === false || strpos($file, realpath($slidesDir)) !== 0 || !is_file($file)) {
        $connection->send(new Response(404, [], '404 Not Found'));
        return;
    }

    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

    $connection->send(new Response(200, ['Content-Type' => $mime], file_get_contents($file)));
};

Worker::runAll();
