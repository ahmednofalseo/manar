<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ServicePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'services');
    }

    public function view(User $user, Service $service): bool
    {
        return $this->canViewModule($user, 'services');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'services');
    }

    public function update(User $user, Service $service): bool
    {
        return $this->canUpdateModule($user, 'services');
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->canDeleteModule($user, 'services');
    }
}
