@extends('layouts.dashboard')

@section('title', 'المستندات - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'المستندات')

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

<div x-data="documentsPage()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">المستندات</h1>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('create', \App\Models\Document::class)
            <a href="{{ route('documents.create', ['type' => 'technical_report']) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-file-alt ml-2"></i>
                تقرير فني جديد
            </a>
            <a href="{{ route('documents.create', ['type' => 'quotation']) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                عرض سعر جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-xl md:rounded-2xl mb-4 md:mb-6">
        <div class="border-b border-white/10 flex flex-wrap overflow-x-auto">
            <a 
                href="{{ route('documents.index', ['type' => 'technical_report'] + request()->except('type')) }}"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $type === 'technical_report' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white' }}"
            >
                <i class="fas fa-file-alt"></i>
                التقارير الفنية
            </a>
            <a 
                href="{{ route('documents.index', ['type' => 'quotation'] + request()->except('type')) }}"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $type === 'quotation' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white' }}"
            >
                <i class="fas fa-file-invoice-dollar"></i>
                عروض الأسعار
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <form method="GET" action="{{ route('documents.index') }}" class="space-y-4">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <!-- Search Bar -->
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="البحث: رقم المستند، العنوان..." 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
                <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">المشروع</label>
                    <select name="project_id" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">جميع المشاريع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">العميل</label>
                    <select name="client_id" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">جميع العملاء</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                    <select name="status" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">جميع الحالات</option>
                        @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-gray-300 text-xs md:text-sm mb-2">من تاريخ</label>
                        <input 
                            type="date" 
                            name="from_date"
                            value="{{ request('from_date') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-300 text-xs md:text-sm mb-2">إلى تاريخ</label>
                        <input 
                            type="date" 
                            name="to_date"
                            value="{{ request('to_date') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('documents.index', ['type' => $type]) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-redo ml-1"></i>
                    إعادة تعيين
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-search ml-1"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Documents List -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        @if($documents->count() > 0)
        <div class="space-y-4">
            @foreach($documents as $document)
            <div class="bg-white/5 rounded-lg p-4 md:p-6 hover:bg-white/10 transition-all duration-200">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas {{ $document->type === 'technical_report' ? 'fa-file-alt' : 'fa-file-invoice-dollar' }} text-2xl text-[#1db8f8]"></i>
                            <div>
                                <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                    {{ $document->title }}
                                    @php
                                        $statusColor = getStatusColor($document->status);
                                    @endphp
                                    <span class="bg-{{ $statusColor }}-500/20 text-{{ $statusColor }}-400 px-2 py-0.5 rounded text-xs">
                                        {{ $statuses[$document->status] ?? $document->status }}
                                    </span>
                                </h3>
                                <p class="text-gray-400 text-sm mt-1">
                                    <i class="fas fa-hashtag ml-1"></i>
                                    {{ $document->document_number }}
                                </p>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400 mt-2">
                                    @if($document->project)
                                    <span>
                                        <i class="fas fa-project-diagram ml-1"></i>
                                        {{ $document->project->name }}
                                    </span>
                                    @endif
                                    @if($document->client)
                                    <span>
                                        <i class="fas fa-user-tie ml-1"></i>
                                        {{ $document->client->name }}
                                    </span>
                                    @endif
                                    <span>
                                        <i class="fas fa-calendar ml-1"></i>
                                        {{ $document->created_at->format('Y-m-d') }}
                                    </span>
                                    @if($document->type === 'quotation' && $document->total_price)
                                    <span class="text-[#1db8f8] font-semibold">
                                        <i class="fas fa-money-bill-wave ml-1"></i>
                                        {{ number_format($document->total_price, 2) }} ر.س
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @can('view', $document)
                        <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm" title="عرض">
                            <i class="fas fa-eye"></i>
                        </a>
                        @endcan
                        @can('update', $document)
                        <a href="{{ route('documents.edit', $document) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm" title="تعديل">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        @can('submit', $document)
                        <form action="{{ route('documents.submit', $document) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إرسال هذا المستند؟')">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg transition-all duration-200 text-sm" title="إرسال">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        @endcan
                        @can('approve', $document)
                        <button 
                            @click="$dispatch('open-approval-modal', { documentId: {{ $document->id }}, documentNumber: '{{ $document->document_number }}' })"
                            class="px-3 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-all duration-200 text-sm" 
                            title="اعتماد"
                        >
                            <i class="fas fa-check"></i>
                        </button>
                        @endcan
                        @can('duplicate', $document)
                        <form action="{{ route('documents.duplicate', $document) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg transition-all duration-200 text-sm" title="نسخ">
                                <i class="fas fa-copy"></i>
                            </button>
                        </form>
                        @endcan
                        <a href="{{ route('documents.preview-pdf', $document) }}" target="_blank" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm" title="معاينة PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @can('delete', $document)
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg">لا توجد مستندات</p>
            @can('create', \App\Models\Document::class)
            <a href="{{ route('documents.create', ['type' => $type]) }}" class="mt-4 inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء {{ $type === 'technical_report' ? 'تقرير فني جديد' : 'عرض سعر جديد' }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>


@php
function getStatusColor($status) {
    $colors = [
        'draft' => 'gray',
        'submitted' => 'yellow',
        'sent' => 'yellow',
        'approved' => 'green',
        'accepted' => 'green',
        'rejected' => 'red',
        'expired' => 'gray',
    ];
    return $colors[$status] ?? 'blue';
}
@endphp

<!-- Approval Modal -->
<div 
    x-data="{ 
        showModal: false, 
        documentId: null, 
        documentNumber: '', 
        action: 'approved', 
        reason: '' 
    }"
    @open-approval-modal.window="showModal = true; documentId = $event.detail.documentId; documentNumber = $event.detail.documentNumber"
    x-show="showModal"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
    @click.self="showModal = false"
>
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-white mb-4">اعتماد / رفض التقرير</h3>
        <form :action="`/documents/${documentId}/approve`" method="POST">
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
                <button type="button" @click="showModal = false" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
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

@push('scripts')
<script>
function documentsPage() {
    return {
        init() {
            // Modal is handled by Alpine.js above
        }
    }
}
</script>
@endpush
@endsection
