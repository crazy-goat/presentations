<?php

ini_set('phar.readonly', '0');

const PHAR_FILE = '/build/app.phar';
const APP_DIR   = __DIR__;

if (file_exists(PHAR_FILE)) {
    unlink(PHAR_FILE);
}

$phar = new Phar(PHAR_FILE);
$phar->startBuffering();

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(APP_DIR, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    $relative = substr($file->getPathname(), strlen(APP_DIR) + 1);
    if (in_array($relative, ['build-phar.php'], true)) {
        continue;
    }
    $phar->addFile($file->getPathname(), $relative);
}

$phar->setStub('<?php require "phar://app.phar/server.php"; __HALT_COMPILER(); ?>');
$phar->stopBuffering();

echo 'Built ' . PHAR_FILE . ' (' . number_format(filesize(PHAR_FILE) / 1024 / 1024, 2) . " MB)\n";
