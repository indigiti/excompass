<?php
declare(strict_types=1);
namespace ExCompass\Domain\Catalog;

final class VerticalRepository {
    public function all(): array {
        return [
            ['slug'=>'real-estate','name'=>'Real Estate','icon'=>'home','accent'=>'#ef4444','prompt'=>'Homes worth shortlisting'],
            ['slug'=>'hospitals','name'=>'Hospitals','icon'=>'hospital','accent'=>'#0ea5e9','prompt'=>'Care you can evaluate'],
            ['slug'=>'schools','name'=>'Schools','icon'=>'school','accent'=>'#8b5cf6','prompt'=>'Schools that fit your child'],
            ['slug'=>'restaurants','name'=>'Restaurants','icon'=>'food','accent'=>'#f97316','prompt'=>'Places worth the table'],
            ['slug'=>'hotels','name'=>'Hotels','icon'=>'hotel','accent'=>'#14b8a6','prompt'=>'Stays worth booking'],
            ['slug'=>'colleges','name'=>'Colleges','icon'=>'college','accent'=>'#6366f1','prompt'=>'Campuses worth comparing'],
            ['slug'=>'malls','name'=>'Malls','icon'=>'mall','accent'=>'#ec4899','prompt'=>'Shopping and leisure'],
            ['slug'=>'gyms','name'=>'Gyms','icon'=>'gym','accent'=>'#22c55e','prompt'=>'Fitness that fits'],
            ['slug'=>'salons','name'=>'Salons','icon'=>'salon','accent'=>'#d946ef','prompt'=>'Beauty and grooming'],
            ['slug'=>'automotive','name'=>'Automotive','icon'=>'auto','accent'=>'#64748b','prompt'=>'Dealers and service'],
            ['slug'=>'coworking','name'=>'Coworking','icon'=>'cowork','accent'=>'#06b6d4','prompt'=>'Workspaces that work'],
            ['slug'=>'localities','name'=>'Localities','icon'=>'locality','accent'=>'#10b981','prompt'=>'Decode where to live'],
            ['slug'=>'preschools','name'=>'Preschools','icon'=>'preschool','accent'=>'#f59e0b','prompt'=>'Early years choices'],
            ['slug'=>'doctors','name'=>'Doctors','icon'=>'doctor','accent'=>'#0284c7','prompt'=>'Specialists to know'],
            ['slug'=>'banquets','name'=>'Banquets','icon'=>'banquet','accent'=>'#a855f7','prompt'=>'Venues for the occasion'],
            ['slug'=>'cafes','name'=>'Cafés','icon'=>'cafe','accent'=>'#a16207','prompt'=>'Coffee worth crossing town for'],
            ['slug'=>'weekend','name'=>'Weekend','icon'=>'weekend','accent'=>'#16a34a','prompt'=>'Your next Pune escape'],
        ];
    }
    public function find(string $slug): ?array {
        foreach($this->all() as $v) if($v['slug']===$slug) return $v;
        return null;
    }
}
