<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Domain/Auth/UserRepositoryInterface.php';
require_once dirname(__DIR__) . '/app/Infrastructure/Storage/JsonStore.php';
require_once dirname(__DIR__) . '/app/Infrastructure/Storage/JsonUserRepository.php';

use ExCompass\Infrastructure\Storage\JsonStore;
use ExCompass\Infrastructure\Storage\JsonUserRepository;

$storage = require dirname(__DIR__) . '/config/storage.php';

$name = trim((string) ($argv[1] ?? ''));
$email = trim((string) ($argv[2] ?? ''));
$password = (string) (getenv('EXCOMPASS_ADMIN_PASSWORD') ?: '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    fwrite(STDERR, "Usage: EXCOMPASS_ADMIN_PASSWORD='12+ chars' php bin/create-admin.php 'Name' admin@example.com\n");
    exit(1);
}

$repository = new JsonUserRepository(new JsonStore($storage['path']));
$user = $repository->save([
    'name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'roles' => ['admin'],
    'status' => 'active',
]);

echo "Admin user ready: {$user['email']}\n";
