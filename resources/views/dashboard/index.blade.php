@extends('layouts.dashboard')

@section('title', __('Dashboard') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Dashboard'))

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
    
    @media (max-width: 640px) {
        .kpi-card:hover {
            transform: none;
        }
    }
    
    /* Responsive table wrapper */
    .table-wrapper {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .table-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    
    .table-wrapper::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }
    
    .table-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    
    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

@section('content')

@if(!$isAdmin)
<!-- User Dashboard (Non-Admin) -->
@include('dashboard.user-dashboard')
@else
<!-- Admin Dashboard -->
<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-3 md:p-4 mb-4 md:mb-6">
    <form method="GET" action="{{ route('dashboard.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('City') }}</label>
                <select name="city" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('All Cities') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('Owner') }}</label>
                <select name="owner" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('All Owners') }}</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner }}" {{ request('owner') == $owner ? 'selected' : '' }}>{{ $owner }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="قيد التنفيذ" {{ request('status') == 'قيد التنفيذ' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                    <option value="مكتمل" {{ request('status') == 'مكتمل' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="متوقف" {{ request('status') == 'متوقف' ? 'selected' : '' }}>{{ __('Stopped') }}</option>
                </select>
            </div>
            
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('Engineer') }}</label>
                <select name="engineer_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('All Engineers') }}</option>
                    @foreach($engineers as $engineer)
                        <option value="{{ $engineer->id }}" {{ request('engineer_id') == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('From Date') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            
            <div class="sm:col-span-1">
                <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">{{ __('To Date') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            
            <div class="sm:col-span-1 sm:col-start-1 lg:col-start-auto flex items-end gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-4 md:px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                    <i class="fas fa-search {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Search') }}
                </button>
                <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- KPI 1: Total Projects -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalProjects) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex-1 bg-white/5 rounded-full h-2">
                <div class="bg-primary-400 h-2 rounded-full" style="width: {{ round($avgProgress) }}%"></div>
            </div>
            <span class="text-primary-400 text-sm font-semibold" style="color: #4787a7 !important;">{{ round($avgProgress) }}%</span>
        </div>
        <p class="text-gray-400 text-xs mt-2">{{ __('Average Progress') }}</p>
    </div>
    
    <!-- KPI 2: Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalTasks) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-list-check text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">{{ __('Tasks In Progress') }}</span>
                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">{{ number_format($tasksInProgress) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">{{ __('Completed Tasks') }}</span>
                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">{{ number_format($tasksDone) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">{{ __('Overdue Tasks') }}</span>
                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs">{{ number_format($tasksOverdue) }}</span>
            </div>
        </div>
    </div>
    
    <!-- KPI 3: Collections -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Collections This Month') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($collectedThisMonth, 2) }}</h3>
                <p class="text-gray-400 text-xs mt-1">ر.س</p>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">{{ __('Total Due') }}</span>
                <span class="text-white font-semibold">{{ number_format($totalInvoices, 2) }} {{ app()->getLocale() === 'ar' ? 'ر.س' : 'SAR' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">{{ __('Remaining') }}</span>
                <span class="text-red-400 font-semibold">{{ number_format($totalDue, 2) }} {{ app()->getLocale() === 'ar' ? 'ر.س' : 'SAR' }}</span>
            </div>
        </div>
    </div>
    
    <!-- KPI 4: Clients -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Active Clients') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($activeClients) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-tie text-purple-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">{{ __('New This Month') }}</span>
                <span class="bg-primary-400/20 text-primary-400 px-2 py-1 rounded text-xs" style="color: #4787a7 !important;">+{{ number_format($newClientsThisMonth) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">{{ __('Pending Approvals') }}</span>
                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">{{ number_format($pendingApprovals) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Left Column: Projects Grid -->
    <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <!-- Projects Grid -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Projects') }}</h2>
                <a href="{{ route('projects.index') }}" class="text-primary-400 hover:text-primary-300 text-xs md:text-sm">
                    <span class="hidden sm:inline">{{ __('View All') }}</span>
                    <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                @forelse($recentProjects as $project)
                    @php
                        // استخدام العلاقات المحملة مسبقاً بدلاً من استعلامات جديدة
                        $currentStage = $project->projectStages->firstWhere('status', 'in_progress');
                        $stageProgress = $currentStage ? $currentStage->progress : 0;
                        $attachmentsCount = $project->attachments ? $project->attachments->count() : 0;
                        $tasksCount = $project->tasks ? $project->tasks->count() : 0;
                    @endphp
                    <div class="bg-white/5 rounded-lg md:rounded-xl p-3 md:p-4 border border-white/10 hover:border-primary-400/40 transition-all duration-200">
                        <div class="flex items-start justify-between mb-3 gap-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-semibold truncate">{{ $project->name }}</h3>
                                <p class="text-gray-400 text-sm mt-1 truncate">
                                    {{ $project->city ?? 'غير محدد' }} - {{ $project->district ?? 'غير محدد' }} | {{ $project->owner ?? 'غير محدد' }}
                                </p>
                            </div>
                            <span class="bg-primary-400/20 text-primary-400 px-2 py-1 rounded text-xs whitespace-nowrap flex-shrink-0">{{ $project->type ?? 'غير محدد' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2 md:gap-4 mb-4">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <div class="relative w-10 h-10 md:w-12 md:h-12 flex-shrink-0">
                                    <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 48 48">
                                        <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="none"/>
                                        <circle cx="24" cy="24" r="20" stroke="#1db8f8" stroke-width="4" fill="none" 
                                                stroke-dasharray="125.6" stroke-dashoffset="{{ 125.6 - (125.6 * ($project->progress ?? 0) / 100) }}" stroke-linecap="round"/>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold">{{ $project->progress ?? 0 }}%</span>
                                </div>
                                <span class="text-gray-400 text-xs truncate">{{ $currentStage->stage_name ?? 'بدون مرحلة' }}</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('projects.show', $project->id) }}" class="flex-1 min-w-[80px] bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 px-2 md:px-3 py-1.5 rounded text-xs transition-all duration-200 text-center whitespace-nowrap">
                                <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                <span class="hidden xs:inline">{{ __('View') }}</span>
                            </a>
                            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="flex-1 min-w-[80px] bg-white/5 hover:bg-white/10 text-white px-2 md:px-3 py-1.5 rounded text-xs transition-all duration-200 text-center whitespace-nowrap">
                                <i class="fas fa-tasks {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                <span class="hidden xs:inline">{{ __('Task') }}</span>
                            </a>
                            @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
                            <a href="{{ route('financials.index', ['project_id' => $project->id]) }}" class="flex-1 min-w-[80px] bg-white/5 hover:bg-white/10 text-white px-2 md:px-3 py-1.5 rounded text-xs transition-all duration-200 text-center whitespace-nowrap">
                                <i class="fas fa-file-invoice {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                <span class="hidden xs:inline">{{ __('Invoice') }}</span>
                            </a>
                            @endif
                            <a href="{{ route('projects.show', $project->id) }}" class="bg-white/5 hover:bg-white/10 text-white px-2 md:px-3 py-1.5 rounded text-xs transition-all duration-200 flex-shrink-0">
                                <i class="fas fa-paperclip"></i>
                            </a>
                        </div>
                        
                        <div class="flex items-center gap-2 md:gap-4 mt-3 pt-3 border-t border-white/10 flex-wrap">
                            <span class="text-gray-400 text-xs whitespace-nowrap">
                                <i class="fas fa-file {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $attachmentsCount }} {{ __('attachments') }}
                            </span>
                            <span class="text-gray-400 text-xs whitespace-nowrap">
                                <i class="fas fa-tasks {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                {{ $tasksCount }} {{ __('tasks') }}
                            </span>
                            @if($project->status === 'مكتمل')
                            <span class="text-green-400 text-xs flex-shrink-0">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center text-gray-400 py-8">{{ __('No projects found') }}</div>
                @endforelse
            </div>
        </div>
        
        <!-- Tasks Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-3">
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Upcoming Tasks') }}</h2>
                <a href="{{ route('tasks.create') }}" class="w-full sm:w-auto bg-primary-500 hover:bg-primary-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm transition-all duration-200 inline-block text-center">
                    <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                    {{ __('Create Task') }}
                </a>
            </div>
            
            <div class="overflow-x-auto -mx-4 md:mx-0 table-wrapper">
                <div class="min-w-full inline-block px-4 md:px-0">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="text-right border-b border-white/10">
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-4">{{ __('Task') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-4 hidden md:table-cell">{{ __('Assignee') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-4 hidden lg:table-cell">{{ __('Due Date') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-4">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingTasks as $task)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer" onclick="window.location.href='{{ route('tasks.show', $task->id) }}'">
                                    <td class="py-2 md:py-3 text-white text-xs md:text-sm px-2 md:px-4">
                                        <div class="flex flex-col">
                                            <span class="truncate max-w-[200px] md:max-w-none">{{ $task->title }}</span>
                                            <span class="text-gray-400 text-xs md:hidden mt-1">{{ $task->assignee->name ?? 'غير محدد' }} • {{ $task->due_date ? $task->due_date->format('Y-m-d') : 'غير محدد' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-4 hidden md:table-cell truncate max-w-[150px]">{{ $task->assignee->name ?? 'غير محدد' }}</td>
                                    <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-4 hidden lg:table-cell whitespace-nowrap">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'غير محدد' }}</td>
                                    <td class="py-2 md:py-3 px-2 md:px-4">
                                        <span class="px-2 py-1 rounded text-xs whitespace-nowrap
                                            @if($task->status === 'done') bg-green-500/20 text-green-400
                                            @elseif($task->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                                            @elseif($task->status === 'new') bg-blue-500/20 text-blue-400
                                            @elseif($task->status === 'rejected') bg-red-500/20 text-red-400
                                            @else bg-gray-500/20 text-gray-400
                                            @endif">
                                            @if($task->status === 'done') {{ __('Done') }}
                                            @elseif($task->status === 'in_progress') {{ __('In Progress') }}
                                            @elseif($task->status === 'new') {{ __('New') }}
                                            @elseif($task->status === 'rejected') {{ __('Rejected') }}
                                            @else {{ $task->status }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">{{ __('No upcoming tasks') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                     </table>
                 </div>
             </div>
         </div>
         
                   <!-- Invoices Widget -->
          @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
          <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
             <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-3">
                 <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Invoices & Payments') }}</h2>
                 <a href="{{ route('financials.create') }}" class="w-full sm:w-auto bg-primary-500 hover:bg-primary-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm transition-all duration-200 inline-block text-center">
                    <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                    {{ __('Create Invoice') }}
                </a>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-2 gap-2">
                    <span class="text-gray-300 text-xs md:text-sm">{{ __('Collected') }}</span>
                    <span class="text-white font-semibold text-xs md:text-sm break-all sm:break-normal">{{ number_format($totalCollected, 2) }} / {{ number_format($totalInvoices, 2) }} ر.س</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-2 md:h-3">
                    <div class="bg-primary-400 h-2 md:h-3 rounded-full transition-all duration-300" style="width: {{ $totalInvoices > 0 ? round(($totalCollected / $totalInvoices) * 100) : 0 }}%"></div>
                </div>
                <p class="text-gray-400 text-xs mt-2">{{ __('Remaining') }}: <span class="text-red-400">{{ number_format($totalDue, 2) }} ر.س</span></p>
            </div>
            
            <!-- Invoices Table -->
            <div class="overflow-x-auto -mx-4 md:mx-0 table-wrapper">
                <div class="min-w-full inline-block px-4 md:px-0">
                    <table class="w-full min-w-[700px]">
                        <thead>
                            <tr class="text-right border-b border-white/10">
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">{{ __('Invoice Number') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4 hidden sm:table-cell">{{ __('Client') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4 hidden md:table-cell">{{ __('Project') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">{{ __('Amount') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">{{ __('Status') }}</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $invoice)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer" onclick="window.location.href='{{ route('financials.show', $invoice->id) }}'">
                                    <td class="py-3 text-white text-xs md:text-sm px-2 md:px-4">
                                        <div class="flex flex-col">
                                            <span class="truncate max-w-[120px]">{{ $invoice->number }}</span>
                                            <span class="text-gray-400 text-xs sm:hidden mt-1">{{ $invoice->client->name ?? 'غير محدد' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-gray-300 text-xs md:text-sm px-2 md:px-4 hidden sm:table-cell truncate max-w-[150px]">{{ $invoice->client->name ?? 'غير محدد' }}</td>
                                    <td class="py-3 text-gray-300 text-xs md:text-sm px-2 md:px-4 hidden md:table-cell truncate max-w-[150px]">{{ $invoice->project->name ?? 'غير محدد' }}</td>
                                    <td class="py-3 text-white font-semibold text-xs md:text-sm px-2 md:px-4 whitespace-nowrap">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                                    <td class="py-3 px-2 md:px-4">
                                        <span class="px-2 py-1 rounded text-xs whitespace-nowrap
                                            @if($invoice->status->value === 'paid') bg-green-500/20 text-green-400
                                            @elseif($invoice->status->value === 'partial') bg-yellow-500/20 text-yellow-400
                                            @elseif($invoice->status->value === 'unpaid') bg-red-500/20 text-red-400
                                            @elseif($invoice->status->value === 'overdue') bg-orange-500/20 text-orange-400
                                            @endif">
                                            {{ $invoice->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 md:px-4">
                                        <a href="{{ route('financials.pdf', $invoice->id) }}" target="_blank" class="text-primary-400 hover:text-primary-300 text-sm md:text-base" onclick="event.stopPropagation()">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">{{ __('No invoices found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
          @endif
    </div>
    
    <!-- Right Column: Widgets -->
    <div class="space-y-4 md:space-y-6">
        <!-- Completion Rate Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h2 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">{{ __('Completion Rate') }}</h2>
            <div class="flex justify-center overflow-hidden">
                <canvas id="completionChart" width="200" height="200" class="max-w-full h-auto w-full max-w-[200px]"></canvas>
            </div>
            <p class="text-center text-gray-400 text-xs md:text-sm mt-3 md:mt-4">{{ __('Project Distribution') }}</p>
        </div>
        
        <!-- Top Performers Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h2 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">{{ __('Top Performers') }}</h2>
            <div class="space-y-3">
                @forelse($topPerformers as $index => $performer)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-400 font-bold" style="color: #4787a7 !important;">{{ $index + 1 }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-white text-sm font-semibold truncate">{{ $performer['user']->name }}</p>
                                <p class="text-gray-400 text-xs truncate">{{ $performer['user']->job_title ?? 'مهندس' }}</p>
                            </div>
                        </div>
                        <span class="text-green-400 font-bold whitespace-nowrap ml-2">{{ $performer['completion_rate'] }}%</span>
                    </div>
                @empty
                    <div class="text-center text-gray-400 text-sm py-4">{{ __('No data available') }}</div>
                @endforelse
            </div>
        </div>
        
        <!-- Clients Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-bold text-white">{{ __('Clients') }}</h2>
                <a href="{{ route('clients.create') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-2 md:px-3 py-1 md:py-1.5 rounded-lg text-xs transition-all duration-200 inline-block">
                    <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                    <span class="hidden sm:inline">{{ __('Add') }}</span>
                </a>
            </div>
            <div class="mb-3 md:mb-4">
                <p class="text-2xl md:text-3xl font-bold text-white break-words">{{ number_format($activeClients) }}</p>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Active Clients') }}</p>
                <p class="text-primary-400 text-xs mt-1 break-words" style="color: #4787a7 !important;">+{{ number_format($newClientsThisMonth) }} {{ __('New This Month') }}</p>
            </div>
            <div class="space-y-2">
                @forelse($recentClientActivities as $activity)
                    <div class="p-3 bg-white/5 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-400 mt-1 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm truncate">{{ $activity['message'] }}</p>
                                <p class="text-gray-400 text-xs mt-1">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 text-sm py-4">{{ __('No recent activities') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-2 sm:p-4" onclick="closeModalOnBackdrop(event, 'taskModal')">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-md w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-lg md:text-xl font-bold text-white">{{ __('Create New Task') }}</h3>
            <button onclick="closeModal('taskModal')" class="text-gray-400 hover:text-white p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Task Name') }}</label>
                <input type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Description') }}</label>
                <textarea class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" rows="3"></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Project') }}</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>{{ __('Select Project') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Assignee') }}</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>{{ __('Select Assignee') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Due Date') }}</label>
                    <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Priority') }}</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>{{ __('Normal') }}</option>
                        <option>{{ __('High') }}</option>
                        <option>{{ __('Low') }}</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="button" onclick="closeModal('taskModal')" class="flex-1 bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-2 sm:p-4" onclick="closeModalOnBackdrop(event, 'invoiceModal')">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-md w-full max-h-[95vh] sm:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-lg md:text-xl font-bold text-white">{{ __('Create New Invoice') }}</h3>
            <button onclick="closeModal('invoiceModal')" class="text-gray-400 hover:text-white p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Client') }}</label>
                <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option>{{ __('Select Client') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Project') }}</label>
                <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option>{{ __('Select Project') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Amount') }}</label>
                <input type="number" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" placeholder="0.00">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Due Date') }}</label>
                <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="button" onclick="closeModal('invoiceModal')" class="flex-1 bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Completion Chart
    const ctx = document.getElementById('completionChart');
    if (ctx) {
        const projectStatusData = @json($projectStatusDistribution);
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['{{ __('Completed') }}', '{{ __('In Progress') }}', '{{ __('Stopped') }}'],
                datasets: [{
                    data: [
                        projectStatusData['مكتمل'] || 0,
                        projectStatusData['قيد التنفيذ'] || 0,
                        projectStatusData['متوقف'] || 0
                    ],
                    backgroundColor: [
                        '#1db8f8',
                        '#4787a7',
                        '#6b7280'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff',
                            padding: 15
                        }
                    }
                }
            }
        });
    }
    
    // Modal Functions
    function openTaskModal() {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('taskModal').classList.add('flex');
    }
    
    function openInvoiceModal() {
        document.getElementById('invoiceModal').classList.remove('hidden');
        document.getElementById('invoiceModal').classList.add('flex');
    }
    
    function openClientModal() {
        // Implement later
        showToast('سيتم تنفيذ هذه الوظيفة قريباً', 'info');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(modalId).classList.remove('flex');
    }
    
    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            closeModal(modalId);
        }
    }
    
    // Toast Function
    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${colors[type]} text-white`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
</script>
@endpush

@endif
@endsection
