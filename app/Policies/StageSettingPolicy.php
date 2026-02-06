<?php

namespace App\Policies;

use App\Models\StageSetting;
use App\Models\User;

class StageSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StageSetting $stageSetting): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StageSetting $stageSetting): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StageSetting $stageSetting): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }
}
