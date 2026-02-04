<?php

namespace App\Policies;

use App\Models\WorkflowTemplate;
use App\Models\User;

class WorkflowTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('workflow-templates.view') || 
               $user->hasPermission('workflow-templates.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasPermission('workflow-templates.view') || 
               $user->hasPermission('workflow-templates.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('workflow-templates.create') || 
               $user->hasPermission('workflow-templates.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasPermission('workflow-templates.edit') || 
               $user->hasPermission('workflow-templates.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkflowTemplate $workflowTemplate): bool
    {
        return $user->hasPermission('workflow-templates.delete') || 
               $user->hasPermission('workflow-templates.manage') ||
               $user->hasRole('super_admin');
    }
}
