@extends('layouts.dashboard')

@section('title', __('User Profile') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('User Profile'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .kpi-card {
        transition: all 0.3s ease;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(29, 184, 248, 0.2);
    }
</style>
@endpush

@section('content')
@php
    use App\Helpers\PermissionHelper;
    $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1db8f8&color=fff&size=128';
@endphp

<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }}">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <div class="flex items-center gap-4">
        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-4 border-primary-400/50 object-cover">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $user->job_title ?? __('No Job Title') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @can('update', $user)
        <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-pen {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Edit') }}
        </a>
        @endcan
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Performance KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Total Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalTasks) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-list-check text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Completed Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Completed Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($completedTasks) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="pt-3 border-t border-white/10">
            <p class="text-gray-300 text-xs">{{ __('Completion Rate') }}: <span class="text-green-400 font-bold">{{ $completionRate }}%</span></p>
        </div>
    </div>
    
    <!-- Overdue Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Overdue Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($overdueTasks->count()) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Projects -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalProjects) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="pt-3 border-t border-white/10">
            <p class="text-gray-300 text-xs">{{ __('Active') }}: <span class="text-primary-400 font-bold">{{ $activeProjects }}</span> | {{ __('Completed') }}: <span class="text-green-400 font-bold">{{ $completedProjects }}</span></p>
        </div>
    </div>
</div>

<!-- Main Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Email') }}</p>
            <p class="text-white font-semibold">{{ $user->email }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Phone') }}</p>
            <p class="text-white font-semibold">{{ $user->phone ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('National ID') }}</p>
            <p class="text-white font-semibold">{{ $user->national_id ?? '-' }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Roles') }}</p>
            <div class="flex flex-wrap gap-2 mt-1">
                @forelse($user->roles as $role)
                    <span class="px-3 py-1 bg-primary-400/20 text-primary-400 rounded-lg text-sm font-semibold">{{ $role->display_name }}</span>
                @empty
                    <span class="text-gray-400 text-sm">{{ __('No roles assigned') }}</span>
                @endforelse
            </div>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Status') }}</p>
            <span class="inline-block {{ $user->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }} px-3 py-1 rounded-lg text-sm font-semibold">
                {{ $user->status === 'active' ? __('Active') : __('Suspended') }}
            </span>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Last Login') }}</p>
            <p class="text-white font-semibold">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : __('Never') }}</p>
        </div>
        @if($user->practice_license_no)
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Practice License No') }}</p>
            <p class="text-white font-semibold">{{ $user->practice_license_no }}</p>
        </div>
        @endif
        @if($user->engineer_rank_expiry)
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Engineer Rank Expiry') }}</p>
            <p class="text-white font-semibold">{{ $user->engineer_rank_expiry->format('Y-m-d') }}</p>
        </div>
        @endif
        <div>
            <p class="text-gray-400 text-sm mb-1">{{ __('Created At') }}</p>
            <p class="text-white font-semibold">{{ $user->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="userTabs()">
    <!-- Tab Headers -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-white/10 pb-4">
        <button 
            @click="activeTab = 'projects'"
            :class="activeTab === 'projects' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-diagram-project {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Projects') }}
            <span class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} bg-white/10 px-2 py-0.5 rounded text-xs">{{ $totalProjects }}</span>
        </button>
        <button 
            @click="activeTab = 'tasks'"
            :class="activeTab === 'tasks' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-tasks {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Assigned Tasks') }}
            <span class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} bg-white/10 px-2 py-0.5 rounded text-xs">{{ $totalTasks }}</span>
        </button>
        <button 
            @click="activeTab = 'overdue'"
            :class="activeTab === 'overdue' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-exclamation-triangle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Overdue Tasks') }}
            <span class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs">{{ $overdueTasks->count() }}</span>
        </button>
        <button 
            @click="activeTab = 'created'"
            :class="activeTab === 'created' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-plus-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Created Tasks') }}
            <span class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} bg-white/10 px-2 py-0.5 rounded text-xs">{{ $createdTasks->count() }}</span>
        </button>
        <button 
            @click="activeTab = 'login'"
            :class="activeTab === 'login' ? 'bg-primary-400/20 text-primary-400 border-primary-400' : 'text-gray-400 hover:text-white'"
            class="px-4 py-2 border-b-2 border-transparent transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-sign-in-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Login History') }}
        </button>
    </div>

    <!-- Tab Content -->
    <!-- Projects Tab -->
    <div x-show="activeTab === 'projects'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">{{ __('User Projects') }}</h3>
            @can('create', \App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('New Project') }}
            </a>
            @endcan
        </div>
        @forelse($userProjects as $project)
            @php
                $role = $projectRoles[$project->id] ?? 'member';
                $roleText = '';
                $roleColor = '';
                if ($role === 'manager') {
                    $roleText = __('Project Manager');
                    $roleColor = 'bg-primary-400/20 text-primary-400';
                } elseif ($role === 'team_member') {
                    $roleText = __('Team Member');
                    $roleColor = 'bg-blue-500/20 text-blue-400';
                } else {
                    $roleText = __('Member');
                    $roleColor = 'bg-gray-500/20 text-gray-400';
                }
                $userTasksInProject = $project->tasks->where('assignee_id', $user->id);
                $userCompletedTasksInProject = $userTasksInProject->where('status', 'done')->count();
                $userTotalTasksInProject = $userTasksInProject->count();
            @endphp
            <div class="bg-white/5 rounded-lg md:rounded-xl p-4 border border-white/10 hover:border-primary-400/40 transition-all duration-200">
                <div class="flex items-start justify-between mb-3 gap-2">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-white font-semibold truncate mb-1">{{ $project->name }}</h4>
                        <p class="text-gray-400 text-sm truncate">
                            {{ $project->city ?? __('Not specified') }} - {{ $project->district ?? __('Not specified') }} | {{ $project->owner ?? __('Not specified') }}
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs whitespace-nowrap {{ $roleColor }}">{{ $roleText }}</span>
                </div>
                <div class="flex items-center gap-4 mb-3">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="relative w-10 h-10 md:w-12 md:h-12 flex-shrink-0">
                            <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="none"/>
                                <circle cx="24" cy="24" r="20" stroke="#1db8f8" stroke-width="4" fill="none" 
                                        stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * ($project->progress ?? 0) / 100) }}" stroke-linecap="round"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold">{{ $project->progress ?? 0 }}%</span>
                        </div>
                        <span class="text-gray-400 text-xs truncate">{{ $project->current_stage ?? __('No stage') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-white/10 flex-wrap">
                    <span class="text-gray-400 text-xs whitespace-nowrap">
                        <i class="fas fa-list-check {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $userCompletedTasksInProject }} / {{ $userTotalTasksInProject }} {{ __('Tasks Completed') }}
                    </span>
                    <a href="{{ route('projects.show', $project->id) }}" class="text-primary-400 hover:text-primary-300 text-xs">
                        <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('View Details') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">
                {{ __('No projects found') }}
            </div>
        @endforelse
    </div>

    <!-- Tasks Tab -->
    <div x-show="activeTab === 'tasks'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">{{ __('Assigned Tasks') }}</h3>
            @can('create', \App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('New Task') }}
            </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Task Title') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Project') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Stage') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Due Date') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedTasks as $task)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                            <td class="py-3 text-white text-sm font-semibold">{{ $task->title }}</td>
                            <td class="py-3 text-gray-300 text-sm">{{ $task->project->name ?? '-' }}</td>
                            <td class="py-3 text-gray-300 text-sm">{{ $task->projectStage->stage_name ?? '-' }}</td>
                            <td class="py-3">
                                @php
                                    $statusMap = [
                                        'new' => ['text' => __('New'), 'class' => 'bg-blue-500/20 text-blue-400'],
                                        'in_progress' => ['text' => __('In Progress'), 'class' => 'bg-yellow-500/20 text-yellow-400'],
                                        'done' => ['text' => __('Done'), 'class' => 'bg-green-500/20 text-green-400'],
                                        'rejected' => ['text' => __('Rejected'), 'class' => 'bg-red-500/20 text-red-400'],
                                    ];
                                    $status = $statusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $status['class'] }}">{{ $status['text'] }}</span>
                            </td>
                            <td class="py-3 text-gray-300 text-sm">
                                @if($task->due_date)
                                    {{ $task->due_date->format('Y-m-d') }}
                                    @if($task->due_date < now() && !in_array($task->status, ['done', 'rejected']))
                                        <span class="text-red-400 text-xs {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}">({{ __('Overdue') }})</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">{{ __('No tasks found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overdue Tasks Tab -->
    <div x-show="activeTab === 'overdue'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">{{ __('Overdue Tasks') }}</h3>
        </div>
        @forelse($overdueTasks as $task)
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h4 class="text-white font-semibold mb-1">{{ $task->title }}</h4>
                        <p class="text-gray-300 text-sm">{{ $task->project->name ?? __('No Project') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">
                        {{ __('Overdue') }}: {{ $task->due_date->diffForHumans() }}
                    </span>
                </div>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-red-500/20">
                    <span class="text-gray-300 text-xs">
                        <i class="fas fa-calendar {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('Due Date') }}: {{ $task->due_date->format('Y-m-d') }}
                    </span>
                    <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-400 hover:text-primary-300 text-xs">
                        <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('View Task') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">
                <i class="fas fa-check-circle text-green-400 text-4xl mb-4"></i>
                <p>{{ __('No overdue tasks') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Created Tasks Tab -->
    <div x-show="activeTab === 'created'" class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">{{ __('Created Tasks') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Task Title') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Project') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Assignee') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Created At') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($createdTasks as $task)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                            <td class="py-3 text-white text-sm font-semibold">{{ $task->title }}</td>
                            <td class="py-3 text-gray-300 text-sm">{{ $task->project->name ?? '-' }}</td>
                            <td class="py-3 text-gray-300 text-sm">{{ $task->assignee->name ?? '-' }}</td>
                            <td class="py-3">
                                @php
                                    $statusMap = [
                                        'new' => ['text' => __('New'), 'class' => 'bg-blue-500/20 text-blue-400'],
                                        'in_progress' => ['text' => __('In Progress'), 'class' => 'bg-yellow-500/20 text-yellow-400'],
                                        'done' => ['text' => __('Done'), 'class' => 'bg-green-500/20 text-green-400'],
                                        'rejected' => ['text' => __('Rejected'), 'class' => 'bg-red-500/20 text-red-400'],
                                    ];
                                    $status = $statusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $status['class'] }}">{{ $status['text'] }}</span>
                            </td>
                            <td class="py-3 text-gray-300 text-sm">{{ $task->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3">
                                <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">{{ __('No tasks found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Login History Tab -->
    <div x-show="activeTab === 'login'" class="space-y-4">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Login History') }}</h3>
        @forelse($loginLogs as $log)
            <div class="flex gap-3 pb-3 border-b border-white/10 last:border-0">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sign-in-alt text-green-400 text-xs"></i>
                    </div>
                    @if(!$loop->last)
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-semibold">{{ __('Login') }}</p>
                    <p class="text-gray-400 text-xs mt-1">
                        <i class="fas fa-clock {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ $log['last_activity'] }}
                    </p>
                    @if($log['ip_address'])
                    <p class="text-gray-400 text-xs mt-1">
                        <i class="fas fa-network-wired {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('IP Address') }}: {{ $log['ip_address'] }}
                    </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">
                <i class="fas fa-history text-gray-500 text-4xl mb-4"></i>
                <p>{{ __('No login history found') }}</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function userTabs() {
    return {
        activeTab: 'projects'
    }
}
</script>
@endpush

@endsection
