<?php

namespace App\Policies;

use App\Models\ProjectType;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ProjectTypePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function view(User $user, ProjectType $projectType): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function create(User $user): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function update(User $user, ProjectType $projectType): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function delete(User $user, ProjectType $projectType): bool
    {
        return $this->canManageModule($user, 'settings');
    }
}
