@extends('layouts.dashboard')

@section('title', 'تفاصيل القالب - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'تفاصيل القالب')

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
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $workflowTemplate->name }}</h1>
            @if($workflowTemplate->service)
            <p class="text-gray-400 text-sm mt-1">
                <i class="fas fa-cog ml-1"></i>
                {{ $workflowTemplate->service->name }}
            </p>
            @endif
        </div>
        <div class="flex items-center gap-3">
            @can('update', $workflowTemplate)
            <a href="{{ route('workflow-templates.edit', $workflowTemplate) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            @endcan
            <a href="{{ route('workflow-templates.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Steps Timeline -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-xl font-bold text-white mb-6">خطوات المسار</h2>
                <div class="space-y-4">
                    @foreach($workflowTemplate->steps as $index => $step)
                    <div class="bg-white/5 rounded-lg p-4 border-r-4 border-[#1db8f8]">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-[#1db8f8] text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <h3 class="text-lg font-bold text-white">{{ $step->name }}</h3>
                                </div>
                                @if($step->description)
                                <p class="text-gray-300 text-sm mb-3">{{ $step->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    <span class="text-gray-400">
                                        <i class="fas fa-building ml-1"></i>
                                        {{ $step->department }}
                                    </span>
                                    <span class="text-gray-400">
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $step->default_duration_days }} يوم
                                    </span>
                                    @if($step->is_parallel)
                                    <span class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded text-xs">
                                        متوازي
                                    </span>
                                    @endif
                                    @if(!$step->is_required)
                                    <span class="bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded text-xs">
                                        اختياري
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Template Info -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-lg font-bold text-white mb-4">معلومات القالب</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-gray-400 text-sm">الحالة</label>
                        <p class="text-white">
                            @if($workflowTemplate->is_active)
                            <span class="text-green-400">نشط</span>
                            @else
                            <span class="text-red-400">غير نشط</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">النوع</label>
                        <p class="text-white">
                            @if($workflowTemplate->is_default)
                            <span class="text-primary-400">افتراضي</span>
                            @else
                            <span class="text-gray-400">مخصص</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                <h2 class="text-lg font-bold text-white mb-4">الإحصائيات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">عدد الخطوات</span>
                        <span class="text-white font-bold">{{ $workflowTemplate->steps->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">المدة الإجمالية</span>
                        <span class="text-white font-bold">{{ $workflowTemplate->steps->sum('default_duration_days') }} يوم</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">المسارات المستخدمة</span>
                        <span class="text-white font-bold">{{ $workflowTemplate->projectWorkflows->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
