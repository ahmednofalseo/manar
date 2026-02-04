@extends('layouts.dashboard')

@section('title', 'تفاصيل الموافقة - المنار')
@section('page-title', 'تفاصيل الموافقة')

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
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('approvals.index') }}" class="text-primary-400 hover:text-primary-300">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للقائمة
        </a>
    </div>

    <!-- Approval Details -->
    <div class="glass-card rounded-xl md:rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white mb-6">تفاصيل الموافقة</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Project Info -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">معلومات المشروع</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-400 text-sm">اسم المشروع:</span>
                        <p class="text-white font-semibold">{{ $approval->project->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">رقم المشروع:</span>
                        <p class="text-white">{{ $approval->project->project_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">العميل:</span>
                        <p class="text-white">{{ $approval->client->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Approval Info -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">معلومات الموافقة</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-400 text-sm">المرحلة:</span>
                        <p class="text-white font-semibold">{{ $approval->stage_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">الحالة:</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold 
                            {{ $approval->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                            {{ $approval->status === 'approved' ? 'bg-green-500/20 text-green-400' : '' }}
                            {{ $approval->status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}
                        ">
                            {{ $approval->status_label }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">طلب من:</span>
                        <p class="text-white">{{ $approval->requester->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">تاريخ الطلب:</span>
                        <p class="text-white">{{ $approval->requested_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($approval->decided_by)
                    <div>
                        <span class="text-gray-400 text-sm">قرر من:</span>
                        <p class="text-white">{{ $approval->decider->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 text-sm">تاريخ القرار:</span>
                        <p class="text-white">{{ $approval->decided_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Item Info -->
        <div class="mt-6 pt-6 border-t border-white/10">
            <h3 class="text-lg font-semibold text-white mb-4">العنصر المطلوب الموافقة عليه</h3>
            <div class="bg-white/5 rounded-lg p-4">
                <p class="text-gray-300">
                    <i class="fas 
                        {{ $approval->approvable_type === 'App\Models\ProjectAttachment' ? 'fa-file' : 'fa-layer-group' }} 
                        ml-2
                    "></i>
                    {{ $approval->approvable_type === 'App\Models\ProjectAttachment' ? 'مرفق مشروع' : 'مرحلة مشروع' }}
                </p>
            </div>
        </div>

        <!-- Notes -->
        @if($approval->manager_note || $approval->client_note)
        <div class="mt-6 pt-6 border-t border-white/10">
            <h3 class="text-lg font-semibold text-white mb-4">الملاحظات</h3>
            <div class="space-y-4">
                @if($approval->manager_note)
                <div>
                    <span class="text-gray-400 text-sm">ملاحظة المدير:</span>
                    <p class="text-white bg-white/5 rounded-lg p-4 mt-2">{{ $approval->manager_note }}</p>
                </div>
                @endif
                @if($approval->client_note)
                <div>
                    <span class="text-gray-400 text-sm">ملاحظة العميل:</span>
                    <p class="text-white bg-white/5 rounded-lg p-4 mt-2">{{ $approval->client_note }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        @if($approval->status === 'pending' && auth()->user()->can('decide', $approval))
        <div class="mt-6 pt-6 border-t border-white/10 flex items-center gap-3">
            <button onclick="openDecideModal({{ $approval->id }}, 'approve')" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-check ml-2"></i>
                موافقة
            </button>
            <button onclick="openDecideModal({{ $approval->id }}, 'reject')" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-times ml-2"></i>
                رفض
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Decide Modal -->
@include('approvals.modals.decide')

@push('scripts')
<script>
function openDecideModal(approvalId, decision) {
    window.dispatchEvent(new CustomEvent('open-decide-modal', { 
        detail: { approvalId: approvalId, decision: decision } 
    }));
}
</script>
@endpush

@endsection






