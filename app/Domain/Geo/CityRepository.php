<?php
declare(strict_types=1);

namespace ExCompass\Domain\Geo;

final class CityRepository
{
    public function all(): array
    {
        return [
            [
                'slug'=>'pune',
                'name'=>'Pune',
                'state'=>'Maharashtra',
                'country'=>'India',
                'active'=>true,
                'default'=>true,
                'latitude'=>18.5204,
                'longitude'=>73.8567,
            ],
        ];
    }

    public function active(): array
    {
        return array_values(array_filter($this->all(), static fn(array $city): bool => !empty($city['active'])));
    }

    public function find(string $slug): ?array
    {
        foreach($this->all() as $city){
            if($city['slug']===$slug) return $city;
        }
        return null;
    }

    public function default(): array
    {
        foreach($this->active() as $city){
            if(!empty($city['default'])) return $city;
        }
        return $this->active()[0];
    }
}
