<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

final class EntityProfileService
{
    public function __construct(
        private readonly VerticalDetailRepository $details,
        private readonly RemoteMediaCatalog $media
    ) {
    }

    public function enrich(array $entity, int $index = 0): array
    {
        $detail = $this->details->find((string) $entity['vertical']);
        $score = (int) ($entity['score'] ?? 0);
        $entity['locality'] = $entity['locality'] ?? $entity['location'];
        $entity['category'] = $entity['category'] ?? $this->pick($detail['categories'], $index);
        $entity['tier'] = $entity['tier'] ?? $this->pick($detail['tiers'], $index + 1);
        $entity['availability'] = $entity['availability'] ?? $this->pick($detail['statuses'], $index + 2);
        $entity['intent'] = $entity['intent'] ?? $this->pick($detail['intents'], $index);
        $entity['primary'] = $entity['primary'] ?? ($entity['tags'][0] ?? $entity['category']);
        $entity['secondary'] = $entity['secondary'] ?? ($entity['tags'][1] ?? $detail['singular']);
        $entity['tertiary'] = $entity['tertiary'] ?? ($entity['tags'][2] ?? $entity['intent']);
        $entity['rating'] = $this->rating($score);
        $entity['editorial_status'] = $entity['editorial_status'] ?? ($index === 0 ? 'featured' : 'ranking');
        $entity['badges'] = $entity['badges'] ?? match ($index) {
            0 => ['ExCompass Top Pick', 'Best in Category'],
            1 => ['Editor’s Choice'],
            2 => ['Best Value'],
            default => [],
        };
        $entity['description'] = $entity['description'] ?? sprintf(
            '%s is included in the ExCompass demo ranking experience for %s. The copy, scores and attributes are working data designed to exercise the complete product before verified editorial research is published.',
            $entity['name'],
            $detail['singular']
        );
        $entity['observation'] = $entity['observation'] ?? ($entity['highlight'] . ' The working score balances quality, access, value and user experience; the editorial desk can replace this demonstration observation with verified research.');
        $entity['breakdown'] = $entity['breakdown'] ?? $this->breakdown($score, $detail['score_labels']);
        $entity['standout'] = $entity['standout'] ?? ['Strong category performance','Convenient Pune location','Balanced quality and value proposition','Clear fit for its target audience'];
        $entity['liked'] = $entity['liked'] ?? ['Location and accessibility','Core service or product quality','User experience'];
        $entity['consider'] = $entity['consider'] ?? ['Peak-time demand may affect access','Pricing or value should be checked against current offers','Verify latest availability before acting'];
        $entity['best_for'] = $entity['best_for'] ?? array_values(array_unique([$entity['intent'],$detail['intents'][0],$detail['intents'][1]]));
        $entity['nearby'] = $entity['nearby'] ?? [
            ['name'=>'Major Road / Transit','time'=>(8+$index*2).' mins'],
            ['name'=>'Hospital / Essential','time'=>(10+$index).' mins'],
            ['name'=>'Retail / Dining','time'=>(12+$index*2).' mins'],
            ['name'=>'City Hub','time'=>(18+$index*2).' mins'],
        ];
        $entity['locality_scores'] = $entity['locality_scores'] ?? [
            'Connectivity'=>min(95,84+$index),
            'Schools'=>min(95,80+$index*2),
            'Healthcare'=>min(95,82+$index),
            'Restaurants'=>min(95,78+$index*2),
            'Safety'=>min(95,81+$index),
            'Green Space'=>min(95,72+$index*2),
            'Affordability'=>max(58,82-$index*3),
        ];
        $entity['coordinates'] = $entity['coordinates'] ?? [18.5204 + (($index-2)*0.012),73.8567 + (($index%3-1)*0.018)];
        $entity['reviewer'] = $entity['reviewer'] ?? 'ExCompass Research Desk';
        $entity['review_date'] = $entity['review_date'] ?? '2026-10-07';
        $entity['score_version'] = $entity['score_version'] ?? '1.2-demo';
        $entity['evidence'] = $entity['evidence'] ?? ['Editorial desk review','Publicly available entity information','Location and accessibility assessment','Category-specific scoring worksheet'];
        $entity['demo'] = true;

        if ($entity['vertical'] === 'real-estate') {
            $entity = $this->realEstateDetails($entity, $index);
        }

        return $this->media->enrich($entity, $index);
    }

    private function realEstateDetails(array $entity, int $index): array
    {
        $specific = [
            'godrej-emerald-waters'=>['Apartment','₹1–2 Cr','2027','Family','₹ 1.1 Cr – 2.4 Cr','2, 3 & 4 BHK','17 Acres'],
            'lodha-panache'=>['Apartment','₹1–2 Cr','2028+','End Use','₹ 1.3 Cr – 2.8 Cr','2 & 3 BHK','12 Acres'],
            'vtp-earth-one'=>['Township','Under ₹1 Cr','2028+','Investment','₹ 78 L – 1.5 Cr','2 & 3 BHK','14 Acres'],
            'kolte-patil-24k-manor'=>['Apartment','₹2 Cr+','2027','End Use','₹ 2.1 Cr – 4.5 Cr','3 & 4 BHK','8 Acres'],
            'nyati-elysia'=>['Apartment','₹1–2 Cr','Ready','Family','₹ 90 L – 1.8 Cr','2 & 3 BHK','9 Acres'],
            'majestique-marbella'=>['Apartment','₹1–2 Cr','2028+','Investment','₹ 1.2 Cr – 2.3 Cr','2 & 3 BHK','10 Acres'],
        ];
        $row = $specific[$entity['slug']] ?? [
            $index % 5 === 0 ? 'Township' : ($index % 4 === 0 ? 'Villa' : 'Apartment'),
            $index % 6 === 0 ? '₹2 Cr+' : ($index % 3 === 0 ? 'Under ₹1 Cr' : '₹1–2 Cr'),
            $index % 4 === 0 ? 'Ready' : ($index % 2 === 0 ? '2027' : '2028+'),
            $index % 3 === 0 ? 'Investment' : ($index % 2 === 0 ? 'End Use' : 'Family'),
            '₹ 72 L – 2.2 Cr',
            '2 & 3 BHK',
            (($index % 9) + 6) . ' Acres',
        ];
        [$entity['category'],$entity['tier'],$entity['availability'],$entity['intent'],$entity['primary'],$entity['secondary'],$entity['tertiary']] = $row;
        return $entity;
    }

    private function pick(array $values, int $index): string
    {
        return $values[$index % count($values)];
    }

    private function rating(int $score): string
    {
        return match (true) {
            $score >= 90 => 'Exceptional',
            $score >= 85 => 'Excellent',
            $score >= 80 => 'Very Good',
            $score >= 70 => 'Good',
            default => 'Developing',
        };
    }

    private function breakdown(int $total, array $labels): array
    {
        $maxima = [20,20,15,15,15,10,5];
        $remaining = $total;
        $rows = [];
        foreach ($maxima as $i => $max) {
            $leftMax = array_sum(array_slice($maxima, $i + 1));
            $min = max(0, $remaining - $leftMax);
            $ideal = (int) round($total * $max / 100);
            $score = min($max, max($min, $ideal));
            if ($i === count($maxima) - 1) {
                $score = $remaining;
            }
            $remaining -= $score;
            $rows[] = ['label'=>$labels[$i] ?? ('Criterion '.($i+1)),'score'=>$score,'max'=>$max,'evidence'=>'Working evidence placeholder — replace with approved source notes.'];
        }
        return $rows;
    }
}
