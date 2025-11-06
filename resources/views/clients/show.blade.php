@extends('layouts.dashboard')

@section('title', 'تفاصيل العميل - المنار')
@section('page-title', 'تفاصيل العميل')

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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">أحمد محمد العلي</h1>
        <p class="text-gray-400 text-sm">تاريخ الإنشاء: 2025-01-15</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('clients.edit', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen ml-2"></i>
            تعديل
        </a>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-start gap-6">
        <div class="w-20 h-20 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-circle text-primary-400 text-4xl"></i>
        </div>
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">نوع العميل</p>
                <p class="text-white font-semibold">فرد</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الجوال</p>
                <p class="text-white font-semibold">0501234567</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">البريد الإلكتروني</p>
                <p class="text-white font-semibold">ahmed@example.com</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">المدينة / الحي</p>
                <p class="text-white font-semibold">الرياض / العليا</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الهوية</p>
                <p class="text-white font-semibold">1234567890</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">الحالة</p>
                <span class="inline-block bg-green-500/20 text-green-400 px-3 py-1 rounded-lg text-sm font-semibold">نشط</span>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <p class="text-gray-400 text-sm mb-1">العنوان الكامل</p>
                <p class="text-white">حي العليا، شارع الملك فهد، مبنى رقم 123، الطابق الثاني</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="clientTabs()">
    <!-- Tab Headers -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-white/10">
        <button 
            @click="activeTab = 'projects'"
            :class="activeTab === 'projects' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-clipboard-list ml-2"></i>
            المشاريع المرتبطة
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">5</span>
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
        <button 
            @click="activeTab = 'notes'"
            :class="activeTab === 'notes' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-comment-dots ml-2"></i>
            ملاحظات العميل
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">8</span>
        </button>
        <button 
            @click="activeTab = 'activity'"
            :class="activeTab === 'activity' ? 'bg-primary-500/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-clock-rotate-left ml-2"></i>
            السجل
        </button>
    </div>

    <!-- Tab Content -->
    <!-- Projects Tab -->
    <div x-show="activeTab === 'projects'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">المشاريع المرتبطة</h3>
            <a href="{{ route('projects.create') }}" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                مشروع جديد
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-sm font-normal pb-3">اسم المشروع</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">نوع المشروع</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">تاريخ البدء</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold">مشروع فيلا رقم 1</td>
                        <td class="py-3 text-gray-300 text-sm">تصميم وإشراف</td>
                        <td class="py-3">
                            <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs font-semibold">قيد التنفيذ</span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">2025-01-20</td>
                        <td class="py-3">
                            <a href="/projects/1" class="text-primary-400 hover:text-primary-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold">مشروع مجمع سكني</td>
                        <td class="py-3 text-gray-300 text-sm">تصميم</td>
                        <td class="py-3">
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-semibold">مكتمل</span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">2024-11-15</td>
                        <td class="py-3">
                            <a href="/projects/2" class="text-primary-400 hover:text-primary-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attachments Tab -->
    <div x-show="activeTab === 'attachments'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">المرفقات</h3>
            <button @click="openAttachmentModal()" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
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
                            <p class="text-white font-semibold text-sm">هوية العميل.pdf</p>
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
                    <button class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Tab -->
    <div x-show="activeTab === 'notes'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">ملاحظات العميل</h3>
            <button @click="openNotesModal()" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                إضافة ملاحظة
            </button>
        </div>
        <div class="space-y-3">
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-white font-semibold">فاطمة سالم</p>
                        <p class="text-gray-400 text-xs">2025-11-05 10:30 ص</p>
                    </div>
                </div>
                <p class="text-gray-300 text-sm mt-2">العميل يطلب تحديثات على التصميم المعماري للمشروع.</p>
            </div>
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-white font-semibold">محمد أحمد</p>
                        <p class="text-gray-400 text-xs">2025-11-03 14:20 م</p>
                    </div>
                </div>
                <p class="text-gray-300 text-sm mt-2">تم التواصل مع العميل وتم الاتفاق على موعد المراجعة النهائية.</p>
            </div>
        </div>
    </div>

    <!-- Activity Tab -->
    <div x-show="activeTab === 'activity'" class="space-y-4">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        <div class="space-y-3">
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-comment text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم إضافة ملاحظة</p>
                    <p class="text-gray-400 text-xs">بواسطة: فاطمة سالم</p>
                    <p class="text-gray-400 text-xs">2025-11-05 10:30 ص</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-link text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم ربط مشروع جديد</p>
                    <p class="text-gray-400 text-xs">مشروع: مشروع فيلا رقم 1</p>
                    <p class="text-gray-400 text-xs">2025-01-20 09:15 ص</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم إنشاء العميل</p>
                    <p class="text-gray-400 text-xs">بواسطة: مدير النظام</p>
                    <p class="text-gray-400 text-xs">2025-01-15 08:00 ص</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('components.modals.client-attachment')
@include('components.modals.client-notes')

@push('scripts')
<script>
function clientTabs() {
    return {
        activeTab: 'projects',
        openAttachmentModal() {
            window.dispatchEvent(new CustomEvent('open-client-attachment-modal', { detail: { clientId: '{{ $id }}' } }));
        },
        openNotesModal() {
            window.dispatchEvent(new CustomEvent('open-client-notes-modal', { detail: { clientId: '{{ $id }}' } }));
        }
    }
}
</script>
@endpush

@endsection


