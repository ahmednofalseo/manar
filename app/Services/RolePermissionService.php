<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    /**
     * @param  array{name: string, display_name: string, description?: string|null}  $attributes
     * @param  list<int>  $permissionIds
     */
    public function createRole(array $attributes, array $permissionIds = []): Role
    {
        return DB::transaction(function () use ($attributes, $permissionIds) {
            $role = Role::create($attributes);
            $this->syncPermissions($role, $permissionIds);

            return $role->load('permissions');
        });
    }

    /**
     * @param  array{name?: string, display_name?: string, description?: string|null}  $attributes
     * @param  list<int>|null  $permissionIds  null = do not change permissions
     */
    public function updateRole(Role $role, array $attributes, ?array $permissionIds = null): Role
    {
        return DB::transaction(function () use ($role, $attributes, $permissionIds) {
            $role->update($attributes);

            if ($permissionIds !== null) {
                $this->syncPermissions($role, $permissionIds);
            }

            return $role->load('permissions');
        });
    }

    /**
     * @param  list<int|string>  $permissionIds
     */
    public function syncPermissions(Role $role, array $permissionIds): void
    {
        if ($role->name === 'super_admin') {
            $permissionIds = Permission::pluck('id')->all();
        }

        $ids = collect($permissionIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $role->permissions()->sync($ids);
    }

    /**
     * @return list<int>
     */
    public function normalizePermissionIds(?array $permissionIds): array
    {
        if ($permissionIds === null) {
            return [];
        }

        return collect($permissionIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }
}
