<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

use ExCompass\Domain\Media\RemoteImagePolicy;

final class RemoteMediaCatalog
{
    public function __construct(private readonly RemoteImagePolicy $policy)
    {
    }

    public function enrich(array $entity, int $index = 0): array
    {
        $defaults = $this->defaultsFor((string)($entity['vertical'] ?? 'real-estate'), $index);
        $hero = $this->policy->sanitize($entity['hero_image_url'] ?? null) ?? $defaults['hero'];
        $customGallery = $this->policy->sanitizeMany((array)($entity['gallery_image_urls'] ?? []), 8);
        $gallery = $customGallery ?: $defaults['gallery'];

        if ($hero !== null && !in_array($hero, $gallery, true)) {
            array_unshift($gallery, $hero);
        }
        $gallery = array_slice(array_values(array_unique(array_filter($gallery))), 0, 8);

        $entity['hero_image_url'] = $hero;
        $entity['gallery_image_urls'] = $gallery;
        $entity['image_alt'] = trim((string)($entity['image_alt'] ?? '')) ?: ('Representative photography for ' . ($entity['name'] ?? 'ExCompass profile'));
        $entity['image_source_name'] = trim((string)($entity['image_source_name'] ?? '')) ?: $defaults['source_name'];
        $entity['image_source_url'] = $this->safeSourceUrl($entity['image_source_url'] ?? null) ?? $defaults['source_url'];
        $entity['image_credit'] = trim((string)($entity['image_credit'] ?? '')) ?: $defaults['credit'];
        $entity['image_verified'] = (bool)($entity['image_verified'] ?? false);
        $entity['image_disclaimer'] = $entity['image_verified']
            ? 'Verified entity imagery.'
            : 'Representative demo photography — not verified as imagery of this specific entity.';

        return $entity;
    }

    private function defaultsFor(string $vertical, int $index): array
    {
        $realEstate = [
            'https://images.unsplash.com/photo-1768834840686-b7f6c40b6020?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1761953744731-4e63f4f65cb7?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1775733924026-93a6e1959aaf?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1768834840686-b7f6c40b6020?auto=format&fit=crop&w=1200&q=76',
        ];

        $sets = [
            'real-estate' => $realEstate,
            'hospitals' => [
                'https://images.unsplash.com/photo-1769147555720-71fc71bfc216?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1600&q=80',
            ],
            'doctors' => [
                'https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1769147555720-71fc71bfc216?auto=format&fit=crop&w=1600&q=80',
            ],
            'restaurants' => [
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&q=80',
            ],
            'cafes' => [
                'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1511081692775-05d0f180a065?auto=format&fit=crop&w=1600&q=80',
            ],
            'gyms' => [
                'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=1600&q=80',
            ],
            'coworking' => [
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1600&q=80',
            ],
            'hotels' => [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1600&q=80',
            ],
            'schools' => [
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=80',
            ],
            'colleges' => [
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1600&q=80',
            ],
            'malls' => [
                'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1519567241046-7f570eee3ce6?auto=format&fit=crop&w=1600&q=80',
            ],
            'salons' => [
                'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1600&q=80',
            ],
            'automotive' => [
                'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80',
            ],
            'localities' => [
                'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1519501025264-65ba15a82390?auto=format&fit=crop&w=1600&q=80',
            ],
            'preschools' => [
                'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=1600&q=80',
            ],
            'banquets' => [
                'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1507501336603-6e31db2be093?auto=format&fit=crop&w=1600&q=80',
            ],
            'weekend' => [
                'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=80',
                'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=80',
            ],
        ];

        $gallery = $sets[$vertical] ?? $realEstate;
        if (count($gallery) === 1) {
            $gallery[] = $gallery[0];
        }
        $rotated = $gallery;
        if ($gallery) {
            $shift = $index % count($gallery);
            $rotated = array_merge(array_slice($gallery, $shift), array_slice($gallery, 0, $shift));
        }

        return [
            'hero' => $rotated[0] ?? null,
            'gallery' => $rotated,
            'source_name' => 'Unsplash',
            'source_url' => 'https://unsplash.com/',
            'credit' => 'Representative demo photography from Unsplash',
        ];
    }

    private function safeSourceUrl(?string $url): ?string
    {
        $url = trim((string)$url);
        if ($url === '') return null;
        $parts = parse_url($url);
        if (!is_array($parts) || strtolower((string)($parts['scheme'] ?? '')) !== 'https' || empty($parts['host'])) {
            return null;
        }
        return $url;
    }
}
