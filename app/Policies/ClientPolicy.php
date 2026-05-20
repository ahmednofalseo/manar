<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class ClientPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'clients');
    }

    public function view(User $user, Client $client): bool
    {
        return $this->canViewModule($user, 'clients');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'clients');
    }

    public function update(User $user, Client $client): bool
    {
        return $this->canUpdateModule($user, 'clients');
    }

    public function delete(User $user, ?Client $client = null): bool
    {
        return $this->canDeleteModule($user, 'clients');
    }

    public function deleteAny(User $user): bool
    {
        return $this->canDeleteModule($user, 'clients');
    }

    public function addNote(User $user, Client $client): bool
    {
        return $this->canUpdateModule($user, 'clients');
    }

    public function viewAttachments(User $user, Client $client): bool
    {
        return $this->canViewModule($user, 'clients');
    }

    public function uploadAttachment(User $user, Client $client): bool
    {
        return $this->canUpdateModule($user, 'clients');
    }

    public function restore(User $user, Client $client): bool
    {
        return $this->canManageModule($user, 'clients');
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $this->canManageModule($user, 'clients');
    }
}
