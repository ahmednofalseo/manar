@extends('layouts.dashboard')

@section('title', 'الموظفون - المنار')
@section('page-title', 'الموظفون')

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">الموظفون</h1>
    <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-file-import ml-2"></i>
            استيراد CSV
        </button>
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-file-export ml-2"></i>
            تصدير
        </button>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-user-plus ml-2"></i>
            إضافة موظف
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي الموظفين</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">45</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">النشطون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">38</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المعلّقون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-400 mt-1 md:mt-2">7</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-slash text-gray-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">آخر تسجيل دخول</p>
                <h3 class="text-2xl md:text-3xl font-bold text-primary-400 mt-1 md:mt-2">32</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
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
                placeholder="بحث: الاسم، البريد، الهاتف، الهوية..." 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="active">نشط</option>
                    <option value="suspended">معلق</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الدور</label>
                <select x-model="role" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأدوار</option>
                    <option value="super_admin">الأدمن العام</option>
                    <option value="project_manager">مدير المشروع</option>
                    <option value="engineer">مهندس/فني</option>
                    <option value="admin_staff">الإداري</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الوظيفة/القسم</label>
                <select x-model="jobTitle" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الوظائف</option>
                    <option value="مهندس معماري">مهندس معماري</option>
                    <option value="مهندس إنشائي">مهندس إنشائي</option>
                    <option value="مهندس كهرباء">مهندس كهرباء</option>
                    <option value="مهندس ميكانيكي">مهندس ميكانيكي</option>
                    <option value="مدير مشروع">مدير مشروع</option>
                    <option value="إداري">إداري</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الفترة الزمنية</label>
                <select x-model="period" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">كل الفترات</option>
                    <option value="today">اليوم</option>
                    <option value="week">هذا الأسبوع</option>
                    <option value="month">هذا الشهر</option>
                    <option value="custom">مخصص</option>
                </select>
            </div>
        </div>

        <!-- Date Range (when custom selected) -->
        <div x-show="period === 'custom'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">من تاريخ</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">إلى تاريخ</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button @click="applyFilters()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <button @click="clearFilters()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times ml-2"></i>
                تفريغ
            </button>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="usersData()">
    <!-- Bulk Actions -->
    <div x-show="selectedUsers.length > 0" class="mb-4 p-3 bg-primary-500/20 rounded-lg flex items-center justify-between">
        <span class="text-white text-sm" x-text="selectedUsers.length + ' موظف محدد'"></span>
        <div class="flex items-center gap-2">
            <button @click="bulkActivate()" class="px-3 py-1 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded text-sm">
                <i class="fas fa-toggle-on ml-1"></i>
                تفعيل
            </button>
            <button @click="bulkSuspend()" class="px-3 py-1 bg-gray-500/20 hover:bg-gray-500/30 text-gray-400 rounded text-sm">
                <i class="fas fa-toggle-off ml-1"></i>
                تعليق
            </button>
            <button @click="bulkAssignRole()" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-user-shield ml-1"></i>
                إسناد دور
            </button>
            <button @click="bulkDelete()" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash ml-1"></i>
                حذف
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
                    <th class="text-gray-400 text-sm font-normal pb-3">الاسم</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">البريد</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الهاتف</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الهوية</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الوظيفة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الأدوار</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">آخر تسجيل دخول</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
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
                                    <p class="text-gray-400 text-xs" x-text="user.jobTitle"></p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.email"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.phone"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.nationalId"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.jobTitle"></td>
                        <td class="py-3">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="role in user.roles" :key="role">
                                    <span class="px-2 py-0.5 bg-primary-500/20 text-primary-400 rounded text-xs font-semibold" x-text="role"></span>
                                </template>
                            </div>
                        </td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="user.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                                x-text="user.status === 'active' ? 'نشط' : 'معلق'"
                            ></span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="user.lastLogin || 'لم يسجل دخول'"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/admin/users/' + user.id" class="text-primary-400 hover:text-primary-300" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/admin/users/' + user.id + '/edit'" class="text-blue-400 hover:text-blue-300" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="openRolesModal(user.id)" class="text-purple-400 hover:text-purple-300" title="أدوار وصلاحيات">
                                    <i class="fas fa-user-shield"></i>
                                </button>
                                <button @click="openResetPasswordModal(user.id)" class="text-yellow-400 hover:text-yellow-300" title="إعادة ضبط كلمة المرور">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button @click="toggleUser(user.id)" class="text-green-400 hover:text-green-300" :title="user.status === 'active' ? 'تعليق' : 'تفعيل'">
                                    <i :class="user.status === 'active' ? 'fas fa-toggle-on' : 'fas fa-toggle-off'"></i>
                                </button>
                                <button @click="deleteUser(user.id)" class="text-red-400 hover:text-red-300" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <template x-for="user in users" :key="user.id">
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
                        <span class="text-gray-400 text-xs">البريد</span>
                        <span class="text-white text-sm" x-text="user.email"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الهاتف</span>
                        <span class="text-white text-sm" x-text="user.phone"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الأدوار</span>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="role in user.roles" :key="role">
                                <span class="px-2 py-0.5 bg-primary-500/20 text-primary-400 rounded text-xs" x-text="role"></span>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الحالة</span>
                        <span 
                            class="px-2 py-1 rounded text-xs font-semibold"
                            :class="user.status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'"
                            x-text="user.status === 'active' ? 'نشط' : 'معلق'"
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

function usersData() {
    return {
        selectedUsers: [],
        users: [
            {
                id: 1,
                name: 'محمد أحمد',
                email: 'mohamed@manar.com',
                phone: '0501234567',
                nationalId: '1234567890',
                jobTitle: 'مهندس معماري',
                roles: ['مدير المشروع', 'مهندس'],
                status: 'active',
                lastLogin: '2025-11-05 10:30 ص',
                avatar: null
            },
            {
                id: 2,
                name: 'فاطمة سالم',
                email: 'fatima@manar.com',
                phone: '0509876543',
                nationalId: '0987654321',
                jobTitle: 'مهندسة إنشائية',
                roles: ['مهندس'],
                status: 'active',
                lastLogin: '2025-11-04 14:20 م',
                avatar: null
            },
            {
                id: 3,
                name: 'خالد مطر',
                email: 'khaled@manar.com',
                phone: '0551234567',
                nationalId: '1122334455',
                jobTitle: 'إداري',
                roles: ['الإداري'],
                status: 'suspended',
                lastLogin: '2025-10-28 09:15 ص',
                avatar: null
            }
        ],
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
            if (confirm('هل أنت متأكد من تغيير حالة هذا الموظف؟')) {
                console.log('Toggling user:', id);
            }
        },
        deleteUser(id) {
            if (confirm('هل أنت متأكد من حذف هذا الموظف؟')) {
                console.log('Deleting user:', id);
            }
        },
        bulkActivate() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('هل أنت متأكد من تفعيل ' + this.selectedUsers.length + ' موظف؟')) {
                console.log('Bulk activate:', this.selectedUsers);
            }
        },
        bulkSuspend() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('هل أنت متأكد من تعليق ' + this.selectedUsers.length + ' موظف؟')) {
                console.log('Bulk suspend:', this.selectedUsers);
            }
        },
        bulkAssignRole() {
            if (this.selectedUsers.length === 0) return;
            alert('إسناد دور للموظفين المحددين');
        },
        bulkDelete() {
            if (this.selectedUsers.length === 0) return;
            if (confirm('هل أنت متأكد من حذف ' + this.selectedUsers.length + ' موظف؟')) {
                console.log('Bulk delete:', this.selectedUsers);
            }
        }
    }
}
</script>
@endpush

@endsection


