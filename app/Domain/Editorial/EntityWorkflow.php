<?php
declare(strict_types=1);

namespace ExCompass\Domain\Editorial;

use ExCompass\Domain\Auth\Access;
use DomainException;

final class EntityWorkflow
{
    private const TRANSITIONS = [
        'draft' => ['review' => 'entities.submit'],
        'review' => ['draft' => 'entities.review', 'approved' => 'entities.approve'],
        'approved' => ['review' => 'entities.review', 'published' => 'entities.publish'],
        'published' => ['approved' => 'entities.publish', 'archived' => 'entities.publish'],
        'archived' => ['draft' => 'entities.approve'],
    ];

    public function __construct(private readonly Access $access)
    {
    }

    public function canTransition(array $user, string $from, string $to): bool
    {
        $permission = self::TRANSITIONS[$from][$to] ?? null;
        return $permission !== null && $this->access->allows($user, $permission);
    }

    public function assertTransition(array $user, string $from, string $to): void
    {
        if (!$this->canTransition($user, $from, $to)) {
            throw new DomainException("Transition {$from} → {$to} is not permitted.");
        }
    }

    public function available(array $user, string $from): array
    {
        $available = [];
        foreach (self::TRANSITIONS[$from] ?? [] as $to => $permission) {
            if ($this->access->allows($user, $permission)) {
                $available[] = $to;
            }
        }
        return $available;
    }
}
