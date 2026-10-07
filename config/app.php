<?php
declare(strict_types=1);

$definedBase = defined('EXCOMPASS_BASE_PATH') ? (string) EXCOMPASS_BASE_PATH : '';

return [
    'name' => getenv('EXCOMPASS_APP_NAME') ?: 'ExCompass',
    'city' => getenv('EXCOMPASS_CITY') ?: 'Pune',
    'base_path' => rtrim((string) (getenv('EXCOMPASS_BASE_PATH') ?: $definedBase), '/'),
    'environment' => getenv('EXCOMPASS_ENV') ?: 'development',
];
