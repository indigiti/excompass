<?php
declare(strict_types=1);

namespace ExCompass\Infrastructure\Storage;

use ExCompass\Domain\Auth\UserRepositoryInterface;
use InvalidArgumentException;

final class JsonUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly JsonStore $store)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));
        foreach ($this->store->read('users.json') as $user) {
            if (strtolower((string) ($user['email'] ?? '')) === $email) {
                return $user;
            }
        }
        return null;
    }

    public function findById(int $id): ?array
    {
        foreach ($this->store->read('users.json') as $user) {
            if ((int) ($user['id'] ?? 0) === $id) {
                return $user;
            }
        }
        return null;
    }

    public function save(array $user): array
    {
        $email = strtolower(trim((string) ($user['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('A valid email is required.');
        }
        if (trim((string) ($user['name'] ?? '')) === '') {
            throw new InvalidArgumentException('A name is required.');
        }

        $user['email'] = $email;
        $user['roles'] = array_values(array_unique(array_map('strval', (array) ($user['roles'] ?? []))));
        $user['status'] = $user['status'] ?? 'active';
        $saved = $user;

        $this->store->update('users.json', function (array $users) use ($user, &$saved): array {
            $found = false;
            $maxId = 0;
            foreach ($users as $index => $existing) {
                $maxId = max($maxId, (int) ($existing['id'] ?? 0));
                $sameId = isset($user['id']) && (int) $user['id'] === (int) ($existing['id'] ?? 0);
                $sameEmail = strtolower((string) ($existing['email'] ?? '')) === $user['email'];
                if ($sameId || $sameEmail) {
                    $saved = $user + ['id' => (int) ($existing['id'] ?? ($maxId + 1))];
                    $users[$index] = $saved;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $saved = $user + ['id' => $maxId + 1];
                $users[] = $saved;
            }
            return array_values($users);
        });

        return $saved;
    }
}
