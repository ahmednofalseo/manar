@extends('layouts.dashboard')

@section('title', 'تفاصيل المستند - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'تفاصيل المستند')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .document-content {
        background: white;
        color: #333;
        padding: 30px;
        border-radius: 8px;
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', sans-serif;
        min-height: 400px;
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $document->title }}</h1>
            <p class="text-gray-400 text-sm mt-1">
                <i class="fas fa-hashtag ml-1"></i>
                {{ $document->document_number }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @can('update', $document)
            <a href="{{ route('documents.edit', $document) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            @endcan
            <a href="{{ route('documents.index', ['type' => $document->type]) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Info -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">معلومات المستند</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-gray-400 text-sm">النوع</label>
                        <p class="text-white font-semibold">
                            {{ $document->type === 'technical_report' ? 'تقرير فني' : 'عرض سعر' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">الحالة</label>
                        <p class="text-white font-semibold">
                            @php
                                $statusColors = [
                                    'draft' => 'gray',
                                    'submitted' => 'yellow',
                                    'sent' => 'yellow',
                                    'approved' => 'green',
                                    'accepted' => 'green',
                                    'rejected' => 'red',
                                    'expired' => 'gray',
                                ];
                                $statusLabels = [
                                    'draft' => 'مسودة',
                                    'submitted' => 'مرسل',
                                    'sent' => 'مرسل',
                                    'approved' => 'معتمد',
                                    'accepted' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                    'expired' => 'منتهي',
                                ];
                                $color = $statusColors[$document->status] ?? 'blue';
                                $label = $statusLabels[$document->status] ?? $document->status;
                            @endphp
                            <span class="bg-{{ $color }}-500/20 text-{{ $color }}-400 px-2 py-0.5 rounded text-xs">{{ $label }}</span>
                        </p>
                    </div>
                    @if($document->project)
                    <div>
                        <label class="text-gray-400 text-sm">المشروع</label>
                        <p class="text-white font-semibold">{{ $document->project->name }}</p>
                    </div>
                    @endif
                    @if($document->client)
                    <div>
                        <label class="text-gray-400 text-sm">العميل</label>
                        <p class="text-white font-semibold">{{ $document->client->name }}</p>
                    </div>
                    @endif
                    @if($document->type === 'quotation' && $document->total_price)
                    <div>
                        <label class="text-gray-400 text-sm">السعر الإجمالي</label>
                        <p class="text-white font-semibold text-[#1db8f8]">{{ number_format($document->total_price, 2) }} ر.س</p>
                    </div>
                    @endif
                    @if($document->type === 'quotation' && $document->expires_at)
                    <div>
                        <label class="text-gray-400 text-sm">تاريخ الانتهاء</label>
                        <p class="text-white font-semibold">{{ $document->expires_at->format('Y-m-d') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Document Content -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">محتوى المستند</h2>
                <div class="document-content" x-data="{ content: @js($document->content) }" x-html="content"></div>
            </div>

            <!-- Approval History -->
            @if($document->approvals->count() > 0)
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">سجل الاعتماد</h2>
                <div class="space-y-3">
                    @foreach($document->approvals as $approval)
                    <div class="bg-white/5 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white font-semibold">{{ $approval->approver->name }}</p>
                                <p class="text-gray-400 text-sm">
                                    <span class="bg-{{ $approval->action === 'approved' ? 'green' : 'red' }}-500/20 text-{{ $approval->action === 'approved' ? 'green' : 'red' }}-400 px-2 py-0.5 rounded text-xs">
                                        {{ $approval->action === 'approved' ? 'معتمد' : 'مرفوض' }}
                                    </span>
                                    - {{ $approval->created_at->format('Y-m-d H:i') }}
                                </p>
                                @if($approval->reason)
                                <p class="text-gray-300 text-sm mt-2">{{ $approval->reason }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-lg font-bold text-white mb-4">إجراءات سريعة</h2>
                <div class="space-y-2">
                    @can('update', $document)
                    <a href="{{ route('documents.edit', $document) }}" class="block w-full px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200 text-center">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    @endcan
                    @can('submit', $document)
                    @if($document->status === 'draft')
                    <form action="{{ route('documents.submit', $document) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إرسال هذا المستند؟')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-paper-plane ml-2"></i>
                            {{ $document->type === 'technical_report' ? 'إرسال للاعتماد' : 'إرسال للعميل' }}
                        </button>
                    </form>
                    @endif
                    @endcan
                    @can('approve', $document)
                    @if($document->status === 'submitted')
                    <button 
                        @click="showApprovalModal = true"
                        class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200"
                    >
                        <i class="fas fa-check ml-2"></i>
                        اعتماد / رفض
                    </button>
                    @endif
                    @endcan
                    @can('duplicate', $document)
                    <form action="{{ route('documents.duplicate', $document) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-copy ml-2"></i>
                            نسخ المستند
                        </button>
                    </form>
                    @endcan
                    <a 
                        href="{{ route('documents.preview-pdf', $document) }}" 
                        target="_blank"
                        class="block w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 text-center"
                    >
                        <i class="fas fa-file-pdf ml-2"></i>
                        معاينة PDF
                    </a>
                    @if($document->type === 'technical_report' && $document->status === 'approved' && $document->pdf_path)
                    <a 
                        href="{{ Storage::url($document->pdf_path) }}" 
                        target="_blank"
                        class="block w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200 text-center"
                    >
                        <i class="fas fa-download ml-2"></i>
                        تحميل PDF النهائي
                    </a>
                    @endif
                </div>
            </div>

            <!-- Document Details -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-lg font-bold text-white mb-4">التفاصيل</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="text-gray-400">أنشئ بواسطة</label>
                        <p class="text-white font-semibold">{{ $document->creator->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $document->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($document->approved_by)
                    <div>
                        <label class="text-gray-400">معتمد بواسطة</label>
                        <p class="text-white font-semibold">{{ $document->approver->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $document->approved_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                    @if($document->rejection_reason)
                    <div>
                        <label class="text-gray-400">سبب الرفض</label>
                        <p class="text-red-300 text-sm">{{ $document->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
@can('approve', $document)
<div 
    x-data="{ showApprovalModal: false, action: 'approved', reason: '' }"
    x-show="showApprovalModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
    @click.self="showApprovalModal = false"
>
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-white mb-4">اعتماد / رفض التقرير</h3>
        <form action="{{ route('documents.approve', $document) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الإجراء <span class="text-red-400">*</span></label>
                    <select 
                        x-model="action"
                        name="action"
                        required
                        class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                        style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                    >
                        <option value="approved" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">اعتماد</option>
                        <option value="rejected" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">رفض</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">
                        <span x-show="action === 'rejected'">سبب الرفض <span class="text-red-400">*</span></span>
                        <span x-show="action === 'approved'">ملاحظات (اختياري)</span>
                    </label>
                    <textarea 
                        x-model="reason"
                        name="reason"
                        :required="action === 'rejected'"
                        rows="4"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="أدخل السبب أو الملاحظات..."
                    ></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="showApprovalModal = false" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <span x-show="action === 'approved'">اعتماد</span>
                    <span x-show="action === 'rejected'">رفض</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
