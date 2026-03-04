@extends('layouts.dashboard')

@section('title', __('Employees') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Employees'))

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
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Employees') }}</h1>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('admin.users.roles-permissions.index') }}" class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg transition-all duration-200 text-sm md:text-base border border-purple-500/30">
            <i class="fas fa-shield-halved {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Roles & Permissions') }}
        </a>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-user-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Add Employee') }}
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Employees') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $totalUsers }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Active') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">{{ $activeUsers }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Suspended') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-400 mt-1 md:mt-2">{{ $suspendedUsers }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-slash text-gray-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Recent Logins') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-primary-400 mt-1 md:mt-2" style="color: #4787a7 !important;">{{ $recentLogins }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock-rotate-left text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="userFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="{{ __('Search: name, email, phone, ID...') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="suspended">{{ __('Suspended') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Role') }}</label>
                <select x-model="role" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Roles') }}</option>
                    <option value="super_admin">{{ __('Super Admin') }}</option>
                    <option value="project_manager">{{ __('Project Manager') }}</option>
                    <option value="engineer">{{ __('Engineer/Technician') }}</option>
                    <option value="admin_staff">{{ __('Admin Staff') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Job Title/Department') }}</label>
                <input 
                    type="text" 
                    name="job_title"
                    value="{{ request('job_title') }}"
                    placeholder="{{ __('Example: Architectural Engineer') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('From Date') }}</label>
                <input 
                    type="date" 
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Additional Date Filter -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('To Date') }}</label>
                <input 
                    type="date" 
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Apply Filters') }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base text-center">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </div>
    </div>
</form>

<!-- Users Table -->
@php
    use Illuminate\Support\Facades\Storage;
    $usersJson = $users->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'nationalId' => $user->national_id ?? '',
            'jobTitle' => $user->job_title ?? '',
            'roles' => $user->roles->pluck('display_name')->toArray(),
            'status' => $user->status,
            'lastLogin' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : null,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ];
    })->toJson();
@endphp
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="usersData({{ $usersJson }})">
    <!-- Bulk Actions -->
    <div x-show="selectedUsers.length > 0" class="mb-4 p-3 bg-primary-400/20 rounded-lg flex items-center justify-between">
        <span class="text-white text-sm" x-text="selectedUsers.length + ' {{ __('employees selected') }}'"></span>
        <div class="flex items-center gap-2">
            <button @click="bulkActivate()" class="px-3 py-1 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded text-sm">
                <i class="fas fa-toggle-on {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Activate') }}
            </button>
            <button @click="bulkSuspend()" class="px-3 py-1 bg-gray-500/20 hover:bg-gray-500/30 text-gray-400 rounded text-sm">
                <i class="fas fa-toggle-off {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Suspend') }}
            </button>
            <button @click="bulkAssignRole()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-user-shield {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Assign Role') }}
            </button>
            <button @click="bulkDelete()" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Delete') }}
            </button>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="pb-3">
                        <input type="checkbox" @change="toggleAll()" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                    </th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Name') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Email') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Phone') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('National ID') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Job Title/Department') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Roles') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Last Login') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="user in users" :key="user.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3">
                            <input type="checkbox" :value="user.id" x-model="selectedUsers" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        </td>
                        <td class="py-3">
                            <div class="flex items-center gap-3">
                                <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=1db8f8&color=fff&size=40'" 
                                     :alt="user.name" 
                                     class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="text-white text-sm font-semibold" x-text="user.name"></p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.email || '-'"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.phone || '-'"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.nationalId || '-'"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.jobTitle || '-'"></td>
                        <td class="py-3">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="role in user.roles" :key="role">
                                    <span class="px-2 py-0.5 bg-primary-400/20 text-primary-400 rounded text-xs font-semibold" x-text="role"></span>
                                </template>
                            </div>
                        </td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="user.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                                x-text="user.status === 'active' ? '{{ __('Active') }}' : '{{ __('Suspended') }}'"
                            ></span>
                        </td>
                            <td class="py-3 text-gray-300 text-sm" x-text="user.lastLogin || '{{ __('Never logged in') }}'"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/admin/users/' + user.id" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/admin/users/' + user.id + '/edit'" class="text-blue-400 hover:text-blue-300" :title="'{{ __('Edit') }}'">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="openRolesModal(user.id)" class="text-purple-400 hover:text-purple-300" :title="'{{ __('Roles & Permissions') }}'">
                                    <i class="fas fa-user-shield"></i>
                                </button>
                                <button @click="openResetPasswordModal(user.id)" class="text-yellow-400 hover:text-yellow-300" :title="'{{ __('Reset Password') }}'">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button @click="toggleUser(user.id)" class="text-green-400 hover:text-green-300" :title="user.status === 'active' ? '{{ __('Suspend') }}' : '{{ __('Activate') }}'">
                                    <i :class="user.status === 'active' ? 'fas fa-toggle-on' : 'fas fa-toggle-off'"></i>
                                </button>
                                <button @click="deleteUser(user.id)" class="text-red-400 hover:text-red-300" :title="'{{ __('Delete') }}'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <template x-for="user in users" :key="'mobile-user-' + user.id">
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=1db8f8&color=fff&size=48'" 
                             :alt="user.name" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <h3 class="text-white font-semibold mb-1" x-text="user.name"></h3>
                            <p class="text-gray-400 text-sm" x-text="user.jobTitle"></p>
                        </div>
                    </div>
                    <input type="checkbox" :value="user.id" x-model="selectedUsers" class="rounded border-white/20 bg-white/5 text-primary-400">
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Email') }}</span>
                        <span class="text-white text-sm" x-text="user.email"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Phone') }}</span>
                        <span class="text-white text-sm" x-text="user.phone"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Roles') }}</span>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="role in user.roles" :key="role">
                                <span class="px-2 py-0.5 bg-primary-400/20 text-primary-400 rounded text-xs" x-text="role"></span>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Status') }}</span>
                        <span 
                            class="px-2 py-1 rounded text-xs font-semibold"
                            :class="user.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                            x-text="user.status === 'active' ? '{{ __('Active') }}' : '{{ __('Suspended') }}'"
                        ></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <a :href="'/admin/users/' + user.id" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a :href="'/admin/users/' + user.id + '/edit'" class="text-blue-400 hover:text-blue-300">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button @click="openRolesModal(user.id)" class="text-purple-400 hover:text-purple-300">
                            <i class="fas fa-user-shield"></i>
                        </button>
                    </div>
                    <button @click="toggleUser(user.id)" :class="user.status === 'active' ? 'text-green-400 hover:text-green-300' : 'text-gray-400 hover:text-gray-300'">
                        <i :class="user.status === 'active' ? 'fas fa-toggle-on' : 'fas fa-toggle-off'"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Modals -->
@include('components.modals.user-roles')
@include('components.modals.user-reset-password')

@push('scripts')
<script>
function userFilters() {
    return {
        search: '',
        status: '',
        role: '',
        jobTitle: '',
        period: '',
        dateFrom: '',
        dateTo: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.status = '';
            this.role = '';
            this.jobTitle = '';
            this.period = '';
            this.dateFrom = '';
            this.dateTo = '';
        }
    }
}

function usersData(initialUsers) {
    return {
        selectedUsers: [],
        users: initialUsers || [],
        toggleAll() {
            // TODO: Implement toggle all
        },
        openRolesModal(id) {
            window.dispatchEvent(new CustomEvent('open-user-roles-modal', { detail: { userId: id } }));
        },
        openResetPasswordModal(id) {
            window.dispatchEvent(new CustomEvent('open-user-reset-password-modal', { detail: { userId: id } }));
        },
        toggleUser(id) {
            if (confirm('{{ __('Are you sure you want to change this employee\'s status?') }}')) {
                console.log('Toggling user:', id);
            }
        },
        deleteUser(id) {
            if (confirm('{{ __('Are you sure you want to delete this employee?') }}')) {
                console.log('Deleting user:', id);
            }
        },
        bulkActivate() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('{{ __('Are you sure you want to activate') }} ' + this.selectedUsers.length + ' {{ __('employees?') }}')) {
                console.log('Bulk activate:', this.selectedUsers);
            }
        },
        bulkSuspend() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('{{ __('Are you sure you want to suspend') }} ' + this.selectedUsers.length + ' {{ __('employees?') }}')) {
                console.log('Bulk suspend:', this.selectedUsers);
            }
        },
        bulkAssignRole() {
            if (this.selectedUsers.length === 0) return;
            alert('{{ __('Assign Role') }}');
        },
        bulkDelete() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('{{ __('Are you sure you want to delete') }} ' + this.selectedUsers.length + ' {{ __('employees?') }}')) {
                console.log('Bulk delete:', this.selectedUsers);
            }
        }
    }
}
</script>
@endpush

@endsection


