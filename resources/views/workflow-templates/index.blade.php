@extends('layouts.dashboard')

@section('title', 'قوالب المسارات - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'قوالب المسارات')

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

<div x-data="templatesPage()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">قوالب المسارات</h1>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('create', \App\Models\WorkflowTemplate::class)
            <a href="{{ route('workflow-templates.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-plus ml-2"></i>
                إنشاء قالب جديد
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الخدمة</label>
                <select x-model="serviceId" @change="applyFilters()" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">جميع الخدمات</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Templates List -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        @if($templates->count() > 0)
        <div class="space-y-4">
            @foreach($templates as $template)
            <div class="bg-white/5 rounded-lg p-4 md:p-6 hover:bg-white/10 transition-all duration-200">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-sitemap text-2xl text-[#1db8f8]"></i>
                            <div>
                                <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                    {{ $template->name }}
                                    @if($template->is_default)
                                    <span class="bg-primary-500/20 text-primary-400 px-2 py-0.5 rounded text-xs">افتراضي</span>
                                    @endif
                                    @if(!$template->is_active)
                                    <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs">غير نشط</span>
                                    @endif
                                </h3>
                                @if($template->service)
                                <p class="text-gray-400 text-sm mt-1">
                                    <i class="fas fa-cog ml-1"></i>
                                    {{ $template->service->name }}
                                </p>
                                @endif
                                @if($template->description)
                                <p class="text-gray-300 text-sm mt-2">{{ $template->description }}</p>
                                @endif
                                <div class="mt-3 flex items-center gap-4 text-sm text-gray-400">
                                    <span>
                                        <i class="fas fa-list-ol ml-1"></i>
                                        {{ $template->steps->count() }} خطوة
                                    </span>
                                    <span>
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $template->steps->sum('default_duration_days') }} يوم
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @can('view', $template)
                        <a href="{{ route('workflow-templates.show', $template) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-eye ml-1"></i>
                            عرض
                        </a>
                        @endcan
                        @can('update', $template)
                        <a href="{{ route('workflow-templates.edit', $template) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-edit ml-1"></i>
                            تعديل
                        </a>
                        @endcan
                        @can('delete', $template)
                        <form action="{{ route('workflow-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا القالب؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                                <i class="fas fa-trash ml-1"></i>
                                حذف
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
            {{ $templates->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-sitemap text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg">لا توجد قوالب مسارات</p>
            @can('create', \App\Models\WorkflowTemplate::class)
            <a href="{{ route('workflow-templates.create') }}" class="mt-4 inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إنشاء قالب جديد
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function templatesPage() {
    return {
        serviceId: new URLSearchParams(window.location.search).get('service_id') || '',
        
        applyFilters() {
            const params = new URLSearchParams();
            if (this.serviceId) params.set('service_id', this.serviceId);
            
            window.location.href = '{{ route("workflow-templates.index") }}?' + params.toString();
        }
    }
}
</script>
@endpush
@endsection
