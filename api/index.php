<?php

define('LARAVEL_START', microtime(true));

// On Vercel, /tmp is the only writable directory.
// Pre-create all required storage subdirectories in /tmp.
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $dirs = [
        '/tmp/storage/app/public',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/testing',
        '/tmp/storage/framework/views',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

// Step 1: Load the autoloader FIRST
require __DIR__.'/../vendor/autoload.php';

// Step 2: Boot the application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Step 3: Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);