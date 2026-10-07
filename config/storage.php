<?php
declare(strict_types=1);

return [
    'driver' => getenv('STORAGE_DRIVER') ?: 'json',
    'path' => getenv('EXCOMPASS_STORAGE_PATH') ?: dirname(__DIR__) . '/storage/data',
];
