@extends('layouts.dashboard')

@section('title', 'تفاصيل المصروف - المنار')
@section('page-title', 'تفاصيل المصروف')

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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">مصروف #EXP-2025-001</h1>
        <p class="text-gray-400 text-sm">تاريخ الإضافة: 2025-11-01</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('expenses.edit', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-edit ml-2"></i>
            تعديل
        </a>
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-2">حالة المصروف</p>
                <span class="inline-block bg-green-500/20 text-green-400 px-4 py-2 rounded-lg text-sm font-semibold">معتمد</span>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المبلغ</p>
                <p class="text-2xl font-bold text-white">2,500 <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openApprovalModal('approve')" class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg text-sm">
                <i class="fas fa-circle-check ml-2"></i>
                اعتماد
            </button>
            <button onclick="openApprovalModal('reject')" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm">
                <i class="fas fa-ban ml-2"></i>
                رفض
            </button>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-white/10">
        <p class="text-gray-400 text-sm">تم الاعتماد بواسطة: <span class="text-white">محمد أحمد</span></p>
        <p class="text-gray-400 text-sm">تاريخ الاعتماد: <span class="text-white">2025-11-02 10:30 ص</span></p>
    </div>
</div>

<!-- Main Information -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Expense Details -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-6">معلومات المصروف</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم السند</p>
                <p class="text-white font-semibold">EXP-2025-001</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">التاريخ</p>
                <p class="text-white font-semibold">2025-11-01</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">القسم</p>
                <p class="text-white font-semibold">إدارة</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">نوع المصروف</p>
                <p class="text-white font-semibold">كهرباء</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">طريقة الدفع</p>
                <p class="text-white font-semibold">تحويل بنكي</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">المبلغ</p>
                <p class="text-white font-semibold text-lg">2,500 ر.س</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">الوصف</p>
                <p class="text-white">فاتورة كهرباء المكتب للشهر الحالي</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">ملاحظات إضافية</p>
                <p class="text-white">تم الدفع عبر التحويل البنكي - رقم المرجع: TRX-2025-001</p>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        <div class="space-y-3">
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم الاعتماد</p>
                    <p class="text-gray-400 text-xs">بواسطة: محمد أحمد</p>
                    <p class="text-gray-400 text-xs">2025-11-02 10:30 ص</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم الإضافة</p>
                    <p class="text-gray-400 text-xs">بواسطة: فاطمة سالم</p>
                    <p class="text-gray-400 text-xs">2025-11-01 09:15 ص</p>
                </div>
            </div>
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
                        <p class="text-white font-semibold text-sm">فاتورة الكهرباء.pdf</p>
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
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-image text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">إيصال الدفع.jpg</p>
                        <p class="text-gray-400 text-xs">1.2 MB</p>
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

<!-- Approval Modal -->
@include('components.modals.expense-approval')

@push('scripts')
<script>
function openApprovalModal(action) {
    window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: '{{ $id }}', action: action } }));
}
</script>
@endpush

@endsection


