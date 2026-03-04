<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin: full access
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // Project Manager & Engineer: view only
        return $user->hasRole('project_manager') || $user->hasRole('engineer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        // Admin: full access
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // Project Manager & Engineer: view only
        return $user->hasRole('project_manager') || $user->hasRole('engineer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin can create
        return $user->hasRole('super_admin') || $user->hasRole('admin_staff');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        // Only Admin can update
        return $user->hasRole('super_admin') || $user->hasRole('admin_staff');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client = null): bool
    {
        // Only super_admin can delete
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        // Only super_admin can delete
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can add notes.
     */
    public function addNote(User $user, Client $client): bool
    {
        // Admin & Project Manager can add notes
        return $user->hasRole('super_admin') 
            || $user->hasRole('admin_staff') 
            || $user->hasRole('project_manager');
    }

    /**
     * Determine whether the user can view attachments.
     */
    public function viewAttachments(User $user, Client $client): bool
    {
        // Admin & Project Manager can view attachments
        return $user->hasRole('super_admin') 
            || $user->hasRole('admin_staff') 
            || $user->hasRole('project_manager');
    }

    /**
     * Determine whether the user can upload attachments.
     */
    public function uploadAttachment(User $user, Client $client): bool
    {
        // Only Admin can upload attachments
        return $user->hasRole('super_admin') || $user->hasRole('admin_staff');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasRole('super_admin');
    }
}
