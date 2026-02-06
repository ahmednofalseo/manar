@extends('layouts.dashboard')

@section('title', __('Details') . ' - ' . __('Clients') . ' - ' . \App\Helpers\SettingsHelper::systemName())
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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $client->name }}</h1>
        <p class="text-gray-400 text-sm">تاريخ الإنشاء: {{ $client->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="flex items-center gap-3">
        @can('update', $client)
        <a href="{{ route('clients.edit', $client->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen ml-2"></i>
            {{ __('Edit') }}
        </a>
        @endcan
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-start gap-6">
        <div class="w-20 h-20 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user-circle text-primary-400 text-4xl"></i>
        </div>
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">نوع العميل</p>
                <p class="text-white font-semibold">{{ $client->type_label }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الجوال</p>
                <p class="text-white font-semibold">{{ $client->phone }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">البريد الإلكتروني</p>
                <p class="text-white font-semibold">{{ $client->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">المدينة / الحي</p>
                <p class="text-white font-semibold">{{ $client->city }}@if($client->district) / {{ $client->district }}@endif</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الهوية / السجل التجاري</p>
                <p class="text-white font-semibold">{{ $client->national_id_or_cr ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">الحالة</p>
                <span class="inline-block {{ $client->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }} px-3 py-1 rounded-lg text-sm font-semibold">{{ $client->status_label }}</span>
            </div>
            @if($client->address)
            <div class="md:col-span-2 lg:col-span-3">
                <p class="text-gray-400 text-sm mb-1">العنوان الكامل</p>
                <p class="text-white">{{ $client->address }}</p>
            </div>
            @endif
            @if($client->notes_internal)
            <div class="md:col-span-2 lg:col-span-3">
                <p class="text-gray-400 text-sm mb-1">ملاحظات داخلية</p>
                <p class="text-white">{{ $client->notes_internal }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="clientTabs()">
    <!-- Tab Headers -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-white/10">
        <button 
            @click="activeTab = 'projects'"
            :class="activeTab === 'projects' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-clipboard-list ml-2"></i>
            المشاريع المرتبطة
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">{{ $client->projects_count }}</span>
        </button>
        <button 
            @click="activeTab = 'attachments'"
            :class="activeTab === 'attachments' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-paperclip ml-2"></i>
            المرفقات
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">{{ $client->attachments->count() }}</span>
        </button>
        @if(\App\Helpers\PermissionHelper::hasPermission('documents.view'))
        <button 
            @click="activeTab = 'documents'"
            :class="activeTab === 'documents' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-file-alt ml-2"></i>
            المستندات
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">{{ $client->documents->count() }}</span>
        </button>
        @endif
        <button 
            @click="activeTab = 'notes'"
            :class="activeTab === 'notes' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-comment-dots ml-2"></i>
            ملاحظات العميل
            <span class="mr-2 bg-white/10 px-2 py-0.5 rounded text-xs">{{ $client->notes->count() }}</span>
        </button>
        <button 
            @click="activeTab = 'activity'"
            :class="activeTab === 'activity' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
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
            <a href="{{ route('projects.create') }}" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                مشروع جديد
            </a>
        </div>
        @if($client->projects->count() > 0)
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
                    @foreach($client->projects as $project)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold">{{ $project->name }}</td>
                        <td class="py-3 text-gray-300 text-sm">{{ $project->type ?? '-' }}</td>
                        <td class="py-3">
                            @php
                                $statusColors = [
                                    'قيد التنفيذ' => 'bg-primary-400/20 text-primary-400',
                                    'مكتمل' => 'bg-green-500/20 text-green-400',
                                    'متوقف' => 'bg-yellow-500/20 text-yellow-400',
                                    'ملغي' => 'bg-red-500/20 text-red-400',
                                ];
                                $statusColor = $statusColors[$project->status] ?? 'bg-gray-500/20 text-gray-400';
                            @endphp
                            <span class="{{ $statusColor }} px-2 py-1 rounded text-xs font-semibold">{{ $project->status ?? '-' }}</span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">{{ $project->start_date ? $project->start_date->format('Y-m-d') : '-' }}</td>
                        <td class="py-3">
                            <a href="{{ route('projects.show', $project->id) }}" class="text-primary-400 hover:text-primary-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 bg-white/5 rounded-lg">
            <i class="fas fa-clipboard-list text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg">لا توجد مشاريع مرتبطة بهذا العميل</p>
        </div>
        @endif
    </div>

    <!-- Attachments Tab -->
    <div x-show="activeTab === 'attachments'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">المرفقات</h3>
            @can('uploadAttachment', $client)
            <button type="button" @click="openAttachmentModal()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                رفع ملف
            </button>
            @endcan
        </div>
        @if($client->attachments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($client->attachments as $attachment)
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 {{ str_contains($attachment->file_type, 'pdf') ? 'bg-red-500/20' : 'bg-blue-500/20' }} rounded-lg flex items-center justify-center">
                                    <i class="fas {{ str_contains($attachment->file_type, 'pdf') ? 'fa-file-pdf text-red-400' : 'fa-file-image text-blue-400' }} text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-sm">{{ $attachment->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ number_format($attachment->file_size / 1024, 2) }} KB</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ $attachment->url }}" target="_blank" class="flex-1 px-3 py-2 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm text-center">
                                <i class="fas fa-eye ml-1"></i>
                                عرض
                            </a>
                            <a href="{{ $attachment->url }}" download class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded text-sm text-center">
                                <i class="fas fa-download ml-1"></i>
                                تحميل
                            </a>
                            @can('uploadAttachment', $client)
                            <form action="{{ route('clients.attachments.destroy', [$client->id, $attachment->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المرفق؟');">
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
        @else
            <div class="text-center py-8">
                <i class="fas fa-paperclip text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-400">لا توجد مرفقات</p>
            </div>
        @endif
    </div>

    @if(\App\Helpers\PermissionHelper::hasPermission('documents.view'))
    <!-- Documents Tab -->
    <div x-show="activeTab === 'documents'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">مستندات العميل</h3>
            @can('create', \App\Models\Document::class)
            <div class="flex items-center gap-2">
                <a href="{{ route('documents.create', ['type' => 'technical_report', 'client_id' => $client->id]) }}" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                    <i class="fas fa-file-alt ml-1"></i>
                    تقرير فني
                </a>
                <a href="{{ route('documents.create', ['type' => 'quotation', 'client_id' => $client->id]) }}" class="px-3 py-1 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded text-sm">
                    <i class="fas fa-file-invoice-dollar ml-1"></i>
                    عرض سعر
                </a>
            </div>
            @endcan
        </div>
        @if($client->documents->count() > 0)
        <div class="space-y-3">
            @foreach($client->documents as $document)
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 flex-1">
                        <i class="fas {{ $document->type === 'technical_report' ? 'fa-file-alt' : 'fa-file-invoice-dollar' }} text-2xl text-[#1db8f8]"></i>
                        <div>
                            <h4 class="text-white font-semibold">
                                <a href="{{ route('documents.show', $document) }}" class="hover:text-[#1db8f8] transition-colors">
                                    {{ $document->title }}
                                </a>
                            </h4>
                            <p class="text-gray-400 text-sm">
                                <i class="fas fa-hashtag ml-1"></i>
                                {{ $document->document_number }}
                                @if($document->project)
                                - {{ $document->project->name }}
                                @endif
                            </p>
                            <p class="text-gray-400 text-xs mt-1">
                                <i class="fas fa-calendar ml-1"></i>
                                {{ $document->created_at->format('Y-m-d') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('documents.preview-pdf', $document) }}" target="_blank" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @can('delete', $document)
                        @if($document->canBeDeleted())
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-white/5 rounded-lg">
            <i class="fas fa-file-alt text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg">لا توجد مستندات</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Notes Tab -->
    <div x-show="activeTab === 'notes'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">ملاحظات العميل</h3>
            @can('addNote', $client)
            <button type="button" @click="openNotesModal()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus ml-1"></i>
                إضافة ملاحظة
            </button>
            @endcan
        </div>
        @if($client->notes->count() > 0)
            <div class="space-y-3">
                @foreach($client->notes as $note)
                    <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-white font-semibold">{{ $note->creator->name ?? 'غير معروف' }}</p>
                                <p class="text-gray-400 text-xs">{{ $note->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm mt-2">{{ $note->body }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-comment-dots text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-400">لا توجد ملاحظات</p>
            </div>
        @endif
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
            window.dispatchEvent(new CustomEvent('open-client-attachment-modal', { detail: { clientId: '{{ $client->id }}' } }));
        },
        openNotesModal() {
            window.dispatchEvent(new CustomEvent('open-client-notes-modal', { detail: { clientId: '{{ $client->id }}' } }));
        }
    }
}
</script>
@endpush

@endsection


