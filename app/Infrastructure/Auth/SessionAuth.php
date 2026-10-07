<?php
declare(strict_types=1);

namespace ExCompass\Infrastructure\Auth;

use ExCompass\Domain\Auth\UserRepositoryInterface;

final class SessionAuth
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly array $config
    ) {
        $this->start();
    }

    public function user(): ?array
    {
        $id = (int) ($_SESSION['auth_user_id'] ?? 0);
        if ($id <= 0) {
            return null;
        }

        $lastSeen = (int) ($_SESSION['auth_last_seen'] ?? 0);
        if ($lastSeen > 0 && (time() - $lastSeen) > (int) $this->config['idle_timeout']) {
            $this->logout();
            return null;
        }

        $user = $this->users->findById($id);
        if (!$user || ($user['status'] ?? '') !== 'active') {
            $this->logout();
            return null;
        }

        $_SESSION['auth_last_seen'] = time();
        return $user;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if (!$user || ($user['status'] ?? '') !== 'active') {
            return false;
        }

        if (!password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['auth_user_id'] = (int) $user['id'];
        $_SESSION['auth_last_seen'] = time();
        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    private function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name((string) $this->config['session_name']);
        session_set_cookie_params([
            'httponly' => true,
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'samesite' => 'Lax',
            'path' => '/',
        ]);
        session_start();
    }
}
