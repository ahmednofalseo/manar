@extends('layouts.dashboard')

@section('title', 'تفاصيل الخدمة - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'تفاصيل الخدمة')

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
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $service->name }}</h1>
            @if($service->category)
            <p class="text-gray-400 text-sm mt-1">
                <i class="fas fa-folder ml-1"></i>
                {{ $service->category->name }}
            </p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @can('update', $service)
            <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            @endcan
            <a href="{{ route('services.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Service Details -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">معلومات الخدمة</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-400 text-sm">الاسم</label>
                        <p class="text-white text-lg">{{ $service->name }}</p>
                    </div>
                    @if($service->description)
                    <div>
                        <label class="text-gray-400 text-sm">الوصف</label>
                        <p class="text-white">{{ $service->description }}</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-400 text-sm">الحالة</label>
                            <p class="text-white">
                                @if($service->is_active)
                                <span class="text-green-400">نشط</span>
                                @else
                                <span class="text-red-400">غير نشط</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-gray-400 text-sm">النوع</label>
                            <p class="text-white">
                                @if($service->is_custom)
                                <span class="text-yellow-400">مخصص</span>
                                @else
                                <span class="text-blue-400">افتراضي</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub Services -->
            @if($service->has_sub_services && $service->subServices->count() > 0)
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">الخدمات الفرعية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($service->subServices as $subService)
                    <div class="bg-white/5 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-arrow-left text-[#1db8f8]"></i>
                            <span class="text-white">{{ $subService->name }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Workflow Templates -->
            @if($service->workflowTemplates->count() > 0)
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-4">قوالب المسارات</h2>
                <div class="space-y-3">
                    @foreach($service->workflowTemplates as $template)
                    <div class="bg-white/5 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white font-semibold">{{ $template->name }}</h3>
                                @if($template->description)
                                <p class="text-gray-400 text-sm mt-1">{{ $template->description }}</p>
                                @endif
                                <p class="text-gray-400 text-xs mt-2">
                                    <i class="fas fa-list-ol ml-1"></i>
                                    {{ $template->steps->count() }} خطوة
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($template->is_default)
                                <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs">افتراضي</span>
                                @endif
                                <a href="{{ route('workflow-templates.show', $template) }}" class="px-3 py-1 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                                    <i class="fas fa-eye ml-1"></i>
                                    عرض
                                </a>
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
                    @can('create', \App\Models\WorkflowTemplate::class)
                    <a href="{{ route('workflow-templates.create', ['service_id' => $service->id]) }}" class="block w-full px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200 text-center">
                        <i class="fas fa-sitemap ml-2"></i>
                        إنشاء مسار جديد
                    </a>
                    @endcan
                    @can('update', $service)
                    <a href="{{ route('services.edit', $service) }}" class="block w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-center">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الخدمة
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Stats -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-lg font-bold text-white mb-4">الإحصائيات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">قوالب المسارات</span>
                        <span class="text-white font-bold">{{ $service->workflowTemplates->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">المشاريع المرتبطة</span>
                        <span class="text-white font-bold">{{ $service->projects->count() }}</span>
                    </div>
                    @if($service->has_sub_services)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">الخدمات الفرعية</span>
                        <span class="text-white font-bold">{{ $service->subServices->count() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
