<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ProjectPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'projects');
    }

    public function view(User $user, Project $project): bool
    {
        return $this->canViewModule($user, 'projects');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'projects');
    }

    public function update(User $user, Project $project): bool
    {
        return $this->canUpdateModule($user, 'projects');
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->canDeleteModule($user, 'projects');
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->canManageModule($user, 'projects');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $this->canManageModule($user, 'projects');
    }
}
