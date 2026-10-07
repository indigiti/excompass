<?php
declare(strict_types=1);

namespace ExCompass\Domain\Auth;

final class Access
{
    private const ROLE_PERMISSIONS = [
        'admin' => ['*'],
        'editor' => [
            'admin.view',
            'entities.view',
            'entities.edit',
            'entities.review',
            'entities.approve',
            'entities.publish',
            'scores.edit',
            'evidence.manage',
        ],
        'researcher' => [
            'admin.view',
            'entities.view',
            'entities.edit',
            'entities.submit',
            'evidence.manage',
        ],
        'commercial' => [
            'admin.view',
            'entities.view',
            'leads.view',
            'commercial.manage',
        ],
    ];

    public function allows(?array $user, string $permission): bool
    {
        if (!$user || ($user['status'] ?? 'disabled') !== 'active') {
            return false;
        }

        foreach ((array) ($user['roles'] ?? []) as $role) {
            $permissions = self::ROLE_PERMISSIONS[(string) $role] ?? [];
            if (in_array('*', $permissions, true) || in_array($permission, $permissions, true)) {
                return true;
            }
        }

        return false;
    }
}
