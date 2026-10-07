<?php
declare(strict_types=1);

namespace ExCompass\Infrastructure\Storage;

use ExCompass\Domain\Catalog\EntityRepository;
use ExCompass\Domain\Catalog\MutableEntityRepositoryInterface;
use InvalidArgumentException;

final class JsonEntityRepository implements MutableEntityRepositoryInterface
{
    public function __construct(
        private readonly JsonStore $store,
        private readonly EntityRepository $seedRepository,
        private readonly string $defaultCitySlug = 'pune',
        private readonly string $defaultCityName = 'Pune'
    ) {
    }

    public function all(): array
    {
        $items = $this->store->read('entities.json', $this->seedRepository->all());
        return array_map([$this, 'normalize'], $items);
    }

    public function forVertical(string $vertical): array
    {
        return $this->forCityVertical($this->defaultCitySlug, $vertical);
    }

    public function forCity(string $citySlug): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn(array $entity): bool => ($entity['city_slug'] ?? '') === $citySlug
        ));
    }

    public function forCityVertical(string $citySlug, string $vertical): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn(array $entity): bool =>
                ($entity['city_slug'] ?? '') === $citySlug &&
                ($entity['vertical'] ?? '') === $vertical
        ));
    }

    public function find(string $vertical, string $slug): ?array
    {
        return $this->findInCity($this->defaultCitySlug, $vertical, $slug);
    }

    public function findInCity(string $citySlug, string $vertical, string $slug): ?array
    {
        foreach ($this->all() as $entity) {
            if (
                ($entity['city_slug'] ?? '') === $citySlug &&
                ($entity['vertical'] ?? '') === $vertical &&
                ($entity['slug'] ?? '') === $slug
            ) {
                return $entity;
            }
        }
        return null;
    }

    public function save(array $entity): array
    {
        $entity = $this->normalize($entity);
        $this->validate($entity);
        $saved = $entity;

        $this->store->update('entities.json', function (array $items) use ($entity, &$saved): array {
            if ($items === []) {
                $items = $this->seedRepository->all();
            }

            $nextId = 1;
            foreach ($items as $index => $item) {
                $items[$index] = $this->normalize($item);
                $nextId = max($nextId, ((int) ($items[$index]['id'] ?? 0)) + 1);
            }
            foreach ($items as $index => $item) {
                if (!isset($item['id']) || (int) $item['id'] <= 0) {
                    $items[$index]['id'] = $nextId++;
                }
            }

            $found = false;
            foreach ($items as $index => $item) {
                $sameId = isset($entity['id']) && (int) $entity['id'] === (int) $item['id'];
                $sameKey =
                    ($item['city_slug'] ?? '') === $entity['city_slug'] &&
                    ($item['vertical'] ?? '') === $entity['vertical'] &&
                    ($item['slug'] ?? '') === $entity['slug'];

                if ($sameId || $sameKey) {
                    $saved = $entity;
                    $saved['id'] = (int) $item['id'];
                    $items[$index] = $saved;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $saved = $entity;
                $saved['id'] = $nextId;
                $items[] = $saved;
            }

            return array_values($items);
        }, $this->seedRepository->all());

        return $this->normalize($saved);
    }

    public function setStatus(string $vertical, string $slug, string $status): array
    {
        return $this->setStatusInCity($this->defaultCitySlug, $vertical, $slug, $status);
    }

    public function setStatusInCity(string $citySlug, string $vertical, string $slug, string $status): array
    {
        $entity = $this->findInCity($citySlug, $vertical, $slug);
        if ($entity === null) {
            throw new InvalidArgumentException('Entity not found.');
        }
        $entity['status'] = $status;
        return $this->save($entity);
    }

    private function normalize(array $entity): array
    {
        $entity['status'] = $entity['status'] ?? 'published';
        $entity['tags'] = array_values(array_filter((array) ($entity['tags'] ?? []), 'is_string'));
        $entity['score'] = (int) ($entity['score'] ?? 0);
        $entity['city_slug'] = strtolower(trim((string) ($entity['city_slug'] ?? $this->defaultCitySlug)));
        $entity['city_name'] = trim((string) ($entity['city_name'] ?? $this->defaultCityName));
        $entity['area_name'] = trim((string) ($entity['area_name'] ?? $entity['locality'] ?? $entity['location'] ?? ''));
        $entity['area_slug'] = strtolower(trim((string) ($entity['area_slug'] ?? $this->slugify($entity['area_name']))));
        return $entity;
    }

    private function validate(array $entity): void
    {
        foreach (['city_slug','area_slug','vertical', 'slug', 'name', 'location'] as $key) {
            if (trim((string) ($entity[$key] ?? '')) === '') {
                throw new InvalidArgumentException("Missing entity field: {$key}");
            }
        }

        foreach (['city_slug','area_slug','slug'] as $key) {
            if (!preg_match('/^[a-z0-9-]+$/', (string) $entity[$key])) {
                throw new InvalidArgumentException("Entity {$key} is invalid.");
            }
        }

        if (!in_array((string) $entity['status'], ['draft','review','approved','published','archived'], true)) {
            throw new InvalidArgumentException('Entity status is invalid.');
        }

        if ($entity['score'] < 0 || $entity['score'] > 100) {
            throw new InvalidArgumentException('Entity score must be between 0 and 100.');
        }
    }

    private function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?? '';
        return trim($value, '-');
    }
}
