<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Policies\Concerns\ChecksModulePermissions;

class DocumentPolicy
{
    use ChecksModulePermissions;

    public function viewAny(User $user): bool
    {
        return $this->canViewModule($user, 'documents');
    }

    public function view(User $user, Document $document): bool
    {
        return $this->canViewModule($user, 'documents');
    }

    public function create(User $user): bool
    {
        return $this->canCreateModule($user, 'documents');
    }

    public function update(User $user, Document $document): bool
    {
        if ($document->type === 'technical_report' && $document->status === 'approved') {
            return false;
        }

        return $this->canUpdateModule($user, 'documents');
    }

    public function delete(User $user, Document $document): bool
    {
        if (! $document->canBeDeleted()) {
            return false;
        }

        return $this->canDeleteModule($user, 'documents');
    }

    public function approve(User $user, Document $document): bool
    {
        return ($user->hasPermission('documents.approve') || $this->canManageModule($user, 'documents'))
            && $document->type === 'technical_report';
    }

    public function submit(User $user, Document $document): bool
    {
        if ($document->status !== 'draft') {
            return false;
        }

        return $user->hasPermission('documents.submit') || $this->canManageModule($user, 'documents');
    }

    public function duplicate(User $user, Document $document): bool
    {
        return $this->canCreateModule($user, 'documents');
    }
}
