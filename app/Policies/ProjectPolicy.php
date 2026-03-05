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

        // صلاحية عرض المشاريع تسمح بعرض أي مشروع
        if (PermissionHelper::hasPermission('projects.view') || 
            PermissionHelper::hasPermission('projects.manage')) {
            return true;
        }

        // أو إذا كان مدير المشروع أو عضو في الفريق
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

        // صلاحية تعديل المشاريع تسمح بتعديل أي مشروع
        if (PermissionHelper::hasPermission('projects.edit') || 
            PermissionHelper::hasPermission('projects.manage')) {
            return true;
        }

        // أو إذا كان مدير المشروع أو عضو في الفريق
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

        // صلاحية إدارة كاملة تسمح بحذف أي مشروع
        if (PermissionHelper::hasPermission('projects.manage')) {
            return true;
        }

        // صلاحية الحذف + مدير المشروع
        if (PermissionHelper::hasPermission('projects.delete')) {
            return $project->project_manager_id == $user->id;
        }

        return false;
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




