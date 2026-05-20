<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class SettingPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function create(User $user): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $this->canManageModule($user, 'settings');
    }
}
