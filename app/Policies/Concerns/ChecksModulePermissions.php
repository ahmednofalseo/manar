<?php

namespace App\Policies\Concerns;

use App\Models\User;

/**
 * Standard permission checks aligned with seeded permission names:
 * {module}.view|create|edit|delete|manage
 */
trait ChecksModulePermissions
{
    protected function canViewModule(User $user, string $module): bool
    {
        return $user->hasPermission("{$module}.view")
            || $user->hasPermission("{$module}.manage");
    }

    protected function canCreateModule(User $user, string $module): bool
    {
        return $user->hasPermission("{$module}.create")
            || $user->hasPermission("{$module}.manage");
    }

    protected function canUpdateModule(User $user, string $module): bool
    {
        return $user->hasPermission("{$module}.edit")
            || $user->hasPermission("{$module}.manage");
    }

    protected function canDeleteModule(User $user, string $module): bool
    {
        return $user->hasPermission("{$module}.delete")
            || $user->hasPermission("{$module}.manage");
    }

    protected function canManageModule(User $user, string $module): bool
    {
        return $user->hasPermission("{$module}.manage");
    }
}
