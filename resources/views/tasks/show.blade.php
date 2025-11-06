@extends('layouts.dashboard')

@section('title', 'تفاصيل المهمة - المنار')
@section('page-title', 'تفاصيل المهمة')

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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">مراجعة المخططات المعمارية</h1>
        <p class="text-gray-400 text-sm">تاريخ الإنشاء: 2025-11-01</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openStatusModal()" class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-check-circle ml-2"></i>
            تعليم كمكتملة
        </button>
        <button onclick="openStatusModal('reject')" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-xmark ml-2"></i>
            رفض
        </button>
        <a href="{{ route('tasks.edit', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen ml-2"></i>
            تعديل
        </a>
        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div>
            <p class="text-gray-400 text-sm mb-2">المشروع</p>
            <p class="text-white font-semibold">مشروع فيلا رقم 1</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المهندس المكلف</p>
            <p class="text-white font-semibold">محمد أحمد</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المرحلة</p>
            <span class="inline-block bg-primary-500/20 text-primary-400 px-3 py-1 rounded-lg text-sm font-semibold">معماري</span>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">الحالة</p>
            <span class="inline-block bg-primary-500/20 text-primary-400 px-3 py-1 rounded-lg text-sm font-semibold">قيد التنفيذ</span>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">الأولوية</p>
            <span class="inline-block bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-lg text-sm font-semibold">متوسطة</span>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">تاريخ البدء</p>
            <p class="text-white font-semibold">2025-11-01</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">تاريخ الانتهاء</p>
            <p class="text-white font-semibold">2025-11-15</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">نسبة التقدم</p>
            <div class="flex items-center gap-2">
                <div class="flex-1 bg-white/5 rounded-full h-2">
                    <div class="bg-primary-400 h-2 rounded-full" style="width: 45%"></div>
                </div>
                <span class="text-white font-semibold text-sm">45%</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Description -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-4">وصف المهمة</h2>
        <p class="text-gray-300 leading-relaxed">
            مراجعة شاملة للمخططات المعمارية للمشروع والتأكد من مطابقتها للمواصفات والأنظمة المعمول بها.
            يجب مراجعة جميع التفاصيل والتأكد من صحة القياسات والمواصفات.
        </p>
        <div class="mt-4 pt-4 border-t border-white/10">
            <h3 class="text-lg font-bold text-white mb-2">ملاحظات مدير المشروع</h3>
            <p class="text-gray-300 text-sm">يرجى إعطاء الأولوية لهذه المهمة والإكمال قبل الموعد المحدد.</p>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        <div class="space-y-3">
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-edit text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم تحديث الحالة إلى "قيد التنفيذ"</p>
                    <p class="text-gray-400 text-xs">بواسطة: محمد أحمد</p>
                    <p class="text-gray-400 text-xs">2025-11-03 10:30 ص</p>
                </div>
            </div>
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
                    <p class="text-gray-400 text-xs">2025-11-02 14:20 م</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم إنشاء المهمة</p>
                    <p class="text-gray-400 text-xs">بواسطة: مدير المشروع</p>
                    <p class="text-gray-400 text-xs">2025-11-01 09:00 ص</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">الملاحظات</h2>
        <button onclick="openCommentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-comment-dots ml-2"></i>
            إضافة ملاحظة
        </button>
    </div>
    <div class="space-y-4">
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-white font-semibold">فاطمة سالم</p>
                    <p class="text-gray-400 text-xs">2025-11-02 14:20 م</p>
                </div>
            </div>
            <p class="text-gray-300 text-sm mt-2">تمت المراجعة الأولية للمخططات، يلزم إجراء بعض التعديلات البسيطة.</p>
        </div>
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-white font-semibold">محمد أحمد</p>
                    <p class="text-gray-400 text-xs">2025-11-03 10:30 ص</p>
                </div>
            </div>
            <p class="text-gray-300 text-sm mt-2">تم البدء في تنفيذ التعديلات المطلوبة.</p>
        </div>
    </div>
</div>

<!-- Attachments -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-pdf text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">المخططات المعمارية.pdf</p>
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

<!-- Status Update Modal -->
@include('components.modals.task-update')

@push('scripts')
<script>
function openStatusModal(action = 'approve') {
    window.dispatchEvent(new CustomEvent('open-task-status-modal', { detail: { taskId: '{{ $id }}', action: action } }));
}

function openCommentModal() {
    alert('فتح نافذة إضافة ملاحظة');
}
</script>
@endpush

@endsection


