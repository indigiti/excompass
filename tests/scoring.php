<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Domain/Ranking/ScoringPolicy.php';

use ExCompass\Domain\Ranking\ScoringPolicy;

$policy = new ScoringPolicy();
$total = $policy->weightedTotal([
    ['score'=>8,'maximum'=>10,'weight'=>20],
    ['score'=>45,'maximum'=>50,'weight'=>50],
    ['score'=>24,'maximum'=>30,'weight'=>30],
]);

$expected = 85.0;
$ok = abs($total - $expected) < 0.001;

echo ($ok ? 'PASS' : 'FAIL') . "  weighted scoring = {$total}\n";
exit($ok ? 0 : 1);
