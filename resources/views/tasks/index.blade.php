@extends('layouts.dashboard')

@section('title', __('Task Management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Task Management'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .kanban-column {
        min-height: 500px;
    }
    .task-card {
        transition: all 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 184, 248, 0.2);
    }
    .task-card.dragging {
        opacity: 0.5;
    }
    .kanban-column.drag-over {
        background: rgba(29, 184, 248, 0.1);
        border: 2px dashed rgba(29, 184, 248, 0.5);
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

@if(session('error'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Task Management') }}</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('New Task') }}
        </a>
    </div>
</div>

<!-- Statistics Charts -->
@php
    $projectsChartData = [
        'completed' => $completedProjects ?? 0,
        'in_progress' => $inProgressProjects ?? 0,
        'delayed' => $delayedProjects ?? 0,
    ];
    
    $engineersChartLabels = $topEngineersForChart->pluck('name')->toArray();
    $engineersChartData = $topEngineersForChart->pluck('completed_tasks')->toArray();
    
    $tasksByStatusChartData = [
        $tasksByStatus['new'] ?? 0,
        $tasksByStatus['in_progress'] ?? 0,
        $tasksByStatus['done'] ?? 0,
        $tasksByStatus['rejected'] ?? 0,
    ];
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6" 
     x-data="chartsData(
         {{ json_encode($projectsChartData) }},
         {{ json_encode($engineersChartLabels) }},
         {{ json_encode($engineersChartData) }},
         {{ json_encode($tasksByStatusChartData) }}
     )" 
     x-init="initCharts()">
    <!-- Project Completion Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Project Completion Rate') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="projectCompletionChart"></canvas>
        </div>
    </div>

    <!-- Top Engineers Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Top 5 Engineers') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="topEngineersChart"></canvas>
        </div>
    </div>

    <!-- Tasks by Stage Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Tasks by Stage') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="tasksByStageChart"></canvas>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Tasks Count') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $totalTasks }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tasks text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('In Progress') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-primary-400 mt-1 md:mt-2" style="color: #4787a7 !important;">{{ $inProgressTasks }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-spinner text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Completed Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">{{ $completedTasks }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Overdue Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-red-400 mt-1 md:mt-2">{{ $overdueTasks }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="taskFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="{{ __('Search: name, phone, email...') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Project') }}</label>
                <select name="project_id" x-model="project" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Projects') }}</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                            {{ $proj->name }}@if($proj->project_number) ({{ $proj->project_number }})@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Engineer') }}</label>
                <select name="assignee_id" x-model="engineer" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Engineers') }}</option>
                    @foreach($engineers as $eng)
                        <option value="{{ $eng->id }}" {{ request('assignee_id') == $eng->id ? 'selected' : '' }}>
                            {{ $eng->name }}@if($eng->job_title) - {{ $eng->job_title }}@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Stage') }}</label>
                <select name="project_stage_id" x-model="stage" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Stages') }}</option>
                    @foreach($stages as $stageName)
                        <option value="{{ $stageName }}" {{ request('project_stage_id') == $stageName ? 'selected' : '' }}>
                            {{ $stageName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>{{ __('New') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>{{ __('Done') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Period') }}</label>
                <select x-model="period" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Periods') }}</option>
                    <option value="today">{{ __('Today') }}</option>
                    <option value="week">{{ __('This Week') }}</option>
                    <option value="month">{{ __('This Month') }}</option>
                    <option value="custom">{{ __('Custom') }}</option>
                </select>
            </div>
        </div>

        <!-- Date Range (when custom selected) -->
        <div x-show="period === 'custom'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('From Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('To Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="hidden" name="search" :value="search">
            <input type="hidden" name="project_id" :value="project">
            <input type="hidden" name="assignee_id" :value="engineer">
            <input type="hidden" name="project_stage_id" :value="stage">
            <input type="hidden" name="status" :value="status">
            <input type="hidden" name="from_date" :value="dateFrom">
            <input type="hidden" name="to_date" :value="dateTo">
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Apply Filters') }}
            </button>
            <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base text-center">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </form>
    </div>
</div>

<!-- Kanban Board / Table View -->
@php
    $tasksJson = $tasks->map(function($task) {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'project' => $task->project ? ['name' => $task->project->name] : null,
            'assignee' => $task->assignee ? ['name' => $task->assignee->name] : null,
            'project_stage' => $task->projectStage ? ['stage_name' => $task->projectStage->stage_name] : null,
            'status' => $task->status,
            'progress' => $task->progress ?? 0,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
            'priority' => $task->priority ?? 'medium',
        ];
    })->values()->toJson();
@endphp
<div class="mb-4 md:mb-6" x-data="tasksData({{ $tasksJson }})">
    <!-- View Toggle -->
    <div class="flex items-center justify-end gap-3 mb-4">
        <button @click="viewMode = 'kanban'" :class="viewMode === 'kanban' ? 'bg-primary-500 text-white' : 'bg-white/5 text-gray-300'" class="px-4 py-2 rounded-lg transition-all duration-200 text-sm">
            <i class="fas fa-columns {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            Kanban
        </button>
        <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-primary-500 text-white' : 'bg-white/5 text-gray-300'" class="px-4 py-2 rounded-lg transition-all duration-200 text-sm">
            <i class="fas fa-table {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Table') }}
        </button>
    </div>

    <!-- Kanban Board -->
    <div x-show="viewMode === 'kanban'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- New Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">{{ __('New') }}</h3>
                <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('new').length"></span>
            </div>
            <div class="space-y-3" @drop="handleDrop($event, 'new')" @dragover.prevent @dragenter.prevent>
                <template x-for="task in getTasksByStatus('new')" :key="`task-${task.id}-${task.status}`">
                    <div 
                        class="task-card glass-card rounded-lg p-4 border border-white/10 cursor-move"
                        draggable="true"
                        @dragstart="handleDragStart($event, task)"
                        @dragend="handleDragEnd($event)"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1">
                                <!-- Priority Icon -->
                                <div x-show="task.priority === 'high'" class="text-red-400" title="عاجل - مطلوب بسرعة">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div x-show="task.priority === 'medium'" class="text-yellow-400" title="متوسطة">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div x-show="task.priority === 'low'" class="text-green-400" title="منخفضة">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h4 class="text-white font-semibold text-sm flex-1" x-text="task.title"></h4>
                            </div>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-primary-400 h-1.5 rounded-full" :style="'width: ' + task.progress + '%'"></div>
                            </div>
                            <span class="text-gray-400 text-xs" x-text="task.progress + '%'"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openAttachmentModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" x-show="task.hasAttachment">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-primary-400">{{ __('In Progress') }}</h3>
                <span class="bg-primary-400/20 text-primary-400 px-2 py-1 rounded text-xs font-semibold" style="color: #4787a7 !important;" x-text="getTasksByStatus('in_progress').length"></span>
            </div>
            <div class="space-y-3" @drop="handleDrop($event, 'in_progress')" @dragover.prevent @dragenter.prevent @dragleave.prevent>
                <template x-for="task in getTasksByStatus('in_progress')" :key="`task-${task.id}-${task.status}`">
                    <div 
                        class="task-card glass-card rounded-lg p-4 border border-primary-400/30 cursor-move"
                        draggable="true"
                        @dragstart="handleDragStart($event, task)"
                        @dragend="handleDragEnd($event)"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1">
                                <!-- Priority Icon -->
                                <div x-show="task.priority === 'high'" class="text-red-400" title="عاجل - مطلوب بسرعة">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div x-show="task.priority === 'medium'" class="text-yellow-400" title="متوسطة">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div x-show="task.priority === 'low'" class="text-green-400" title="منخفضة">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h4 class="text-white font-semibold text-sm flex-1" x-text="task.title"></h4>
                            </div>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-primary-400 h-1.5 rounded-full" :style="'width: ' + task.progress + '%'"></div>
                            </div>
                            <span class="text-gray-400 text-xs" x-text="task.progress + '%'"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openAttachmentModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" x-show="task.hasAttachment">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-green-400">{{ __('Done') }}</h3>
                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('done').length"></span>
            </div>
            <div class="space-y-3" @drop="handleDrop($event, 'done')" @dragover.prevent @dragenter.prevent @dragleave.prevent>
                <template x-for="task in getTasksByStatus('done')" :key="`task-${task.id}-${task.status}`">
                    <div 
                        class="task-card glass-card rounded-lg p-4 border border-green-400/30 cursor-move"
                        draggable="true"
                        @dragstart="handleDragStart($event, task)"
                        @dragend="handleDragEnd($event)"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1">
                                <!-- Priority Icon -->
                                <div x-show="task.priority === 'high'" class="text-red-400" title="عاجل - مطلوب بسرعة">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div x-show="task.priority === 'medium'" class="text-yellow-400" title="متوسطة">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div x-show="task.priority === 'low'" class="text-green-400" title="منخفضة">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h4 class="text-white font-semibold text-sm flex-1" x-text="task.title"></h4>
                            </div>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-green-400 h-1.5 rounded-full" style="width: 100%"></div>
                            </div>
                            <span class="text-green-400 text-xs">100%</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs" :title="'{{ __('Comment') }}'">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openStageModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" :title="'{{ __('Edit Stage') }}'">
                                <i class="fas fa-diagram-project"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs" :title="'{{ __('Update Status') }}'">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Rejected Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-red-400">{{ __('Rejected') }}</h3>
                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('rejected').length"></span>
            </div>
            <div class="space-y-3" @drop="handleDrop($event, 'rejected')" @dragover.prevent @dragenter.prevent @dragleave.prevent>
                <template x-for="task in getTasksByStatus('rejected')" :key="`task-${task.id}-${task.status}`">
                    <div 
                        class="task-card glass-card rounded-lg p-4 border border-red-400/30 cursor-move"
                        draggable="true"
                        @dragstart="handleDragStart($event, task)"
                        @dragend="handleDragEnd($event)"
                    >
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1">
                                <!-- Priority Icon -->
                                <div x-show="task.priority === 'high'" class="text-red-400" title="عاجل - مطلوب بسرعة">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div x-show="task.priority === 'medium'" class="text-yellow-400" title="متوسطة">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                <div x-show="task.priority === 'low'" class="text-green-400" title="منخفضة">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h4 class="text-white font-semibold text-sm flex-1" x-text="task.title"></h4>
                            </div>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 overflow-x-auto">
        @if($tasks->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Task') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Project') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Engineer') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Stage') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Progress') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    @php
                        $statusMap = [
                            'new' => ['text' => __('New'), 'class' => 'bg-gray-500/20 text-gray-400'],
                            'in_progress' => ['text' => __('In Progress'), 'class' => 'bg-primary-400/20 text-primary-400'],
                            'done' => ['text' => __('Done'), 'class' => 'bg-green-500/20 text-green-400'],
                            'rejected' => ['text' => __('Rejected'), 'class' => 'bg-red-500/20 text-red-400'],
                        ];
                        $status = $statusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400'];
                    @endphp
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <!-- Priority Icon -->
                                @if($task->priority === 'high')
                                <div class="text-red-400" title="عاجل - مطلوب بسرعة">
                                    <i class="fas fa-fire"></i>
                                </div>
                                @elseif($task->priority === 'medium')
                                <div class="text-yellow-400" title="متوسطة">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                                @else
                                <div class="text-green-400" title="منخفضة">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                @endif
                                <span class="text-white text-sm font-semibold">{{ $task->title }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-gray-300 text-sm">{{ $task->project->name ?? 'غير محدد' }}</td>
                        <td class="py-3 text-gray-300 text-sm">{{ $task->assignee->name ?? 'غير محدد' }}</td>
                        <td class="py-3 text-gray-300 text-sm">{{ $task->projectStage->stage_name ?? 'غير محدد' }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-white/5 rounded-full h-2 w-24">
                                    <div class="bg-primary-400 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                                </div>
                                <span class="text-gray-400 text-xs">{{ $task->progress }}%</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $status['class'] }}">
                                {{ $status['text'] }}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $task)
                                <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-400 hover:text-blue-300" :title="'{{ __('Edit') }}'">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @endcan
                                @can('changeStatus', $task)
                                <button onclick="openStatusModal({{ $task->id }})" class="text-green-400 hover:text-green-300" :title="'{{ __('Update Status') }}'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete', $task)
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this task?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300" :title="'{{ __('Delete') }}'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-tasks text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">{{ __('No tasks found') }}</h3>
            <p class="text-gray-400 mb-4">{{ __('Start by creating a new task') }}</p>
            <a href="{{ route('tasks.create') }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('New Task') }}
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
@include('components.modals.task-update')
@include('components.modals.task-comment')

@push('scripts')
<script>
function taskFilters() {
    return {
        search: '{{ request('search', '') }}',
        project: '{{ request('project_id', '') }}',
        engineer: '{{ request('assignee_id', '') }}',
        stage: '{{ request('project_stage_id', '') }}',
        status: '{{ request('status', '') }}',
        period: '{{ request('period', '') }}',
        dateFrom: '{{ request('from_date', '') }}',
        dateTo: '{{ request('to_date', '') }}',
    }
}

function tasksData(initialTasks) {
    return {
        viewMode: 'kanban', // Default to kanban view
        tasks: initialTasks.map(function(task) {
            return {
                id: task.id,
                title: task.title,
                project: task.project ? task.project.name : 'غير محدد',
                engineer: task.assignee ? task.assignee.name : 'غير محدد',
                stage: task.project_stage ? task.project_stage.stage_name : 'غير محدد',
                status: task.status,
                progress: task.progress || 0,
                hasAttachment: false,
                due_date: task.due_date || null,
                priority: task.priority || 'medium',
            };
        }),
        getTasksByStatus(status) {
            return this.tasks.filter(task => task.status === status);
        },
        getStatusText(status) {
            const statusMap = {
                'new': '{{ __('New') }}',
                'in_progress': '{{ __('In Progress') }}',
                'done': '{{ __('Done') }}',
                'rejected': '{{ __('Rejected') }}'
            };
            return statusMap[status] || status;
        },
        draggedTask: null,
        isDragging: false,
        handleDragStart(event, task) {
            // حفظ نسخة كاملة من المهمة
            this.draggedTask = { ...task };
            this.isDragging = true;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.dropEffect = 'move';
            // استخدام text/plain بدلاً من text/html لتجنب مشاكل المتصفح
            event.dataTransfer.setData('text/plain', task.id.toString());
            event.target.style.opacity = '0.5';
            event.target.classList.add('dragging');
        },
        handleDragEnd(event) {
            event.target.style.opacity = '1';
            event.target.classList.remove('dragging');
            this.isDragging = false;
            // لا نقوم بمسح draggedTask هنا - سيتم مسحه في handleDrop
        },
        handleDrop(event, newStatus) {
            event.preventDefault();
            event.stopPropagation();
            
            // التأكد من وجود مهمة مسحوبة
            if (!this.draggedTask) {
                console.log('No dragged task');
                return;
            }
            
            const taskId = this.draggedTask.id;
            const oldStatus = this.draggedTask.status;
            
            console.log('Dropping task', taskId, 'from', oldStatus, 'to', newStatus);
            
            // إذا كانت الحالة نفسها، لا حاجة لفعل أي شيء
            if (oldStatus === newStatus) {
                this.draggedTask = null;
                return;
            }
            
            // تحديث الحالة في القائمة مباشرة - إعادة إنشاء الـ array بالكامل لإجبار Alpine.js على إعادة الرسم
            this.tasks = this.tasks.map(task => 
                task.id == taskId ? { ...task, status: newStatus } : task
            );
            
            // تنظيف draggedTask بعد التحديث
            this.draggedTask = null;
            
            // تحديث الحالة في قاعدة البيانات
            this.updateTaskStatus(taskId, newStatus, oldStatus);
        },
        async updateTaskStatus(taskId, newStatus, oldStatus = null) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                formData.append('status', newStatus);
                formData.append('_token', csrfToken);

                const response = await fetch(`/tasks/${taskId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                let result;
                try {
                    result = await response.json();
                } catch (e) {
                    throw new Error('{{ __('An error occurred') }}');
                }
                
                if (response.ok) {
                    // التحقق من وجود result.success (قد لا يكون موجوداً في بعض الحالات)
                    if (result && result.success !== false) {
                        // لا حاجة لتحديث القائمة مرة أخرى - تم التحديث في handleDrop
                        // إظهار رسالة نجاح
                        this.showToast('success', (result && result.message) || '{{ __('Success') }}');
                    } else {
                        // في حالة وجود خطأ في الـ result - إعادة الحالة القديمة
                        if (oldStatus) {
                            this.tasks = this.tasks.map(task => 
                                task.id == taskId ? { ...task, status: oldStatus } : task
                            );
                        }
                        this.showToast('error', (result && result.message) || '{{ __('An error occurred') }}');
                    }
                } else {
                    // في حالة الفشل (response غير ok) - إعادة الحالة القديمة
                    if (oldStatus) {
                        this.tasks = this.tasks.map(task => 
                            task.id == taskId ? { ...task, status: oldStatus } : task
                        );
                    }
                    const errorMsg = (result && result.message) || `{{ __('An error occurred') }} (${response.status})`;
                    this.showToast('error', errorMsg);
                }
            } catch (error) {
                console.error('Error:', error);
                // في حالة الفشل، إعادة الحالة القديمة
                if (oldStatus) {
                    this.tasks = this.tasks.map(task => 
                        task.id == taskId ? { ...task, status: oldStatus } : task
                    );
                }
                this.showToast('error', '{{ __('An error occurred') }}');
            }
        },
        showToast(type, message) {
            // إنشاء toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        },
        openStatusModal(id) {
            window.dispatchEvent(new CustomEvent('open-task-status-modal', { detail: { taskId: id } }));
        },
        openStageModal(id) {
            alert('{{ __('Edit Stage') }}');
        },
        openCommentModal(id) {
            window.dispatchEvent(new CustomEvent('open-task-comment-modal', { detail: { taskId: id } }));
        },
        openAttachmentModal(id) {
            alert('{{ __('Attachments') }}');
        },
        deleteTask(id) {
            if (confirm('{{ __('Are you sure you want to delete this task?') }}')) {
                console.log('Deleting task:', id);
            }
        }
    }
}

function chartsData(projectsData = {}, engineersLabels = [], engineersData = [], tasksByStatusData = []) {
    return {
        projectChart: null,
        engineersChart: null,
        stageChart: null,
        projectsData: projectsData,
        engineersLabels: engineersLabels,
        engineersData: engineersData,
        tasksByStatusData: tasksByStatusData,
        initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initCharts(), 100);
                return;
            }

            // Project Completion Doughnut
            const projectCtx = document.getElementById('projectCompletionChart');
            if (projectCtx && !this.projectChart) {
                this.projectChart = new Chart(projectCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['{{ __('Completed') }}', '{{ __('In Progress') }}', '{{ __('Overdue') }}'],
                        datasets: [{
                            data: [
                                this.projectsData.completed || 0,
                                this.projectsData.in_progress || 0,
                                this.projectsData.delayed || 0
                            ],
                            backgroundColor: ['#10b981', '#1db8f8', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // Top Engineers Bar Chart
            const engineersCtx = document.getElementById('topEngineersChart');
            if (engineersCtx && !this.engineersChart) {
                this.engineersChart = new Chart(engineersCtx, {
                    type: 'bar',
                    data: {
                        labels: this.engineersLabels.length > 0 ? this.engineersLabels : ['لا توجد بيانات'],
                        datasets: [{
                            label: '{{ __('Completed Tasks') }}',
                            data: this.engineersData.length > 0 ? this.engineersData : [0],
                            backgroundColor: '#1db8f8'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Tasks by Stage Pie Chart
            const stageCtx = document.getElementById('tasksByStageChart');
            if (stageCtx && !this.stageChart) {
                this.stageChart = new Chart(stageCtx, {
                    type: 'pie',
                    data: {
                        labels: ['{{ __('New') }}', '{{ __('In Progress') }}', '{{ __('Done') }}', '{{ __('Rejected') }}'],
                        datasets: [{
                            data: this.tasksByStatusData.length > 0 ? this.tasksByStatusData : [0, 0, 0, 0],
                            backgroundColor: ['#3b82f6', '#1db8f8', '#10b981', '#ef4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush

@endsection


