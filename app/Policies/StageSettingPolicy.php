<?php

namespace App\Policies;

use App\Models\StageSetting;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class StageSettingPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function view(User $user, StageSetting $stageSetting): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function create(User $user): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function update(User $user, StageSetting $stageSetting): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function delete(User $user, StageSetting $stageSetting): bool
    {
        return $this->canManageModule($user, 'settings');
    }
}
