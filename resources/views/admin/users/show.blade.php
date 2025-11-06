@extends('layouts.dashboard')

@section('title', 'ملف الموظف - المنار')
@section('page-title', 'ملف الموظف')

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

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">محمد أحمد</h1>
        <p class="text-gray-400 text-sm">مهندس معماري</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.edit', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen ml-2"></i>
            تعديل
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-start gap-6">
        <div class="w-24 h-24 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
            <img src="https://ui-avatars.com/api/?name=محمد أحمد&background=1db8f8&color=fff&size=96" alt="محمد أحمد" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">البريد الإلكتروني</p>
                <p class="text-white font-semibold">mohamed@manar.com</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الجوال</p>
                <p class="text-white font-semibold">0501234567</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الهوية</p>
                <p class="text-white font-semibold">1234567890</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">الأدوار</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    <span class="px-3 py-1 bg-primary-500/20 text-primary-400 rounded-lg text-sm font-semibold">مدير المشروع</span>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-semibold">مهندس</span>
                </div>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">الحالة</p>
                <span class="inline-block bg-green-500/20 text-green-400 px-3 py-1 rounded-lg text-sm font-semibold">نشط</span>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">آخر تسجيل دخول</p>
                <p class="text-white font-semibold">2025-11-05 10:30 ص</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم مزاولة المهنة</p>
                <p class="text-white font-semibold">LIC-2024-001</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">تاريخ انتهاء التصنيف</p>
                <p class="text-white font-semibold">2026-12-31</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">تاريخ الإنشاء</p>
                <p class="text-white font-semibold">2025-01-15</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="userTabs()">
    <!-- Tab Headers -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-white/10">
        <button 
            @click="activeTab = 'tasks'"
            :class="activeTab === 'tasks' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-tasks ml-2"></i>
            المهام المسندة
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">12</span>
        </button>
        <button 
            @click="activeTab = 'activity'"
            :class="activeTab === 'activity' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-clock-rotate-left ml-2"></i>
            النشاط
        </button>
        <button 
            @click="activeTab = 'attachments'"
            :class="activeTab === 'attachments' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-paperclip ml-2"></i>
            المرفقات
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">3</span>
        </button>
    </div>

    <!-- Tab Content -->
    <!-- Tasks Tab -->
    <div x-show="activeTab === 'tasks'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">المهام المسندة</h3>
            <div class="flex items-center gap-2">
                <select class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm">
                    <option>جميع الحالات</option>
                    <option>جديد</option>
                    <option>قيد التنفيذ</option>
                    <option>منجز</option>
                </select>
                <a href="{{ route('tasks.create') }}" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                    <i class="fas fa-plus ml-1"></i>
                    مهمة جديدة
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-sm font-normal pb-3">اسم المهمة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">المشروع</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">المرحلة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">تاريخ الاستحقاق</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold">مراجعة المخططات المعمارية</td>
                        <td class="py-3 text-gray-300 text-sm">مشروع فيلا رقم 1</td>
                        <td class="py-3 text-gray-300 text-sm">معماري</td>
                        <td class="py-3">
                            <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs font-semibold">قيد التنفيذ</span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">2025-11-15</td>
                        <td class="py-3">
                            <a href="/tasks/1" class="text-primary-400 hover:text-primary-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold">إعداد تقرير التقدم</td>
                        <td class="py-3 text-gray-300 text-sm">مشروع مجمع سكني</td>
                        <td class="py-3 text-gray-300 text-sm">إنشائي</td>
                        <td class="py-3">
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-semibold">منجز</span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">2025-11-10</td>
                        <td class="py-3">
                            <a href="/tasks/2" class="text-primary-400 hover:text-primary-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity Tab -->
    <div x-show="activeTab === 'activity'" class="space-y-4">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        <div class="space-y-3">
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sign-in-alt text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تسجيل دخول</p>
                    <p class="text-gray-400 text-xs">2025-11-05 10:30 ص</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-shield text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم تحديث الأدوار</p>
                    <p class="text-gray-400 text-xs">بواسطة: الأدمن</p>
                    <p class="text-gray-400 text-xs">2025-11-01 14:20 م</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم إنشاء الحساب</p>
                    <p class="text-gray-400 text-xs">بواسطة: الأدمن</p>
                    <p class="text-gray-400 text-xs">2025-01-15 08:00 ص</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments Tab -->
    <div x-show="activeTab === 'attachments'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">المرفقات</h3>
            <button class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                رفع ملف
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-pdf text-red-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">شهادة مزاولة المهنة.pdf</p>
                            <p class="text-gray-400 text-xs">2.5 MB</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="#" class="flex-1 px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm text-center">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </a>
                    <a href="#" class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded text-sm text-center">
                        <i class="fas fa-download ml-1"></i>
                        تحميل
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function userTabs() {
    return {
        activeTab: 'tasks'
    }
}
</script>
@endpush

@endsection


