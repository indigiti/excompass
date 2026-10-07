<?php
declare(strict_types=1);

namespace ExCompass\Domain\Geo;

final class CityRepository
{
    public function __construct(private readonly array $cities)
    {
    }

    public function all(): array
    {
        return array_values($this->cities);
    }

    public function active(): array
    {
        return array_values(array_filter($this->all(), static fn(array $city): bool => !empty($city['active'])));
    }

    public function find(string $slug): ?array
    {
        foreach($this->all() as $city){
            if(($city['slug']??'')===$slug) return $city;
        }
        return null;
    }

    public function default(): array
    {
        foreach($this->active() as $city){
            if(!empty($city['default'])) return $city;
        }
        $active=$this->active();
        if(!$active){
            throw new \RuntimeException('At least one active city is required.');
        }
        return $active[0];
    }
}
