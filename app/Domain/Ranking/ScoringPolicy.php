<?php
declare(strict_types=1);

namespace ExCompass\Domain\Ranking;

use InvalidArgumentException;

final class ScoringPolicy
{
    public function weightedTotal(array $items): float
    {
        $totalWeight = 0.0;
        $weighted = 0.0;

        foreach ($items as $item) {
            $score = (float) ($item['score'] ?? 0);
            $maximum = (float) ($item['maximum'] ?? 0);
            $weight = (float) ($item['weight'] ?? 0);

            if ($maximum <= 0 || $weight < 0 || $score < 0 || $score > $maximum) {
                throw new InvalidArgumentException('Invalid scoring item.');
            }

            $totalWeight += $weight;
            $weighted += ($score / $maximum) * $weight;
        }

        if ($totalWeight <= 0) {
            return 0.0;
        }

        return round(($weighted / $totalWeight) * 100, 2);
    }
}
