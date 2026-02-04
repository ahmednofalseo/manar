<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use App\Helpers\PermissionHelper;

class ProjectPolicy
{
    /**
     * Determine if the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        return PermissionHelper::hasPermission('projects.view') || 
               PermissionHelper::hasPermission('projects.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // Super admin can view all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check permission
        if (!PermissionHelper::hasPermission('projects.view') && 
            !PermissionHelper::hasPermission('projects.manage')) {
            return false;
        }

        // User can view if they are manager or team member
        return $project->project_manager_id == $user->id ||
               (is_array($project->team_members) && in_array($user->id, $project->team_members));
    }

    /**
     * Determine if the user can create projects.
     */
    public function create(User $user): bool
    {
        return PermissionHelper::hasPermission('projects.create') || 
               PermissionHelper::hasPermission('projects.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // Super admin can update all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check permission
        if (!PermissionHelper::hasPermission('projects.edit') && 
            !PermissionHelper::hasPermission('projects.manage')) {
            return false;
        }

        // User can update if they are manager or team member
        return $project->project_manager_id == $user->id ||
               (is_array($project->team_members) && in_array($user->id, $project->team_members));
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Super admin can delete all
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check permission
        if (!PermissionHelper::hasPermission('projects.delete') && 
            !PermissionHelper::hasPermission('projects.manage')) {
            return false;
        }

        // User can delete if they are manager
        return $project->project_manager_id == $user->id;
    }

    /**
     * Determine if the user can restore the project.
     */
    public function restore(User $user, Project $project): bool
    {
        return $this->delete($user, $project);
    }

    /**
     * Determine if the user can permanently delete the project.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $this->delete($user, $project);
    }
}




