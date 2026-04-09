<?php

require_once __DIR__ . '/vendor/autoload.php';

use Webman\Config;
use Webman\Route;
use Webman\App;
use Workerman\Worker;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

Config::load(config_path());

$worker = new Worker('http://0.0.0.0:8080');
$worker->name  = 'webman';
$worker->count = 1;

$worker->onWorkerStart = function () {
    Config::load(config_path(), ['route']);
    Route::load(config_path('route.php'));
};

Worker::$logFile   = '/dev/null';
Worker::$pidFile   = '/dev/null';
Worker::$stdoutFile = '/dev/null';

App::run();
