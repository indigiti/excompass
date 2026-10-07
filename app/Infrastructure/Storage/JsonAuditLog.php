<?php
declare(strict_types=1);

namespace ExCompass\Infrastructure\Storage;

final class JsonAuditLog
{
    public function __construct(private readonly JsonStore $store)
    {
    }

    public function record(array $user, string $action, string $subjectType, string $subjectKey, ?array $before, ?array $after): void
    {
        $this->store->update('audit.json', static function (array $items) use ($user, $action, $subjectType, $subjectKey, $before, $after): array {
            $items[] = [
                'id' => count($items) + 1,
                'user_id' => (int) ($user['id'] ?? 0),
                'user_email' => (string) ($user['email'] ?? ''),
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_key' => $subjectKey,
                'before' => $before,
                'after' => $after,
                'created_at' => gmdate('c'),
            ];
            return $items;
        });
    }

    public function recent(int $limit = 20): array
    {
        $items = $this->store->read('audit.json');
        return array_slice(array_reverse($items), 0, max(1, $limit));
    }
}
