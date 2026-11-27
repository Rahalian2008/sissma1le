<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-invalidate stale route and config cache on shared hosting (Hostinger) after git updates
$routeCacheFile = __DIR__.'/../bootstrap/cache/routes-v7.php';
$routesWebFile = __DIR__.'/../routes/web.php';
$reqUri = $_SERVER['REQUEST_URI'] ?? '';

if (file_exists($routeCacheFile)) {
    $isOlder = file_exists($routesWebFile) && filemtime($routeCacheFile) < filemtime($routesWebFile);
    $isEmergency = str_contains($reqUri, 'artisan-optimize-clear') || str_contains($reqUri, 'clear_cache');
    $contentCheck = false;
    if (! $isOlder && ! $isEmergency && filesize($routeCacheFile) > 0) {
        $sample = @file_get_contents($routeCacheFile, false, null, 0, 10000);
        if ($sample !== false && (! str_contains($sample, 'pengaduan') || ! str_contains($sample, 'kiosk-tamu'))) {
            $contentCheck = true;
        }
    }

    if ($isOlder || $isEmergency || $contentCheck) {
        @unlink($routeCacheFile);
    }
}

// Emergency cache clearing trigger via browser (e.g. ?clear_cache=sma1le_refresh or /artisan-optimize-clear)
if (isset($_GET['clear_cache']) || str_contains($reqUri, 'artisan-optimize-clear')) {
    @unlink(__DIR__.'/../bootstrap/cache/routes-v7.php');
    @unlink(__DIR__.'/../bootstrap/cache/config.php');
    @unlink(__DIR__.'/../bootstrap/cache/events.php');
    @unlink(__DIR__.'/../bootstrap/cache/packages.php');
    @unlink(__DIR__.'/../bootstrap/cache/services.php');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
