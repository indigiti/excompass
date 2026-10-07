<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

final class EntityRepository implements EntityRepositoryInterface
{
    private DemoEntityCatalog $catalog;

    public function __construct(?DemoEntityCatalog $catalog = null)
    {
        $this->catalog = $catalog ?? new DemoEntityCatalog();
    }

    public function all(): array
    {
        return $this->catalog->all();
    }

    public function forVertical(string $vertical): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn(array $entity): bool => $entity['vertical'] === $vertical
        ));
    }

    public function find(string $vertical, string $slug): ?array
    {
        foreach ($this->all() as $entity) {
            if ($entity['vertical'] === $vertical && $entity['slug'] === $slug) {
                return $entity;
            }
        }
        return null;
    }
}
