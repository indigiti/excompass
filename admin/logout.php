<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

use ExCompass\Support\Csrf;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::valid($_POST['_token'] ?? null)) {
    $auth->logout();
}
admin_redirect('login.php');
