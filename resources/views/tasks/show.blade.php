@extends('layouts.dashboard')

@section('title', __('Details') . ' - ' . __('Tasks') . ' - ' . \App\Helpers\SettingsHelper::systemName())
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
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $task->title }}</h1>
        <p class="text-gray-400 text-sm">تاريخ الإنشاء: {{ $task->created_at->format('Y-m-d') }}</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openStatusModal()" class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-check-circle ml-2"></i>
            {{ __('Mark as Completed') }}
        </button>
        <button onclick="openStatusModal('reject')" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-xmark ml-2"></i>
            {{ __('Reject') }}
        </button>
        <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen ml-2"></i>
            {{ __('Edit') }}
        </a>
        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div>
            <p class="text-gray-400 text-sm mb-2">المشروع</p>
            <p class="text-white font-semibold">{{ $task->project->name ?? 'غير محدد' }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المهندس المكلف</p>
            <p class="text-white font-semibold">{{ $task->assignee->name ?? 'غير محدد' }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المرحلة</p>
            <span class="inline-block bg-primary-400/20 text-primary-400 px-3 py-1 rounded-lg text-sm font-semibold">{{ $task->projectStage->stage_name ?? 'غير محدد' }}</span>
        </div>
        <!-- Status Control Section - Moved to separate card below -->
        <div>
            <p class="text-gray-400 text-sm mb-2">الأولوية</p>
            @php
                $priorityMap = [
                    'low' => ['text' => 'منخفضة', 'class' => 'bg-gray-500/20 text-gray-400'],
                    'medium' => ['text' => 'متوسطة', 'class' => 'bg-yellow-500/20 text-yellow-400'],
                    'high' => ['text' => 'عالية', 'class' => 'bg-red-500/20 text-red-400'],
                ];
                $priority = $priorityMap[$task->priority ?? 'medium'] ?? ['text' => 'متوسطة', 'class' => 'bg-yellow-500/20 text-yellow-400'];
            @endphp
            <span class="inline-block {{ $priority['class'] }} px-3 py-1 rounded-lg text-sm font-semibold">{{ $priority['text'] }}</span>
        </div>
        @if($task->start_date)
        <div>
            <p class="text-gray-400 text-sm mb-2">تاريخ البدء</p>
            <p class="text-white font-semibold">{{ $task->start_date->format('Y-m-d') }}</p>
        </div>
        @endif
        @if($task->due_date)
        <div>
            <p class="text-gray-400 text-sm mb-2">تاريخ الانتهاء</p>
            <p class="text-white font-semibold">{{ $task->due_date->format('Y-m-d') }}</p>
        </div>
        @endif
        <div>
            <p class="text-gray-400 text-sm mb-2">نسبة التقدم</p>
            <div class="flex items-center gap-2">
                <div class="flex-1 bg-white/5 rounded-full h-2">
                    <div class="bg-primary-400 h-2 rounded-full" style="width: {{ $task->progress ?? 0 }}%"></div>
                </div>
                <span class="text-white font-semibold text-sm">{{ $task->progress ?? 0 }}%</span>
            </div>
        </div>
    </div>
</div>

<!-- Status Control Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="taskStatusControl({{ $task->id }}, '{{ $task->status }}')">
    <h2 class="text-lg md:text-xl font-bold text-white mb-4 md:mb-6 flex items-center gap-2">
        <div class="w-10 h-10 bg-primary-400/20 rounded-lg flex items-center justify-center">
            <i class="fas fa-toggle-on text-primary-400 text-lg"></i>
        </div>
        <span>{{ __('Task Status') }}</span>
    </h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-5">
        @php
            $allStatuses = [
                'new' => [
                    'text' => __('New'),
                    'text_ar' => 'جديد',
                    'icon' => 'circle-dot',
                    'icon_bg' => 'bg-gray-500/20',
                    'icon_color' => 'text-gray-400',
                    'bg' => 'bg-gradient-to-br from-gray-500/5 to-gray-600/5',
                    'bg_hover' => 'bg-gradient-to-br from-gray-500/15 to-gray-600/15',
                    'bg_selected' => 'bg-gradient-to-br from-gray-400/20 to-gray-500/20',
                    'text_color' => 'text-gray-300',
                    'border' => 'border-gray-500/20',
                    'border_hover' => 'border-gray-400/40',
                    'border_selected' => 'border-gray-300/60',
                    'ring_color' => 'ring-gray-400/30'
                ],
                'in_progress' => [
                    'text' => __('In Progress'),
                    'text_ar' => 'قيد التنفيذ',
                    'icon' => 'clock-rotate-left',
                    'icon_bg' => 'bg-primary-400/20',
                    'icon_color' => 'text-primary-400',
                    'bg' => 'bg-gradient-to-br from-primary-500/5 to-primary-600/5',
                    'bg_hover' => 'bg-gradient-to-br from-primary-500/15 to-primary-600/15',
                    'bg_selected' => 'bg-gradient-to-br from-primary-400/20 to-primary-500/20',
                    'text_color' => 'text-primary-300',
                    'border' => 'border-primary-500/20',
                    'border_hover' => 'border-primary-400/40',
                    'border_selected' => 'border-primary-300/60',
                    'ring_color' => 'ring-primary-400/30'
                ],
                'done' => [
                    'text' => __('Done'),
                    'text_ar' => 'منجز',
                    'icon' => 'circle-check',
                    'icon_bg' => 'bg-green-500/20',
                    'icon_color' => 'text-green-400',
                    'bg' => 'bg-gradient-to-br from-green-500/5 to-green-600/5',
                    'bg_hover' => 'bg-gradient-to-br from-green-500/15 to-green-600/15',
                    'bg_selected' => 'bg-gradient-to-br from-green-400/20 to-green-500/20',
                    'text_color' => 'text-green-300',
                    'border' => 'border-green-500/20',
                    'border_hover' => 'border-green-400/40',
                    'border_selected' => 'border-green-300/60',
                    'ring_color' => 'ring-green-400/30'
                ],
                'rejected' => [
                    'text' => __('Rejected'),
                    'text_ar' => 'مرفوض',
                    'icon' => 'circle-xmark',
                    'icon_bg' => 'bg-red-500/20',
                    'icon_color' => 'text-red-400',
                    'bg' => 'bg-gradient-to-br from-red-500/5 to-red-600/5',
                    'bg_hover' => 'bg-gradient-to-br from-red-500/15 to-red-600/15',
                    'bg_selected' => 'bg-gradient-to-br from-red-400/20 to-red-500/20',
                    'text_color' => 'text-red-300',
                    'border' => 'border-red-500/20',
                    'border_hover' => 'border-red-400/40',
                    'border_selected' => 'border-red-300/60',
                    'ring_color' => 'ring-red-400/30'
                ],
            ];
        @endphp
        
        @foreach($allStatuses as $statusKey => $statusConfig)
            <button
                @click="changeStatus('{{ $statusKey }}')"
                :disabled="!canChangeTo('{{ $statusKey }}') || isLoading"
                :class="{
                    '{{ $statusConfig['bg'] }} {{ $statusConfig['text_color'] }} {{ $statusConfig['border'] }}': currentStatus !== '{{ $statusKey }}',
                    '{{ $statusConfig['bg_selected'] }} {{ $statusConfig['text_color'] }} {{ $statusConfig['border_selected'] }}': currentStatus === '{{ $statusKey }}' && !isLoading,
                    'opacity-50 cursor-not-allowed grayscale': !canChangeTo('{{ $statusKey }}') || isLoading,
                    'cursor-pointer hover:{{ $statusConfig['bg_hover'] }} hover:{{ $statusConfig['border_hover'] }} hover:shadow-lg hover:-translate-y-1 active:translate-y-0': canChangeTo('{{ $statusKey }}') && !isLoading && currentStatus !== '{{ $statusKey }}',
                    'ring-4 {{ $statusConfig['ring_color'] }}': currentStatus === '{{ $statusKey }}'
                }"
                class="status-button relative rounded-xl md:rounded-2xl p-4 sm:p-5 md:p-6 border-2 transition-all duration-300 flex flex-col items-center justify-center gap-3 sm:gap-4 min-h-[110px] sm:min-h-[130px] md:min-h-[150px] overflow-hidden group backdrop-blur-sm"
            >
                <!-- Animated background gradient for selected -->
                <div 
                    x-show="currentStatus === '{{ $statusKey }}'"
                    class="absolute inset-0 {{ $statusConfig['bg_selected'] }} status-selected-bg"
                ></div>
                
                <!-- Glowing border animation for selected status -->
                <div 
                    x-show="currentStatus === '{{ $statusKey }}'"
                    class="absolute inset-0 rounded-xl md:rounded-2xl border-2 {{ $statusConfig['border_selected'] }} status-selected status-{{ $statusKey }}-glow"
                ></div>
                
                <!-- Icon Container with background -->
                <div class="relative z-10 transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 {{ $statusConfig['icon_bg'] }} rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-{{ $statusConfig['icon'] }} {{ $statusConfig['icon_color'] }} text-lg sm:text-xl md:text-2xl"></i>
                    </div>
                </div>
                
                <!-- Status Text -->
                <div class="relative z-10 text-center px-2">
                    <p class="font-bold text-sm sm:text-base md:text-lg leading-tight" x-text="'{{ app()->getLocale() === 'ar' ? $statusConfig['text_ar'] : $statusConfig['text'] }}'"></p>
                </div>
                
                <!-- Current Status Badge -->
                <div 
                    x-show="currentStatus === '{{ $statusKey }}'"
                    class="absolute top-2 {{ app()->getLocale() === 'ar' ? 'left-2' : 'right-2' }} z-10"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-0"
                    x-transition:enter-end="opacity-100 scale-100"
                >
                    <span class="bg-gradient-to-r from-primary-400 to-primary-500 text-white text-[10px] sm:text-xs font-semibold px-2 sm:px-2.5 py-1 sm:py-1.5 rounded-full flex items-center gap-1.5 shadow-lg">
                        <i class="fas fa-check-circle text-[10px] sm:text-xs"></i>
                        <span class="hidden sm:inline">{{ __('Current') }}</span>
                    </span>
                </div>
                
                <!-- Loading Spinner -->
                <div 
                    x-show="isLoading && selectedStatus === '{{ $statusKey }}'"
                    class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-md rounded-xl md:rounded-2xl z-20"
                    x-transition
                >
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-spinner fa-spin {{ $statusConfig['icon_color'] }} text-2xl sm:text-3xl"></i>
                        <span class="text-white text-xs">{{ __('Updating') }}...</span>
                    </div>
                </div>
                
                <!-- Disabled overlay with icon -->
                <div 
                    x-show="!canChangeTo('{{ $statusKey }}') && currentStatus !== '{{ $statusKey }}'"
                    class="absolute inset-0 bg-black/30 backdrop-blur-sm rounded-xl md:rounded-2xl z-10 flex items-center justify-center"
                >
                    <i class="fas fa-lock text-gray-500 text-lg"></i>
                </div>
                
                <!-- Hover effect overlay -->
                <div 
                    x-show="canChangeTo('{{ $statusKey }}') && currentStatus !== '{{ $statusKey }}'"
                    class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl md:rounded-2xl z-0"
                ></div>
            </button>
        @endforeach
    </div>
    
    <!-- Status Change Notes Modal -->
    <div 
        x-show="showNotesModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.away="closeNotesModal()"
        x-transition
    >
        <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full" @click.stop>
            <h3 class="text-xl font-bold text-white mb-4">{{ __('Change Status') }}</h3>
            
            <div class="mb-4">
                <label class="block text-gray-300 text-sm mb-2">
                    <span x-show="selectedStatus === 'rejected'">{{ __('Rejection Reason') }}</span>
                    <span x-show="selectedStatus === 'done'">{{ __('Completion Notes') }}</span>
                    <span x-show="selectedStatus !== 'rejected' && selectedStatus !== 'done'">{{ __('Notes') }}</span>
                    <span x-show="selectedStatus === 'rejected'" class="text-red-400">*</span>
                </label>
                <textarea 
                    x-model="statusNotes"
                    :required="selectedStatus === 'rejected'"
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    :placeholder="selectedStatus === 'rejected' ? '{{ __('Please enter rejection reason (required)') }}' : '{{ __('Optional notes') }}'"
                ></textarea>
            </div>
            
            <div class="flex items-center justify-end gap-3">
                <button 
                    @click="closeNotesModal()"
                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200"
                >
                    {{ __('Cancel') }}
                </button>
                <button 
                    @click="confirmStatusChange()"
                    class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                >
                    {{ __('Confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Description -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-4">وصف المهمة</h2>
        <p class="text-gray-300 leading-relaxed">
            {{ $task->description ?? 'لا يوجد وصف للمهمة' }}
        </p>
        @if($task->manager_notes)
        <div class="mt-4 pt-4 border-t border-white/10">
            <h3 class="text-lg font-bold text-white mb-2">ملاحظات مدير المشروع</h3>
            <p class="text-gray-300 text-sm">{{ $task->manager_notes }}</p>
        </div>
        @endif
    </div>

    <!-- Activity Timeline -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">سجل النشاط</h3>
        @if($task->notes->count() > 0)
        <div class="space-y-3">
            @foreach($task->notes as $index => $note)
                @php
                    $isLast = $loop->last;
                    $iconMap = [
                        'status_change' => ['icon' => 'edit', 'color' => 'bg-primary-500'],
                        'rejection' => ['icon' => 'xmark', 'color' => 'bg-red-500'],
                        'comment' => ['icon' => 'comment', 'color' => 'bg-blue-500'],
                        'attachment' => ['icon' => 'paperclip', 'color' => 'bg-purple-500'],
                        'reopen' => ['icon' => 'rotate-right', 'color' => 'bg-yellow-500'],
                        'assignment' => ['icon' => 'plus', 'color' => 'bg-green-500'],
                    ];
                    $icon = $iconMap[$note->action_type] ?? ['icon' => 'circle', 'color' => 'bg-gray-500'];
                @endphp
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 {{ $icon['color'] }} rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $icon['icon'] }} text-white text-xs"></i>
                        </div>
                        @if(!$isLast)
                        <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                        @endif
                    </div>
                    <div class="flex-1 pb-3">
                        <p class="text-white text-sm font-semibold">{{ $note->action_description }}</p>
                        @if($note->notes)
                        <p class="text-gray-300 text-sm mt-1">{{ $note->notes }}</p>
                        @endif
                        <p class="text-gray-400 text-xs">بواسطة: {{ $note->user->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $note->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-400 text-sm text-center py-4">لا توجد أنشطة مسجلة</p>
        @endif
    </div>
</div>

<!-- Project Stages with Tasks -->
@if($task->project && $task->projectStage)
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <h2 class="text-xl font-bold text-white mb-6">{{ __('Project Stages & Tasks') }}</h2>
    <div class="space-y-6">
        @php
            $stage = $task->projectStage;
            $statusConfig = [
                'جديد' => ['bg' => 'bg-gray-600', 'icon' => 'circle', 'badge' => 'bg-gray-500/20 text-gray-400'],
                'جارٍ' => ['bg' => 'bg-yellow-500', 'icon' => 'clock', 'badge' => 'bg-yellow-500/20 text-yellow-400'],
                'مكتمل' => ['bg' => 'bg-green-500', 'icon' => 'check', 'badge' => 'bg-green-500/20 text-green-400'],
            ];
            $config = $statusConfig[$stage->status] ?? $statusConfig['جديد'];
        @endphp
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 {{ $config['bg'] }} rounded-full flex items-center justify-center">
                        <i class="fas fa-{{ $config['icon'] }} text-white"></i>
                    </div>
                </div>
                <div class="flex-1 pb-6">
                    <div class="bg-white/5 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-white font-semibold">{{ $stage->stage_name }}</h3>
                            <span class="{{ $config['badge'] }} px-2 py-1 rounded text-xs font-semibold">{{ $stage->status }}</span>
                        </div>
                        
                        @if($stage->tasks->count() > 0)
                        <div class="space-y-2 mt-3">
                            @foreach($stage->tasks as $stageTask)
                                @php
                                    $taskStatusMap = [
                                        'new' => ['text' => 'جديد', 'class' => 'bg-gray-500/20 text-gray-400', 'icon' => 'circle'],
                                        'in_progress' => ['text' => 'قيد التنفيذ', 'class' => 'bg-primary-400/20 text-primary-400', 'icon' => 'clock'],
                                        'done' => ['text' => 'منجز', 'class' => 'bg-green-500/20 text-green-400', 'icon' => 'check'],
                                        'rejected' => ['text' => 'مرفوض', 'class' => 'bg-red-500/20 text-red-400', 'icon' => 'xmark'],
                                    ];
                                    $taskStatus = $taskStatusMap[$stageTask->status] ?? ['text' => $stageTask->status, 'class' => 'bg-gray-500/20 text-gray-400', 'icon' => 'circle'];
                                    $isCurrentTask = $stageTask->id == $task->id;
                                @endphp
                                <a href="{{ route('tasks.show', $stageTask->id) }}" class="block {{ $isCurrentTask ? 'bg-primary-500/10 border-primary-500/50' : 'bg-white/5 border-white/5' }} hover:bg-white/10 rounded-lg p-3 border transition-all duration-200 group">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                @if($isCurrentTask)
                                                    <i class="fas fa-hand-point-left text-primary-400 text-xs"></i>
                                                @endif
                                                @php
                                                    $iconColorMapTask = [
                                                        'new' => 'text-gray-400',
                                                        'in_progress' => 'text-primary-400',
                                                        'done' => 'text-green-400',
                                                        'rejected' => 'text-red-400',
                                                    ];
                                                    $iconColorTask = $iconColorMapTask[$stageTask->status] ?? 'text-gray-400';
                                                @endphp
                                                <i class="fas fa-{{ $taskStatus['icon'] }} text-xs {{ $iconColorTask }}"></i>
                                                <h4 class="text-white text-sm font-semibold group-hover:text-primary-400 transition-colors {{ $isCurrentTask ? 'text-primary-300' : '' }}">
                                                    {{ $stageTask->title }}
                                                    @if($isCurrentTask)
                                                        <span class="text-primary-400 text-xs">(المهمة الحالية)</span>
                                                    @endif
                                                </h4>
                                            </div>
                                            @if($stageTask->assignee)
                                            <p class="text-gray-400 text-xs flex items-center gap-1">
                                                <i class="fas fa-user"></i>
                                                {{ $stageTask->assignee->name }}
                                            </p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="{{ $taskStatus['class'] }} px-2 py-1 rounded text-xs font-semibold whitespace-nowrap">
                                                {{ $taskStatus['text'] }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-400 text-sm text-center py-2 mt-3">
                            <i class="fas fa-inbox ml-2"></i>
                            لا توجد مهام في هذه المرحلة
                        </p>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</div>
@endif

<!-- Comments Section -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">الملاحظات</h2>
        <button onclick="openCommentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-comment-dots ml-2"></i>
            إضافة ملاحظة
        </button>
    </div>
    @if($task->notes->where('action_type', 'comment')->count() > 0)
    <div class="space-y-4">
        @foreach($task->notes->where('action_type', 'comment') as $comment)
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-white font-semibold">{{ $comment->user->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <p class="text-gray-300 text-sm mt-2">{{ $comment->notes }}</p>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-400 text-sm text-center py-4">لا توجد ملاحظات</p>
    @endif
</div>

<!-- Attachments -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
    @if($task->attachments->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($task->attachments as $attachment)
            @php
                $fileExtension = strtolower(pathinfo($attachment->name, PATHINFO_EXTENSION));
                $iconConfig = [
                    'pdf' => ['icon' => 'file-pdf', 'color' => 'bg-red-500/20 text-red-400'],
                    'jpg' => ['icon' => 'file-image', 'color' => 'bg-blue-500/20 text-blue-400'],
                    'jpeg' => ['icon' => 'file-image', 'color' => 'bg-blue-500/20 text-blue-400'],
                    'png' => ['icon' => 'file-image', 'color' => 'bg-blue-500/20 text-blue-400'],
                    'gif' => ['icon' => 'file-image', 'color' => 'bg-blue-500/20 text-blue-400'],
                    'dwg' => ['icon' => 'file-alt', 'color' => 'bg-purple-500/20 text-purple-400'],
                ];
                $fileType = $iconConfig[$fileExtension] ?? ['icon' => 'file', 'color' => 'bg-gray-500/20 text-gray-400'];
                
                // Format file size
                $fileSize = $attachment->file_size;
                $formattedSize = $fileSize;
                if ($fileSize >= 1024 * 1024) {
                    $formattedSize = number_format($fileSize / (1024 * 1024), 2) . ' MB';
                } elseif ($fileSize >= 1024) {
                    $formattedSize = number_format($fileSize / 1024, 2) . ' KB';
                } else {
                    $formattedSize = $fileSize . ' Bytes';
                }
            @endphp
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-12 h-12 {{ $fileType['color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $fileType['icon'] }} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-semibold text-sm truncate" title="{{ $attachment->name }}">
                                {{ $attachment->name }}
                            </p>
                            <p class="text-gray-400 text-xs">{{ $formattedSize }}</p>
                            @if($attachment->uploaded_by)
                            <p class="text-gray-400 text-xs mt-1">
                                <i class="fas fa-user ml-1"></i>
                                {{ $attachment->uploader->name ?? 'غير معروف' }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ $attachment->url }}" target="_blank" class="flex-1 px-3 py-2 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm text-center transition-all duration-200">
                        <i class="fas fa-eye ml-1"></i>
                        عرض
                    </a>
                    <a href="{{ $attachment->url }}" download class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded text-sm text-center transition-all duration-200">
                        <i class="fas fa-download ml-1"></i>
                        تحميل
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-paperclip text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">لا توجد مرفقات</h3>
        <p class="text-gray-400">لم يتم رفع أي مرفقات لهذه المهمة</p>
    </div>
    @endif
</div>

<!-- Status Update Modal -->
@include('components.modals.task-update')

<!-- Comment Modal -->
@include('components.modals.task-comment')

@push('styles')
<style>
    @keyframes glow {
        0%, 100% {
            opacity: 1;
            box-shadow: 0 0 20px currentColor, 0 0 40px currentColor, inset 0 0 20px currentColor;
        }
        50% {
            opacity: 0.8;
            box-shadow: 0 0 30px currentColor, 0 0 60px currentColor, inset 0 0 30px currentColor;
        }
    }
    
    @keyframes pulse-bg {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .status-selected {
        animation: glow 2s ease-in-out infinite;
    }
    
    .status-selected-bg {
        animation: pulse-bg 3s ease-in-out infinite;
    }
    
    /* Custom glow colors for each status */
    .status-new-glow {
        box-shadow: 0 0 20px rgba(156, 163, 175, 0.7), 0 0 40px rgba(156, 163, 175, 0.5), inset 0 0 20px rgba(156, 163, 175, 0.2) !important;
    }
    
    .status-in-progress-glow {
        box-shadow: 0 0 20px rgba(29, 184, 248, 0.7), 0 0 40px rgba(29, 184, 248, 0.5), inset 0 0 20px rgba(29, 184, 248, 0.2) !important;
    }
    
    .status-done-glow {
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.7), 0 0 40px rgba(34, 197, 94, 0.5), inset 0 0 20px rgba(34, 197, 94, 0.2) !important;
    }
    
    .status-rejected-glow {
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.7), 0 0 40px rgba(239, 68, 68, 0.5), inset 0 0 20px rgba(239, 68, 68, 0.2) !important;
    }
    
    /* Button hover effects */
    .status-button {
        position: relative;
        transform-style: preserve-3d;
    }
    
    .status-button:hover:not(:disabled) {
        transform: translateY(-4px) scale(1.02);
    }
    
    .status-button:active:not(:disabled) {
        transform: translateY(-2px) scale(0.98);
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .status-button {
            min-height: 100px;
            padding: 0.75rem;
        }
    }
    
    @media (min-width: 641px) and (max-width: 768px) {
        .status-button {
            min-height: 120px;
        }
    }
    
    /* Smooth transitions */
    button[disabled] {
        pointer-events: none;
    }
    
    /* Icon container animation */
    .status-button:hover:not(:disabled) .fa-circle-dot,
    .status-button:hover:not(:disabled) .fa-clock-rotate-left,
    .status-button:hover:not(:disabled) .fa-circle-check,
    .status-button:hover:not(:disabled) .fa-circle-xmark {
        animation: bounce 0.6s ease-in-out;
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
function taskStatusControl(taskId, currentStatus) {
    return {
        taskId: taskId,
        currentStatus: currentStatus,
        selectedStatus: null,
        statusNotes: '',
        showNotesModal: false,
        isLoading: false,
        
        canChangeTo(newStatus) {
            // Basic rules - can always change to in_progress from new
            if (this.currentStatus === 'new' && newStatus === 'in_progress') {
                return true;
            }
            // Can change from in_progress to done or rejected
            if (this.currentStatus === 'in_progress' && (newStatus === 'done' || newStatus === 'rejected')) {
                return true;
            }
            // Can change from rejected back to new
            if (this.currentStatus === 'rejected' && newStatus === 'new') {
                return true;
            }
            // Can change from done back to in_progress (reopen)
            if (this.currentStatus === 'done' && newStatus === 'in_progress') {
                return true;
            }
            // Cannot change to same status
            if (this.currentStatus === newStatus) {
                return false;
            }
            // Default: allow change (backend will validate)
            return true;
        },
        
        changeStatus(newStatus) {
            if (!this.canChangeTo(newStatus) || this.isLoading) {
                return;
            }
            
            // If changing to rejected or done, show notes modal
            if (newStatus === 'rejected' || newStatus === 'done') {
                this.selectedStatus = newStatus;
                this.statusNotes = '';
                this.showNotesModal = true;
            } else {
                // Direct change for other statuses
                this.confirmStatusChange(newStatus);
            }
        },
        
        closeNotesModal() {
            this.showNotesModal = false;
            this.selectedStatus = null;
            this.statusNotes = '';
        },
        
        async confirmStatusChange(status = null) {
            const newStatus = status || this.selectedStatus;
            
            if (!newStatus) return;
            
            // Validate rejection reason
            if (newStatus === 'rejected' && !this.statusNotes.trim()) {
                alert('{{ __('Please enter rejection reason') }}');
                return;
            }
            
            this.isLoading = true;
            
            try {
                const formData = new FormData();
                formData.append('status', newStatus);
                formData.append('reason', newStatus === 'rejected' ? this.statusNotes : '');
                formData.append('completion_notes', newStatus === 'done' ? this.statusNotes : '');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                
                const response = await fetch(`/tasks/${this.taskId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.currentStatus = newStatus;
                    this.closeNotesModal();
                    
                    // Show success message
                    const statusMap = {
                        'new': '{{ __('New') }}',
                        'in_progress': '{{ __('In Progress') }}',
                        'done': '{{ __('Done') }}',
                        'rejected': '{{ __('Rejected') }}'
                    };
                    
                    // Reload page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    let errorMessage = '{{ __('An error occurred') }}';
                    try {
                        const error = await response.json();
                        errorMessage = error.message || errorMessage;
                    } catch (e) {
                        // If response is not JSON, use default message
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __('An error occurred') }}');
            } finally {
                this.isLoading = false;
            }
        }
    }
}

function openStatusModal(action = 'approve') {
    window.dispatchEvent(new CustomEvent('open-task-status-modal', { detail: { taskId: '{{ $task->id }}', action: action } }));
}

function openCommentModal() {
    window.dispatchEvent(new CustomEvent('open-task-comment-modal', { detail: { taskId: '{{ $task->id }}' } }));
}
</script>
@endpush

@endsection


