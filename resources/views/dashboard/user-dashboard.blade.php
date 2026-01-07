@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<!-- Welcome Section -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-primary-400/20 flex items-center justify-center flex-shrink-0">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1db8f8&color=fff&size=80' }}" 
                     alt="{{ $user->name }}" 
                     class="w-full h-full rounded-full object-cover">
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-white mb-1">{{ __('Welcome') }}, {{ $user->name }}</h1>
                <p class="text-gray-400 text-sm">{{ $user->job_title ?? __('Team Member') }}</p>
            </div>
        </div>
        @if($userRank)
        <div class="flex items-center gap-3 px-4 py-2 bg-primary-400/20 rounded-lg border border-primary-400/30">
            <div class="text-center">
                <p class="text-primary-400 text-xs">{{ __('Your Rank') }}</p>
                <p class="text-white text-2xl font-bold">#{{ $userRank }}</p>
            </div>
            @if($userRank <= 3)
            <i class="fas fa-trophy text-yellow-400 text-2xl"></i>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- User Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Total Projects -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('My Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $userStats['total_projects'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-400 text-xs mt-2">{{ __('Projects you are working on') }}</p>
    </div>

    <!-- Total Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('My Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $userStats['total_tasks'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-list-check text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-1 mt-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ __('In Progress') }}</span>
                <span class="text-yellow-400 font-semibold">{{ $userStats['in_progress_tasks'] }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ __('New') }}</span>
                <span class="text-gray-300 font-semibold">{{ $userStats['new_tasks'] }}</span>
            </div>
        </div>
    </div>

    <!-- Completed Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Completed Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $userStats['done_tasks'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="mt-2">
            <div class="flex items-center justify-between mb-1">
                <span class="text-gray-400 text-xs">{{ __('Completion Rate') }}</span>
                <span class="text-green-400 font-bold text-sm">{{ $userStats['completion_rate'] }}%</span>
            </div>
            <div class="w-full bg-white/5 rounded-full h-2">
                <div class="bg-green-400 h-2 rounded-full transition-all" style="width: {{ $userStats['completion_rate'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Rejected Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Rejected Tasks') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $userStats['rejected_tasks'] }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-times-circle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-400 text-xs mt-2">{{ __('Tasks that were rejected') }}</p>
    </div>
</div>

<!-- Alerts Section -->
@if($userStats['overdue_tasks'] > 0 || $userStats['upcoming_tasks'] > 0)
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
    @if($userStats['overdue_tasks'] > 0)
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 border-2 border-red-400/40 bg-red-500/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">{{ __('Overdue Tasks') }}</h3>
                <p class="text-red-300 text-sm">{{ $userStats['overdue_tasks'] }} {{ __('tasks are overdue') }}</p>
            </div>
        </div>
        <a href="{{ route('tasks.index', ['status' => 'overdue']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-all text-sm">
            <span>{{ __('View Overdue Tasks') }}</span>
            <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"></i>
        </a>
    </div>
    @endif

    @if($userStats['upcoming_tasks'] > 0)
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 border-2 border-orange-400/40 bg-orange-500/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-orange-400 text-xl"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">{{ __('Upcoming Tasks') }}</h3>
                <p class="text-orange-300 text-sm">{{ $userStats['upcoming_tasks'] }} {{ __('tasks due soon') }}</p>
            </div>
        </div>
        <a href="{{ route('tasks.index', ['status' => 'upcoming']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-300 rounded-lg transition-all text-sm">
            <span>{{ __('View Upcoming Tasks') }}</span>
            <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"></i>
        </a>
    </div>
    @endif
</div>
@endif

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Left Column: My Projects -->
    <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <!-- My Projects -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('My Projects') }}</h2>
                <a href="{{ route('projects.index') }}" class="text-primary-400 hover:text-primary-300 text-xs md:text-sm">
                    <span class="hidden sm:inline">{{ __('View All') }}</span>
                    <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'mr-1' : 'ml-1' }}"></i>
                </a>
            </div>
            
            @if($userProjects->count() > 0)
            <div class="space-y-3">
                @foreach($userProjects->take(6) as $project)
                @php
                    $userTasks = $project->tasks->where('assignee_id', $user->id);
                    $userDoneTasks = $userTasks->where('status', 'done')->count();
                    $userTotalTasks = $userTasks->count();
                    $userProgress = $userTotalTasks > 0 ? round(($userDoneTasks / $userTotalTasks) * 100) : 0;
                    
                    // Determine user's role in project
                    $userRole = 'member';
                    $roleLabel = __('Member');
                    if ($project->project_manager_id == $user->id) {
                        $userRole = 'manager';
                        $roleLabel = __('Project Manager');
                    } elseif ($project->team_members && is_array($project->team_members) && in_array($user->id, $project->team_members)) {
                        $userRole = 'team_member';
                        $roleLabel = __('Team Member');
                    }
                @endphp
                <div class="bg-white/5 rounded-lg md:rounded-xl p-4 border border-white/10 hover:border-primary-400/40 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3 gap-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold mb-1">{{ $project->name }}</h3>
                            <p class="text-gray-400 text-xs mb-2">{{ $project->city ?? 'غير محدد' }} - {{ $project->district ?? 'غير محدد' }}</p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-1 bg-primary-400/20 text-primary-400 text-xs rounded">
                                    <i class="fas fa-user-tie {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                    {{ $roleLabel }}
                                </span>
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded">
                                    {{ $userTotalTasks }} {{ __('tasks') }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('projects.show', $project->id) }}" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-400">{{ __('My Progress') }}</span>
                            <span class="text-white font-semibold">{{ $userProgress }}%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="bg-primary-400 h-2 rounded-full transition-all" style="width: {{ $userProgress }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>{{ $userDoneTasks }} {{ __('completed') }}</span>
                            <span>{{ $userTotalTasks - $userDoneTasks }} {{ __('remaining') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <i class="fas fa-folder-open text-gray-500 text-4xl mb-3"></i>
                <p class="text-gray-400">{{ __('No projects assigned to you yet') }}</p>
            </div>
            @endif
        </div>

        <!-- Projects with Overdue Tasks -->
        @if($projectsWithOverdueTasks->count() > 0)
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 border-2 border-red-400/40">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Projects with Overdue Tasks') }}</h2>
            </div>
            <div class="space-y-3">
                @foreach($projectsWithOverdueTasks->take(3) as $project)
                @php
                    $overdueTasks = $project->tasks->where('assignee_id', $user->id)
                        ->where('due_date', '<', now())
                        ->whereNotIn('status', ['done', 'rejected']);
                    $daysOverdue = $overdueTasks->map(function($task) {
                        return now()->diffInDays($task->due_date);
                    })->max();
                @endphp
                <div class="bg-red-500/10 rounded-lg p-3 border border-red-400/30">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-semibold text-sm">{{ $project->name }}</h3>
                        <span class="text-red-400 text-xs font-bold">{{ $overdueTasks->count() }} {{ __('tasks') }}</span>
                    </div>
                    <p class="text-red-300 text-xs">{{ __('Delayed by') }}: {{ $daysOverdue }} {{ __('days') }}</p>
                    <a href="{{ route('projects.show', $project->id) }}" class="text-red-300 hover:text-red-200 text-xs mt-2 inline-flex items-center gap-1">
                        <span>{{ __('View Project') }}</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Projects with Upcoming Tasks -->
        @if($projectsWithUpcomingTasks->count() > 0)
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 border-2 border-orange-400/40">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-orange-400"></i>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Projects with Upcoming Tasks') }}</h2>
            </div>
            <div class="space-y-3">
                @foreach($projectsWithUpcomingTasks->take(3) as $project)
                @php
                    $upcomingTasks = $project->tasks->where('assignee_id', $user->id)
                        ->where('due_date', '>=', now())
                        ->where('due_date', '<=', now()->addDays(7))
                        ->whereNotIn('status', ['done', 'rejected']);
                @endphp
                <div class="bg-orange-500/10 rounded-lg p-3 border border-orange-400/30">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-semibold text-sm">{{ $project->name }}</h3>
                        <span class="text-orange-400 text-xs font-bold">{{ $upcomingTasks->count() }} {{ __('tasks') }}</span>
                    </div>
                    <p class="text-orange-300 text-xs">{{ __('Tasks due within 7 days') }}</p>
                    <a href="{{ route('projects.show', $project->id) }}" class="text-orange-300 hover:text-orange-200 text-xs mt-2 inline-flex items-center gap-1">
                        <span>{{ __('View Project') }}</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Leaderboard & Motivation -->
    <div class="space-y-4 md:space-y-6">
        <!-- Leaderboard -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-white">{{ __('Top Performers') }}</h2>
                <i class="fas fa-trophy text-yellow-400"></i>
            </div>
            <div class="space-y-3">
                @foreach($leaderboard->take(5) as $index => $performer)
                @php
                    $isCurrentUser = $performer['user']->id === $user->id;
                @endphp
                <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border {{ $isCurrentUser ? 'border-primary-400/50 bg-primary-500/10' : 'border-white/10' }}">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-400/20 flex items-center justify-center {{ $index < 3 ? 'bg-yellow-500/20' : '' }}">
                        @if($index === 0)
                            <i class="fas fa-crown text-yellow-400 text-sm"></i>
                        @elseif($index === 1)
                            <i class="fas fa-medal text-gray-300 text-sm"></i>
                        @elseif($index === 2)
                            <i class="fas fa-award text-orange-400 text-sm"></i>
                        @else
                            <span class="text-white text-xs font-bold">#{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-semibold truncate {{ $isCurrentUser ? 'text-primary-400' : '' }}">
                            {{ $performer['user']->name }}
                            @if($isCurrentUser)
                            <span class="text-xs">({{ __('You') }})</span>
                            @endif
                        </p>
                        <p class="text-gray-400 text-xs">{{ $performer['completed_tasks'] }}/{{ $performer['total_tasks'] }} {{ __('tasks') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-primary-400 font-bold text-sm">{{ $performer['completion_rate'] }}%</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Motivation Card -->
        @if($userStats['completion_rate'] < 100)
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 bg-gradient-to-br from-primary-500/20 to-blue-500/20 border border-primary-400/30">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-400/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-fire text-primary-400 text-2xl"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ __('Keep Going!') }}</h3>
                <p class="text-gray-300 text-sm mb-4">
                    {{ __('You have completed') }} {{ $userStats['done_tasks'] }} {{ __('out of') }} {{ $userStats['total_tasks'] }} {{ __('tasks') }}
                </p>
                <div class="w-full bg-white/10 rounded-full h-3 mb-2">
                    <div class="bg-primary-400 h-3 rounded-full transition-all" style="width: {{ $userStats['completion_rate'] }}%"></div>
                </div>
                <p class="text-primary-300 text-xs">
                    {{ __('Only') }} {{ $userStats['total_tasks'] - $userStats['done_tasks'] }} {{ __('tasks remaining to reach 100%!') }}
                </p>
            </div>
        </div>
        @elseif($userStats['total_tasks'] > 0)
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-400/30">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-500/30 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-star text-yellow-400 text-2xl"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">{{ __('Excellent Work!') }}</h3>
                <p class="text-gray-300 text-sm mb-2">
                    {{ __('You have completed all your tasks!') }}
                </p>
                <p class="text-green-300 text-xs">
                    {{ __('Keep up the great work!') }}
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

