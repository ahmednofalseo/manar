@php
    $workflows = $project->workflows ?? collect();
@endphp

<div x-data="workflowTab()">
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
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">خطوات المسار</h3>
                @can('update', $project)
                <a href="{{ route('projects.workflows.show', [$project, $workflow]) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                    <i class="fas fa-cog ml-1"></i>
                    إدارة المسار
                </a>
                @endcan
            </div>
            <div class="step-timeline space-y-6">
                @foreach($workflow->steps as $index => $step)
                <div class="step-item {{ $step->status === 'completed' ? 'completed' : ($step->status === 'in_progress' ? 'in-progress' : ($step->status === 'blocked' ? 'blocked' : '')) }} pr-10">
                    <div class="bg-white/5 rounded-lg p-4 border-r-4 {{ $step->status === 'completed' ? 'border-green-500' : ($step->status === 'in_progress' ? 'border-yellow-500' : ($step->status === 'blocked' ? 'border-red-500' : 'border-[#1db8f8]')) }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-[#1db8f8] text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <h4 class="text-lg font-bold text-white">{{ $step->name }}</h4>
                                    <span class="bg-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-500/20 text-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-400 px-2 py-0.5 rounded text-xs whitespace-nowrap">
                                        @if($step->status === 'completed')
                                        <i class="fas fa-check-circle ml-1"></i> مكتمل
                                        @elseif($step->status === 'in_progress')
                                        <i class="fas fa-spinner fa-spin ml-1"></i> قيد التنفيذ
                                        @elseif($step->status === 'blocked')
                                        <i class="fas fa-ban ml-1"></i> معطل
                                        @else
                                        <i class="fas fa-clock ml-1"></i> معلق
                                        @endif
                                    </span>
                                </div>
                                @if($step->description)
                                <p class="text-gray-300 text-sm mb-3 mr-11">{{ $step->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-4 text-sm">
                                    <span class="text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-building text-[#1db8f8]"></i>
                                        {{ $step->department }}
                                    </span>
                                    <span class="text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-clock text-yellow-400"></i>
                                        {{ $step->duration_days }} يوم
                                    </span>
                                    @if($step->assignedUser)
                                    <span class="text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-user text-green-400"></i>
                                        {{ $step->assignedUser->name }}
                                    </span>
                                    @endif
                                    @if($step->expected_end_date)
                                    <span class="text-gray-400 flex items-center gap-1">
                                        <i class="fas fa-calendar text-blue-400"></i>
                                        متوقع: {{ $step->expected_end_date->format('Y-m-d') }}
                                    </span>
                                    @endif
                                    @if($step->actual_completion_date)
                                    <span class="text-green-400 flex items-center gap-1">
                                        <i class="fas fa-check text-green-400"></i>
                                        اكتمل: {{ $step->actual_completion_date->format('Y-m-d') }}
                                    </span>
                                    @endif
                                    @if($step->delay_days > 0)
                                    <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i>
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

@push('styles')
<style>
    .step-timeline {
        position: relative;
        padding-right: 2rem;
    }
    .step-timeline::before {
        content: '';
        position: absolute;
        right: 1.5rem;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, 
            rgba(29, 184, 248, 0.3) 0%, 
            rgba(29, 184, 248, 0.5) 50%, 
            rgba(29, 184, 248, 0.3) 100%);
        border-radius: 2px;
    }
    .step-item {
        position: relative;
    }
    .step-item::after {
        content: '';
        position: absolute;
        right: 1.25rem;
        top: 1.5rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #1db8f8;
        border: 3px solid #173343;
        z-index: 2;
        box-shadow: 0 0 0 2px rgba(29, 184, 248, 0.2);
    }
    .step-item.completed::after {
        background: #10b981;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .step-item.in-progress::after {
        background: #f59e0b;
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        animation: pulse 2s infinite;
    }
    .step-item.blocked::after {
        background: #ef4444;
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }
    @keyframes pulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1);
        }
        50% { 
            opacity: 0.7; 
            transform: scale(1.1);
        }
    }
    .step-item:last-child::before {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
function workflowTab() {
    return {
        activeWorkflow: {{ $workflows->count() > 0 ? $workflows->first()->id : 'null' }},
    }
}
</script>
@endpush
