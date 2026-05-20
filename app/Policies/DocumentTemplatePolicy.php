<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class DocumentTemplatePolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'documents');
    }

    public function view(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $this->canViewModule($user, 'documents');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'documents');
    }

    public function update(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $this->canUpdateModule($user, 'documents');
    }

    public function delete(User $user, DocumentTemplate $documentTemplate): bool
    {
        return $this->canDeleteModule($user, 'documents');
    }
}
