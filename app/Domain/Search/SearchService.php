<?php
declare(strict_types=1);
namespace ExCompass\Domain\Search;

final class SearchService {
    public function search(array $entities,array $verticals,string $query,?string $vertical=null): array {
        $query=mb_strtolower(trim($query));
        $names=[];
        foreach($verticals as $v) $names[$v['slug']]=$v['name'];
        $out=array_filter($entities,function(array $e)use($query,$vertical,$names):bool{
            if($vertical&&$e['vertical']!==$vertical) return false;
            if($query==='') return true;
            $hay=implode(' ',[$e['name'],$e['location'],$e['highlight'],$names[$e['vertical']]??'',implode(' ',$e['tags'])]);
            return str_contains(mb_strtolower($hay),$query);
        });
        usort($out,fn(array $a,array $b):int=>$b['score']<=>$a['score']);
        return array_values($out);
    }
}
