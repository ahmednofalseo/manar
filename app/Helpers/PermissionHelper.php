<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if current user has a specific permission.
     */
    public static function hasPermission($permission)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Super admin has all permissions
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Check if current user has any of the given permissions.
     */
    public static function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if current user has all of the given permissions.
     */
    public static function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }
}

