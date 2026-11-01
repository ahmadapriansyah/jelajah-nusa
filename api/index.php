<?php

/**
 * Entry point untuk deployment di Vercel Serverless Function.
 */

// Sembunyikan pesan deprecation notices di lingkungan serverless PHP 8.4+
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Siapkan folder sementara yang writable di /tmp untuk Laravel view, storage, & bootstrap cache
$writableDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$cacheDir = '/tmp/bootstrap/cache';
putenv("APP_PACKAGES_CACHE={$cacheDir}/packages.php");
putenv("APP_SERVICES_CACHE={$cacheDir}/services.php");
putenv("APP_CONFIG_CACHE={$cacheDir}/config.php");
putenv("APP_ROUTES_CACHE={$cacheDir}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$cacheDir}/events.php");
$_ENV['APP_PACKAGES_CACHE'] = "{$cacheDir}/packages.php";
$_ENV['APP_SERVICES_CACHE'] = "{$cacheDir}/services.php";
$_ENV['APP_CONFIG_CACHE'] = "{$cacheDir}/config.php";
$_ENV['APP_ROUTES_CACHE'] = "{$cacheDir}/routes-v7.php";
$_ENV['APP_EVENTS_CACHE'] = "{$cacheDir}/events.php";
$_SERVER['APP_PACKAGES_CACHE'] = "{$cacheDir}/packages.php";
$_SERVER['APP_SERVICES_CACHE'] = "{$cacheDir}/services.php";
$_SERVER['APP_CONFIG_CACHE'] = "{$cacheDir}/config.php";
$_SERVER['APP_ROUTES_CACHE'] = "{$cacheDir}/routes-v7.php";
$_SERVER['APP_EVENTS_CACHE'] = "{$cacheDir}/events.php";

// Teruskan ke public/index.php
require __DIR__ . '/../public/index.php';
