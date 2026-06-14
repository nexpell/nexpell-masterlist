<?php

if (!function_exists('safe_query')) {
    die('Access denied');
}

global $modulname, $version, $plugin;

$modulname = 'masterlist';
$version = isset($plugin['version']) ? (string)$plugin['version'] : ($version ?? '0.0.0');

require __DIR__ . '/install.php';
