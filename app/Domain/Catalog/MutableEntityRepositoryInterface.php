<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

interface MutableEntityRepositoryInterface extends EntityRepositoryInterface
{
    public function save(array $entity): array;
    public function setStatus(string $vertical, string $slug, string $status): array;
}
