<?php

namespace App\Policies;

use App\Models\JobTitle;
use App\Models\User;

class JobTitlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    public function view(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    public function update(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }

    public function delete(User $user, JobTitle $jobTitle): bool
    {
        return $user->hasRole('super_admin') || $user->hasPermission('settings.manage');
    }
}
