<?php
require_once 'vendor/autoload.php';

define('ROOT', __DIR__);

spl_autoload_register(function($name) {
    $possibleFileLocations = array(
        '/app/',
        '/config/',
    );
    foreach ($possibleFileLocations as $location) {
        $file = __DIR__ . $location . str_replace('\\', '/', $name) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    throw new RuntimeException('Class not found for: ' . $name);
});

function assertCli() {
    if (PHP_SAPI !== 'cli') {
        throw new RuntimeException('Not running as CLI!');
    }
}
