<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class TaskPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'tasks');
    }

    public function view(User $user, Task $task): bool
    {
        return $this->canViewModule($user, 'tasks');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'tasks');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->canUpdateModule($user, 'tasks');
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->canDeleteModule($user, 'tasks');
    }

    public function changeStatus(User $user, Task $task): bool
    {
        return $this->canUpdateModule($user, 'tasks');
    }

    public function reject(User $user, Task $task): bool
    {
        return $this->canDeleteModule($user, 'tasks') || $this->canManageModule($user, 'tasks');
    }

    public function addNote(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }
}
