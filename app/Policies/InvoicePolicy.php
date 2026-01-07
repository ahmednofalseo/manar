<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin/Finance: full access
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // PM: view + create request
        if ($user->hasRole('project_manager')) {
            return true;
        }

        // Engineer: view none
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Admin/Finance: full access
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // PM: view only
        if ($user->hasRole('project_manager')) {
            // يمكنه رؤية فواتير مشاريعه
            if (!$invoice->relationLoaded('project')) {
                $invoice->load('project');
            }
            return $invoice->project->project_manager_id === $user->id;
        }

        // Engineer: view none
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin/Finance: manage
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // PM: create request
        if ($user->hasRole('project_manager')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // Admin/Finance: manage
        if ($user->hasRole('super_admin') || $user->hasRole('admin_staff')) {
            return true;
        }

        // PM: يمكنه تحديث فواتير مشاريعه
        if ($user->hasRole('project_manager')) {
            if (!$invoice->relationLoaded('project')) {
                $invoice->load('project');
            }
            return $invoice->project->project_manager_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Only super_admin can delete
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('super_admin');
    }
}
