<?php
declare(strict_types=1);

return [
    'session_name' => getenv('EXCOMPASS_SESSION_NAME') ?: 'excompass_admin',
    'idle_timeout' => (int) (getenv('EXCOMPASS_SESSION_IDLE') ?: 7200),
];
