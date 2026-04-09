#!/usr/bin/env php
<?php

$buildDir = __DIR__ . '/build';
if (!is_dir($buildDir)) {
    mkdir($buildDir, 0777, true);
}

$pharFile = $buildDir . '/webman.phar';
if (file_exists($pharFile)) {
    unlink($pharFile);
}

echo "Packing PHAR...\n";

$phar = new Phar($pharFile);
$phar->startBuffering();
$phar->setSignatureAlgorithm(Phar::SHA256);
$phar->buildFromDirectory(__DIR__, '#^(?!.*(/.git/|/build/|/runtime/))(.*)$#');
$phar->setStub("#!/usr/bin/env php\n<?php\nPhar::mapPhar('webman');\nrequire 'phar://webman/vendor/autoload.php';\nsupport\\App::run();\n__HALT_COMPILER();");
$phar->stopBuffering();
unset($phar);

echo "Combining with micro.sfx...\n";

$sfxFile = $buildDir . '/php8.3.micro.sfx';
$binFile = $buildDir . '/webman.bin';

file_put_contents($binFile, file_get_contents($sfxFile) . file_get_contents($pharFile));
chmod($binFile, 0755);

echo "Done: $binFile\n";
