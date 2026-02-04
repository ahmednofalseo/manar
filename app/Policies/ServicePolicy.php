<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('services.view') || 
               $user->hasPermission('services.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        return $user->hasPermission('services.view') || 
               $user->hasPermission('services.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('services.create') || 
               $user->hasPermission('services.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->hasPermission('services.edit') || 
               $user->hasPermission('services.manage') ||
               $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermission('services.delete') || 
               $user->hasPermission('services.manage') ||
               $user->hasRole('super_admin');
    }
}
