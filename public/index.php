<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-invalidate stale route and config cache on shared hosting (Hostinger) after git updates
$routeCacheFile = __DIR__.'/../bootstrap/cache/routes-v7.php';
$routesWebFile = __DIR__.'/../routes/web.php';
if (file_exists($routeCacheFile) && file_exists($routesWebFile) && filemtime($routeCacheFile) < filemtime($routesWebFile)) {
    @unlink($routeCacheFile);
}

// Emergency cache clearing trigger via browser (e.g. ?clear_cache=sma1le_refresh)
if (isset($_GET['clear_cache']) && in_array($_GET['clear_cache'], ['1', 'true', 'sma1le_refresh'], true)) {
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
