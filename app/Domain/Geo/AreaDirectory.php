<?php
declare(strict_types=1);

namespace ExCompass\Domain\Geo;

final class AreaDirectory
{
    public function slug(string $name): string
    {
        $value=mb_strtolower(trim($name));
        $value=preg_replace('/[^a-z0-9]+/u','-',$value) ?? '';
        return trim($value,'-');
    }

    public function fromEntities(array $entities,string $citySlug): array
    {
        $areas=[];
        foreach($entities as $entity){
            if(($entity['city_slug']??'pune')!==$citySlug) continue;
            $name=trim((string)($entity['area_name']??$entity['locality']??$entity['location']??''));
            if($name==='') continue;
            $slug=trim((string)($entity['area_slug']??''));
            if($slug==='') $slug=$this->slug($name);
            if($slug==='') continue;
            if(!isset($areas[$slug])) $areas[$slug]=['slug'=>$slug,'name'=>$name,'count'=>0,'verticals'=>[]];
            $areas[$slug]['count']++;
            $vertical=(string)($entity['vertical']??'');
            if($vertical!=='') $areas[$slug]['verticals'][$vertical]=($areas[$slug]['verticals'][$vertical]??0)+1;
        }
        $areas=array_values($areas);
        usort($areas,static fn(array $a,array $b):int=>($b['count']<=>$a['count'])?:strcmp($a['name'],$b['name']));
        return $areas;
    }

    public function find(array $entities,string $citySlug,string $areaSlug): ?array
    {
        foreach($this->fromEntities($entities,$citySlug) as $area){
            if($area['slug']===$areaSlug) return $area;
        }
        return null;
    }
}
