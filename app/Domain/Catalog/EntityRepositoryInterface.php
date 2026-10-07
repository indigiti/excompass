<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

interface EntityRepositoryInterface
{
    public function all(): array;
    public function forVertical(string $vertical): array;
    public function find(string $vertical, string $slug): ?array;
}
