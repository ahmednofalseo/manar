<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class CityPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function view(User $user, City $city): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function create(User $user): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function update(User $user, City $city): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function delete(User $user, City $city): bool
    {
        return $this->canManageModule($user, 'settings');
    }
}
