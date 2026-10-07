<?php
declare(strict_types=1);

namespace ExCompass\Domain\Auth;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array;
    public function findById(int $id): ?array;
    public function save(array $user): array;
}
