<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RolesAndPermissionsController extends Controller
{
    public function __construct(
        private readonly RolePermissionService $rolePermissionService
    ) {}

    /**
     * Display roles and permissions management page.
     */
    public function index()
    {
        Gate::authorize('manage-roles-permissions');

        $roles = Role::with('permissions')->orderBy('display_name')->get();
        $permissions = Permission::orderBy('group')->orderBy('display_name')->get()->groupBy('group');

        $rolesArray = $roles->map(fn (Role $role) => $this->roleToArray($role))->values()->all();

        $permissionsArray = $permissions->mapWithKeys(function ($groupPermissions, $group) {
            return [$group => $groupPermissions->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'group' => $permission->group,
            ])->values()->all()];
        })->all();

        return view('admin.roles-permissions.index', compact('roles', 'permissions', 'rolesArray', 'permissionsArray'));
    }

    /**
     * Store a new role with permissions.
     */
    public function storeRole(StoreRoleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $permissionIds = $this->rolePermissionService->normalizePermissionIds($validated['permissions'] ?? []);

        $role = $this->rolePermissionService->createRole([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ], $permissionIds);

        return response()->json([
            'success' => true,
            'message' => __('Role created successfully'),
            'role' => $this->roleToArray($role),
        ]);
    }

    /**
     * Update a role and its permissions.
     */
    public function updateRole(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super_admin' && $request->input('name') !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => __('Cannot rename super admin role'),
            ], 422);
        }

        $validated = $request->validated();
        $permissionIds = array_key_exists('permissions', $validated)
            ? $this->rolePermissionService->normalizePermissionIds($validated['permissions'] ?? [])
            : null;

        $attributes = [
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ];

        if ($role->name !== 'super_admin' && isset($validated['name'])) {
            $attributes['name'] = $validated['name'];
        }

        $role = $this->rolePermissionService->updateRole($role, $attributes, $permissionIds);

        return response()->json([
            'success' => true,
            'message' => __('Role updated successfully'),
            'role' => $this->roleToArray($role),
        ]);
    }

    /**
     * Delete a role.
     */
    public function deleteRole(int $id): JsonResponse
    {
        Gate::authorize('manage-roles-permissions');

        $role = Role::findOrFail($id);

        if ($role->name === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete super admin role'),
            ], 403);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('Cannot delete role assigned to users'),
            ], 422);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => __('Role deleted successfully'),
        ]);
    }

    /**
     * Store a new permission.
     */
    public function storePermission(Request $request): JsonResponse
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
            'permission' => $permission,
        ]);
    }

    /**
     * Update a permission.
     */
    public function updatePermission(Request $request, int $id): JsonResponse
    {
        Gate::authorize('manage-roles-permissions');

        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,'.$id,
            'display_name' => 'required|string',
            'group' => 'nullable|string',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Permission updated successfully'),
            'permission' => $permission,
        ]);
    }

    /**
     * Delete a permission.
     */
    public function deletePermission(int $id): JsonResponse
    {
        Gate::authorize('manage-roles-permissions');

        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => __('Permission deleted successfully'),
        ]);
    }

    /**
     * Update role permissions only (API for backward compatibility).
     */
    public function updateRolePermissions(Request $request, int $id): JsonResponse
    {
        Gate::authorize('manage-roles-permissions');

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $permissionIds = $this->rolePermissionService->normalizePermissionIds($validated['permissions'] ?? []);
        $this->rolePermissionService->syncPermissions($role, $permissionIds);

        return response()->json([
            'success' => true,
            'message' => __('Role permissions updated successfully'),
            'role' => $this->roleToArray($role->load('permissions')),
        ]);
    }

    /**
     * @return array{id: int, name: string, display_name: string, description: ?string, permissions: list<int>}
     */
    private function roleToArray(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'permissions' => $role->permissions->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
        ];
    }
}
