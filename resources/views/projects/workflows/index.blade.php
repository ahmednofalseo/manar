@extends('layouts.dashboard')

@section('title', 'مسارات المشروع - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'مسارات المشروع')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .step-timeline {
        position: relative;
    }
    .step-timeline::before {
        content: '';
        position: absolute;
        right: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(255, 255, 255, 0.1);
    }
    .step-item {
        position: relative;
    }
    .step-item::before {
        content: '';
        position: absolute;
        right: 15px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #1db8f8;
        border: 3px solid #173343;
        z-index: 1;
    }
    .step-item.completed::before {
        background: #10b981;
    }
    .step-item.in-progress::before {
        background: #f59e0b;
        animation: pulse 2s infinite;
    }
    .step-item.blocked::before {
        background: #ef4444;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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

<div x-data="workflowsPage()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">مسارات المشروع</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $project->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            @can('update', $project)
            <a href="{{ route('projects.workflows.create', $project) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة مسار جديد
            </a>
            @endcan
            <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>

    @if($workflows->count() > 0)
    <!-- Workflows Tabs -->
    <div class="glass-card rounded-xl md:rounded-2xl mb-4 md:mb-6">
        <div class="border-b border-white/10 flex flex-wrap overflow-x-auto">
            @foreach($workflows as $index => $workflow)
            <button 
                @click="activeWorkflow = {{ $workflow->id }}"
                :class="activeWorkflow === {{ $workflow->id }} ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-sitemap"></i>
                {{ $workflow->name }}
                @if($workflow->is_parallel)
                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded text-xs">متوازي</span>
                @endif
                <span class="bg-primary-400/20 text-primary-400 px-2 py-0.5 rounded text-xs">{{ $workflow->progress }}%</span>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Workflow Content -->
    @foreach($workflows as $workflow)
    <div x-show="activeWorkflow === {{ $workflow->id }}" class="space-y-6">
        <!-- Workflow Header -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white mb-2">{{ $workflow->name }}</h2>
                    @if($workflow->service)
                    <p class="text-gray-400 text-sm">
                        <i class="fas fa-cog ml-1"></i>
                        {{ $workflow->service->name }}
                    </p>
                    @endif
                    <div class="flex items-center gap-4 mt-3 text-sm">
                        <span class="text-gray-400">
                            <i class="fas fa-calendar ml-1"></i>
                            البدء: {{ $workflow->start_date ? $workflow->start_date->format('Y-m-d') : 'غير محدد' }}
                        </span>
                        <span class="text-gray-400">
                            <i class="fas fa-calendar-check ml-1"></i>
                            الانتهاء المتوقع: {{ $workflow->expected_end_date ? $workflow->expected_end_date->format('Y-m-d') : 'غير محدد' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="bg-primary-500/20 rounded-lg px-4 py-2">
                        <span class="text-primary-400 font-bold">{{ $workflow->progress }}%</span>
                    </div>
                    @can('update', $project)
                    <a href="{{ route('projects.workflows.show', [$project, $workflow]) }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200">
                        <i class="fas fa-cog ml-2"></i>
                        إدارة
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Steps Timeline -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-6">خطوات المسار</h3>
            <div class="step-timeline space-y-6">
                @foreach($workflow->steps as $step)
                <div class="step-item {{ $step->status === 'completed' ? 'completed' : ($step->status === 'in_progress' ? 'in-progress' : ($step->status === 'blocked' ? 'blocked' : '')) }} pr-10">
                    <div class="bg-white/5 rounded-lg p-4 border-r-4 {{ $step->status === 'completed' ? 'border-green-500' : ($step->status === 'in_progress' ? 'border-yellow-500' : ($step->status === 'blocked' ? 'border-red-500' : 'border-[#1db8f8]')) }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h4 class="text-lg font-bold text-white">{{ $step->name }}</h4>
                                    <span class="bg-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-500/20 text-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-400 px-2 py-0.5 rounded text-xs">
                                        @if($step->status === 'completed')
                                        مكتمل
                                        @elseif($step->status === 'in_progress')
                                        قيد التنفيذ
                                        @elseif($step->status === 'blocked')
                                        معطل
                                        @else
                                        معلق
                                        @endif
                                    </span>
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
                                        {{ $step->duration_days }} يوم
                                    </span>
                                    @if($step->assignedUser)
                                    <span class="text-gray-400">
                                        <i class="fas fa-user ml-1"></i>
                                        {{ $step->assignedUser->name }}
                                    </span>
                                    @endif
                                    @if($step->expected_end_date)
                                    <span class="text-gray-400">
                                        <i class="fas fa-calendar ml-1"></i>
                                        {{ $step->expected_end_date->format('Y-m-d') }}
                                    </span>
                                    @endif
                                    @if($step->delay_days > 0)
                                    <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs">
                                        <i class="fas fa-exclamation-triangle ml-1"></i>
                                        تأخير {{ $step->delay_days }} يوم
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
    @else
    <!-- Empty State -->
    <div class="glass-card rounded-xl md:rounded-2xl p-12 text-center">
        <i class="fas fa-sitemap text-6xl text-gray-500 mb-4"></i>
        <p class="text-gray-400 text-lg mb-4">لا توجد مسارات لهذا المشروع</p>
        @can('update', $project)
        <a href="{{ route('projects.workflows.create', $project) }}" class="inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-plus ml-2"></i>
            إضافة مسار جديد
        </a>
        @endcan
    </div>
    @endif
</div>

@push('scripts')
<script>
function workflowsPage() {
    return {
        activeWorkflow: {{ $workflows->count() > 0 ? $workflows->first()->id : 'null' }},
    }
}
</script>
@endpush
@endsection
