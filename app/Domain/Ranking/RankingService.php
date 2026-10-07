<?php
declare(strict_types=1);
namespace ExCompass\Domain\Ranking;

final class RankingService {
    public function rank(array $entities): array {
        usort($entities,fn(array $a,array $b):int=>$b['score']<=>$a['score']);
        foreach($entities as $i=>&$e) $e['rank']=$i+1;
        return $entities;
    }
    public function band(int $score): string {
        return match(true){$score>=90=>'Exceptional',$score>=85=>'Excellent',$score>=80=>'Very Good',$score>=70=>'Good',default=>'Developing'};
    }
}
