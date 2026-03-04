@extends('layouts.dashboard')

@section('title', __('Roles & Permissions') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Roles & Permissions'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<div x-data="rolesPermissionsData()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Roles & Permissions') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ __('Manage roles and assign permissions to them') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Back') }}
            </a>
            <button @click="openRoleModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Add Role') }}
            </button>
        </div>
    </div>

    <!-- Roles List -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Roles') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($roles as $role)
            <div class="p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-all">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-white font-semibold mb-1">{{ $role->display_name }}</h3>
                        <p class="text-gray-400 text-sm mb-2">{{ $role->description ?? '-' }}</p>
                        <p class="text-gray-500 text-xs font-mono">{{ $role->name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="openRoleModal({{ $role->id }})" class="text-blue-400 hover:text-blue-300" title="{{ __('Edit') }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($role->name !== 'super_admin')
                        <button @click="deleteRole({{ $role->id }})" class="text-red-400 hover:text-red-300" title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="pt-3 border-t border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-400 text-xs">{{ __('Permissions') }}:</span>
                        <span class="text-primary-400 text-sm font-semibold">{{ $role->permissions->count() }}</span>
                    </div>
                    @if($role->permissions->count() > 0)
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($role->permissions->take(5) as $permission)
                        <span class="px-2 py-1 bg-primary-500/20 text-primary-400 text-xs rounded">{{ $permission->display_name }}</span>
                        @endforeach
                        @if($role->permissions->count() > 5)
                        <span class="px-2 py-1 bg-gray-500/20 text-gray-400 text-xs rounded">+{{ $role->permissions->count() - 5 }}</span>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-500 text-xs italic">{{ __('No permissions assigned') }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Role Modal (with Permissions Selection) -->
    <div x-show="showRoleModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeRoleModal()">
        <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-3xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            <h3 class="text-xl font-bold text-white mb-6">
                <span x-text="editingRoleId ? '{{ __('Edit Role') }}' : '{{ __('Add Role') }}'"></span>
            </h3>
            
            <form @submit.prevent="saveRole()">
                <!-- Role Information -->
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Role Name') }} (slug) <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            x-model="roleForm.name" 
                            required 
                            :disabled="editingRoleId && editingRoleId !== null"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 disabled:opacity-50 disabled:cursor-not-allowed" 
                            placeholder="e.g., accountant"
                        >
                        <p class="text-gray-500 text-xs mt-1">{{ __('Used in code, cannot be changed after creation') }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Display Name') }} <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            x-model="roleForm.display_name" 
                            required 
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" 
                            placeholder="{{ __('Role Display Name') }}"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Description') }}</label>
                        <textarea 
                            x-model="roleForm.description" 
                            rows="2" 
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="{{ __('Role description') }}"
                        ></textarea>
                    </div>
                </div>

                <!-- Permissions Selection -->
                <div class="mb-6">
                    <label class="block text-gray-300 text-sm mb-4">{{ __('Select Permissions') }}</label>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        @foreach($permissions as $group => $groupPermissions)
                        <div>
                            <h4 class="text-white font-semibold mb-3 text-sm flex items-center gap-2">
                                <i class="fas fa-folder text-primary-400"></i>
                                {{ $group ?? __('Other') }}
                            </h4>
                            <div class="space-y-2 pl-6">
                                @foreach($groupPermissions as $permission)
                                <label class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer transition-all">
                                    <input 
                                        type="checkbox" 
                                        :value="{{ $permission->id }}" 
                                        x-model="roleForm.permissions" 
                                        class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500"
                                    >
                                    <div class="flex-1">
                                        <span class="text-white text-sm block">{{ $permission->display_name }}</span>
                                        <span class="text-gray-500 text-xs font-mono">{{ $permission->name }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                    <button 
                        type="button" 
                        @click="closeRoleModal()" 
                        class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all"
                    >
                        <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function rolesPermissionsData() {
    return {
        showRoleModal: false,
        editingRoleId: null,
        roleForm: {
            name: '',
            display_name: '',
            description: '',
            permissions: []
        },
        roles: @json($rolesArray ?? []),
        permissionsByGroup: @json($permissionsArray ?? []),
        openRoleModal(roleId = null) {
            this.editingRoleId = roleId;
            if (roleId) {
                const role = this.roles.find(r => r.id === roleId);
                if (role) {
                    this.roleForm = {
                        name: role.name,
                        display_name: role.display_name,
                        description: role.description || '',
                        permissions: [...role.permissions]
                    };
                }
            } else {
                this.roleForm = {
                    name: '',
                    display_name: '',
                    description: '',
                    permissions: []
                };
            }
            this.showRoleModal = true;
        },
        closeRoleModal() {
            this.showRoleModal = false;
            this.editingRoleId = null;
            this.roleForm = {
                name: '',
                display_name: '',
                description: '',
                permissions: []
            };
        },
        async saveRole() {
            const url = this.editingRoleId 
                ? `/admin/users/roles/${this.editingRoleId}`
                : '/admin/users/roles';
            const method = this.editingRoleId ? 'PUT' : 'POST';
            
            try {
                // First save role info
                const roleResponse = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: this.roleForm.name,
                        display_name: this.roleForm.display_name,
                        description: this.roleForm.description
                    })
                });
                
                const roleData = await roleResponse.json();
                if (!roleData.success) {
                    this.showToast('error', roleData.message || '{{ __('An error occurred') }}');
                    return;
                }

                // Then save permissions
                const roleId = this.editingRoleId || roleData.role.id;
                const permissionsResponse = await fetch(`/admin/users/roles/${roleId}/permissions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ permissions: this.roleForm.permissions })
                });
                
                const permissionsData = await permissionsResponse.json();
                if (permissionsData.success) {
                    this.showToast('success', '{{ __('Role saved successfully') }}');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showToast('error', permissionsData.message || '{{ __('An error occurred') }}');
                }
            } catch (error) {
                this.showToast('error', '{{ __('An error occurred') }}');
            }
        },
        async deleteRole(roleId) {
            if (!confirm('{{ __('Are you sure you want to delete this role?') }}')) return;
            
            try {
                const response = await fetch(`/admin/users/roles/${roleId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.showToast('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showToast('error', data.message || '{{ __('An error occurred') }}');
                }
            } catch (error) {
                this.showToast('error', '{{ __('An error occurred') }}');
            }
        },
        showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 ${'{{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }}'} z-50 p-4 rounded-lg shadow-lg max-w-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    }
}
</script>
@endpush

@endsection
