<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documents.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // لا يمكن تعديل المستندات المعتمدة
        if ($document->type === 'technical_report' && $document->status === 'approved') {
            return false;
        }
        
        return $user->hasPermission('documents.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // لا يمكن حذف المستندات المعتمدة أو المرسلة
        if (!$document->canBeDeleted()) {
            return false;
        }
        
        return $user->hasPermission('documents.delete');
    }

    /**
     * Determine whether the user can approve documents.
     */
    public function approve(User $user, Document $document): bool
    {
        // فقط الأدمن العام يمكنه اعتماد التقارير
        return $user->hasRole('super_admin') && $document->type === 'technical_report';
    }

    /**
     * Determine whether the user can submit documents.
     */
    public function submit(User $user, Document $document): bool
    {
        // يمكن إرسال المستندات في حالة Draft فقط
        if ($document->status !== 'draft') {
            return false;
        }
        
        return $user->hasPermission('documents.submit');
    }

    /**
     * Determine whether the user can duplicate documents.
     */
    public function duplicate(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.create');
    }
}
