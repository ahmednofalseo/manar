<?php

namespace App\Policies;

use App\Models\JobTitle;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class JobTitlePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function view(User $user, JobTitle $jobTitle): bool
    {
        return $this->canViewModule($user, 'settings');
    }

    public function create(User $user): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $this->canManageModule($user, 'settings');
    }

    public function delete(User $user, JobTitle $jobTitle): bool
    {
        return $this->canManageModule($user, 'settings');
    }
}
