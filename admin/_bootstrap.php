<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/runtime.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Domain/Auth/UserRepositoryInterface.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Domain/Auth/Access.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Infrastructure/Storage/JsonUserRepository.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Infrastructure/Auth/SessionAuth.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Support/Csrf.php';
require_once EXCOMPASS_PRIVATE_ROOT_PATH . '/app/Domain/Editorial/EntityWorkflow.php';

use ExCompass\Domain\Auth\Access;
use ExCompass\Domain\Editorial\EntityWorkflow;
use ExCompass\Infrastructure\Auth\SessionAuth;
use ExCompass\Infrastructure\Storage\JsonUserRepository;
use ExCompass\Support\Csrf;

$authConfig = require EXCOMPASS_PRIVATE_ROOT_PATH . '/config/auth.php';
$access = new Access();
$userRepository = new JsonUserRepository($store);
$auth = new SessionAuth($userRepository, $authConfig);
$workflow = new EntityWorkflow($access);

function admin_user(): ?array
{
    global $auth;
    return $auth->user();
}

function require_admin(string $permission = 'admin.view'): array
{
    global $access;
    $user = admin_user();
    if (!$user) {
        header('Location: ' . u('admin/login.php'));
        exit;
    }
    if (!$access->allows($user, $permission)) {
        http_response_code(403);
        exit('Forbidden');
    }
    return $user;
}

function admin_redirect(string $path): never
{
    header('Location: ' . u('admin/' . ltrim($path, '/')));
    exit;
}

function admin_flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['admin_flash'] = $message;
        return null;
    }
    $value = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);
    return is_string($value) ? $value : null;
}
