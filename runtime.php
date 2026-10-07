<?php
declare(strict_types=1);

/**
 * Resolve the private ExCompass runtime.
 *
 * Source checkout: uses this repository root.
 * DigiOps deploy: public_html/excompass -> private_html/excompass.
 */
$explicit = trim((string) (getenv('EXCOMPASS_PRIVATE_ROOT') ?: ''));
$deployed = dirname(__DIR__, 2) . '/private_html/excompass';

if ($explicit !== '') {
    $privateRoot = rtrim($explicit, DIRECTORY_SEPARATOR);
} elseif (is_file($deployed . '/app/bootstrap.php')) {
    $privateRoot = $deployed;
} else {
    $privateRoot = __DIR__;
}

if (!is_file($privateRoot . '/app/bootstrap.php')) {
    http_response_code(503);
    exit('ExCompass runtime unavailable.');
}

if (!defined('EXCOMPASS_PRIVATE_ROOT_PATH')) {
    define('EXCOMPASS_PRIVATE_ROOT_PATH', $privateRoot);
}

if ($privateRoot !== __DIR__ && !defined('EXCOMPASS_BASE_PATH')) {
    define('EXCOMPASS_BASE_PATH', '/excompass');
}

require_once $privateRoot . '/app/bootstrap.php';
