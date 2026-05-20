<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class UserPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'users');
    }

    public function view(User $user, User $model): bool
    {
        return $this->canViewModule($user, 'users') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'users');
    }

    public function update(User $user, User $model): bool
    {
        return $this->canUpdateModule($user, 'users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $this->canDeleteModule($user, 'users') && $user->id !== $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
