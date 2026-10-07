<?php
declare(strict_types=1);
return [
    'name'=>getenv('EXCOMPASS_APP_NAME') ?: 'ExCompass',
    'city'=>getenv('EXCOMPASS_CITY') ?: 'Pune',
    'base_path'=>rtrim((string)(getenv('EXCOMPASS_BASE_PATH') ?: ''),'/'),
    'environment'=>getenv('EXCOMPASS_ENV') ?: 'development',
];
