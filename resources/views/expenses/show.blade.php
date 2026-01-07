@extends('layouts.dashboard')

@section('title', __('Details') . ' - ' . __('Expenses') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Details'))

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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">مصروف #{{ $expense->voucher_number }}</h1>
        <p class="text-gray-400 text-sm">تاريخ الإضافة: {{ $expense->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="flex items-center gap-3">
        @can('update', $expense)
        <a href="{{ route('expenses.edit', $expense->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-edit ml-2"></i>
            {{ __('Edit') }}
        </a>
        @endcan
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Status Badge -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-2">حالة المصروف</p>
                <span class="inline-block px-4 py-2 rounded-lg text-sm font-semibold
                    @if($expense->status->value === 'approved') bg-green-500/20 text-green-400
                    @elseif($expense->status->value === 'rejected') bg-red-500/20 text-red-400
                    @elseif($expense->status->value === 'pending') bg-yellow-500/20 text-yellow-400
                    @endif">
                    {{ $expense->status_label }}
                </span>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">المبلغ</p>
                <p class="text-2xl font-bold text-white">{{ number_format($expense->amount, 2) }} <span class="text-lg text-gray-400">ر.س</span></p>
            </div>
        </div>
        @if($expense->status->value === 'pending')
        <div class="flex items-center gap-3">
            @can('approve', $expense)
            <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'approve' } }))" class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg text-sm">
                <i class="fas fa-circle-check ml-2"></i>
                اعتماد
            </button>
            @endcan
            @can('reject', $expense)
            <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'reject' } }))" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm">
                <i class="fas fa-ban ml-2"></i>
                رفض
            </button>
            @endcan
        </div>
        @endif
    </div>
    @if($expense->status->value === 'approved' && $expense->approver)
    <div class="mt-4 pt-4 border-t border-white/10">
        <p class="text-gray-400 text-sm">تم الاعتماد بواسطة: <span class="text-white">{{ $expense->approver->name }}</span></p>
        <p class="text-gray-400 text-sm">تاريخ الاعتماد: <span class="text-white">{{ $expense->approved_at->format('Y-m-d H:i') }}</span></p>
    </div>
    @elseif($expense->status->value === 'rejected' && $expense->rejector)
    <div class="mt-4 pt-4 border-t border-white/10">
        <p class="text-gray-400 text-sm">تم الرفض بواسطة: <span class="text-white">{{ $expense->rejector->name }}</span></p>
        <p class="text-gray-400 text-sm">تاريخ الرفض: <span class="text-white">{{ $expense->rejected_at->format('Y-m-d H:i') }}</span></p>
        @if($expense->rejection_reason)
        <p class="text-gray-400 text-sm mt-2">سبب الرفض: <span class="text-white">{{ $expense->rejection_reason }}</span></p>
        @endif
    </div>
    @endif
</div>

<!-- Main Information -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Expense Details -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-6">معلومات المصروف</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم السند</p>
                <p class="text-white font-semibold">{{ $expense->voucher_number }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">التاريخ</p>
                <p class="text-white font-semibold">{{ $expense->date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">القسم</p>
                <p class="text-white font-semibold">{{ $expense->department }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">نوع المصروف</p>
                <p class="text-white font-semibold">{{ $expense->type }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">طريقة الدفع</p>
                <p class="text-white font-semibold">{{ $expense->payment_method }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">المبلغ</p>
                <p class="text-white font-semibold text-lg">{{ number_format($expense->amount, 2) }} ر.س</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">الوصف</p>
                <p class="text-white">{{ $expense->description }}</p>
            </div>
            @if($expense->notes)
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">ملاحظات إضافية</p>
                <p class="text-white">{{ $expense->notes }}</p>
            </div>
            @endif
            <div>
                <p class="text-gray-400 text-sm mb-1">منشئ المصروف</p>
                <p class="text-white font-semibold">{{ $expense->creator->name ?? 'غير محدد' }}</p>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        <div class="space-y-3">
            @if($expense->status->value === 'approved' && $expense->approved_at)
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم الاعتماد</p>
                    <p class="text-gray-400 text-xs">بواسطة: {{ $expense->approver->name ?? 'غير محدد' }}</p>
                    <p class="text-gray-400 text-xs">{{ $expense->approved_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            @elseif($expense->status->value === 'rejected' && $expense->rejected_at)
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ban text-white text-xs"></i>
                    </div>
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم الرفض</p>
                    <p class="text-gray-400 text-xs">بواسطة: {{ $expense->rejector->name ?? 'غير محدد' }}</p>
                    <p class="text-gray-400 text-xs">{{ $expense->rejected_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            @endif
            <div class="flex gap-3">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-plus text-white text-xs"></i>
                    </div>
                </div>
                <div class="flex-1 pb-3">
                    <p class="text-white text-sm font-semibold">تم الإضافة</p>
                    <p class="text-gray-400 text-xs">بواسطة: {{ $expense->creator->name ?? 'غير محدد' }}</p>
                    <p class="text-gray-400 text-xs">{{ $expense->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attachments -->
@if($expense->attachments->count() > 0)
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($expense->attachments as $attachment)
            @php
                $isPdf = str_contains($attachment->file_type, 'pdf');
                $isImage = str_contains($attachment->file_type, 'image');
                $iconColor = $isPdf ? 'red' : ($isImage ? 'blue' : 'gray');
            @endphp
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-{{ $iconColor }}-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $isPdf ? 'file-pdf' : ($isImage ? 'file-image' : 'file') }} text-{{ $iconColor }}-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $attachment->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $attachment->formatted_size }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($isImage)
                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="flex-1 px-3 py-2 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm text-center">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </a>
                    @endif
                    <a href="{{ route('expenses.attachments.download', ['id' => $expense->id, 'attachmentId' => $attachment->id]) }}" class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded text-sm text-center">
                        <i class="fas fa-download ml-1"></i>
                        تحميل
                    </a>
                    @can('update', $expense)
                    <form action="{{ route('expenses.attachments.delete', ['id' => $expense->id, 'attachmentId' => $attachment->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المرفق؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Approval Modal -->
@include('components.modals.expense-approval')

@push('scripts')
<script>
function openApprovalModal(action) {
    window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: action } }));
}
</script>
@endpush

@endsection


