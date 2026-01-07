<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RolesAndPermissionsController extends Controller
{
    /**
     * Display roles and permissions management page.
     */
    public function index()
    {
        Gate::authorize('manage-roles-permissions');
        
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');
        
        // Convert roles to array format for JavaScript
        $rolesArray = $roles->map(function($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('id')->toArray()
            ];
        })->toArray();
        
        // Convert permissions to array format for JavaScript
        $permissionsArray = $permissions->mapWithKeys(function($groupPermissions, $group) {
            return [$group => $groupPermissions->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'group' => $permission->group
                ];
            })->values()->toArray()];
        })->toArray();
        
        return view('admin.roles-permissions.index', compact('roles', 'permissions', 'rolesArray', 'permissionsArray'));
    }

    /**
     * Store a new role.
     */
    public function storeRole(Request $request)
    {
        Gate::authorize('manage-roles-permissions');
        
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Role created successfully'),
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description
            ]
        ]);
    }

    /**
     * Update a role.
     */
    public function updateRole(Request $request, $id)
    {
        Gate::authorize('manage-roles-permissions');
        
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'display_name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Role updated successfully'),
            'role' => $role->load('permissions')
        ]);
    }

    /**
     * Delete a role.
     */
    public function deleteRole($id)
    {
        Gate::authorize('manage-roles-permissions');
        
        $role = Role::findOrFail($id);
        
        // Prevent deletion of super_admin role
        if ($role->name === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete super admin role')
            ], 403);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => __('Role deleted successfully')
        ]);
    }

    /**
     * Store a new permission.
     */
    public function storePermission(Request $request)
    {
        Gate::authorize('manage-roles-permissions');
        
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'required|string',
            'group' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Permission created successfully'),
            'permission' => $permission
        ]);
    }

    /**
     * Update a permission.
     */
    public function updatePermission(Request $request, $id)
    {
        Gate::authorize('manage-roles-permissions');
        
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'display_name' => 'required|string',
            'group' => 'nullable|string',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Permission updated successfully'),
            'permission' => $permission
        ]);
    }

    /**
     * Delete a permission.
     */
    public function deletePermission($id)
    {
        Gate::authorize('manage-roles-permissions');
        
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => __('Permission deleted successfully')
        ]);
    }

    /**
     * Update role permissions.
     */
    public function updateRolePermissions(Request $request, $id)
    {
        Gate::authorize('manage-roles-permissions');
        
        $role = Role::findOrFail($id);
        
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => __('Role permissions updated successfully'),
            'role' => $role->load('permissions')
        ]);
    }
}

