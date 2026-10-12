@php
    $stageStatusConfig = [
        'locked' => ['badge' => 'bg-gray-500/20 text-gray-400', 'icon' => 'lock'],
        'active' => ['badge' => 'bg-yellow-500/20 text-yellow-400', 'icon' => 'clock'],
        'awaiting_payment' => ['badge' => 'bg-orange-500/20 text-orange-400', 'icon' => 'file-invoice-dollar'],
        'completed' => ['badge' => 'bg-green-500/20 text-green-400', 'icon' => 'check'],
    ];
    $taskStatusMap = [
        'new' => ['text' => __('Task status new'), 'class' => 'bg-gray-500/20 text-gray-400'],
        'in_progress' => ['text' => __('Task status in progress'), 'class' => 'bg-primary-400/20 text-primary-400'],
        'done' => ['text' => __('Task status done'), 'class' => 'bg-green-500/20 text-green-400'],
        'rejected' => ['text' => __('Task status rejected'), 'class' => 'bg-red-500/20 text-red-400'],
    ];
    $unassignedTasks = $project->tasks->whereNull('project_stage_id');
@endphp

<div class="space-y-4 md:space-y-6">
    @forelse($project->projectStages as $stage)
        @php
            $statusValue = $stage->status instanceof \App\Enums\ProjectStageStatus ? $stage->status->value : (string) $stage->status;
            $statusLabel = $stage->status instanceof \App\Enums\ProjectStageStatus ? $stage->status->label() : $statusValue;
            $config = $stageStatusConfig[$statusValue] ?? $stageStatusConfig['locked'];
            $stageTasks = $stage->tasks;
            $isActive = $statusValue === 'active';
        @endphp
        <section class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 {{ $isActive ? 'ring-1 ring-yellow-500/40' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-bold text-white">{{ $stageLabelMap[$stage->stage_name] ?? $stage->stage_name }}</h2>
                        <span class="{{ $config['badge'] }} px-2 py-1 rounded text-xs font-semibold whitespace-nowrap">
                            <i class="fas fa-{{ $config['icon'] }} {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mt-1">{{ __('Tasks in stage count', ['count' => $stageTasks->count()]) }}</p>
                </div>
                @if($isActive)
                <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm whitespace-nowrap">
                    <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Create task') }}
                </a>
                @endif
            </div>

            @if($stageTasks->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-right border-b border-white/10">
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Task') }}</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Assignee') }}</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Due date') }}</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Progress') }}</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stageTasks as $task)
                            @php $status = $taskStatusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400']; @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                                <td class="py-3">
                                    <p class="text-white text-sm font-semibold">{{ $task->display_title }}</p>
                                    @if($task->description)
                                    <p class="text-gray-400 text-xs mt-1">{{ \Illuminate\Support\Str::limit($task->description, 50) }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-gray-300 text-sm">{{ optional($task->assignee)->name ?? __('Not specified') }}</td>
                                <td class="py-3 text-gray-300 text-sm">{{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-white/5 rounded-full h-2 w-24">
                                            <div class="bg-primary-400 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                                        </div>
                                        <span class="text-gray-400 text-xs">{{ $task->progress }}%</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="text-primary-400 hover:text-primary-300" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $task)
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-400 hover:text-blue-300" title="{{ __('Edit') }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400 text-sm text-center py-6">{{ __('No tasks in this stage') }}</p>
            @endif
        </section>
    @empty
        <div class="glass-card rounded-xl md:rounded-2xl p-8 text-center">
            <i class="fas fa-diagram-project text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">{{ __('No stages yet') }}</h3>
            <p class="text-gray-400">{{ __('Add stages from edit hint') }}</p>
        </div>
    @endforelse

    @if($unassignedTasks->isNotEmpty())
    <section class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-lg font-bold text-white mb-4">{{ __('Not specified') }}</h2>
        <div class="space-y-2">
            @foreach($unassignedTasks as $task)
            <a href="{{ route('tasks.show', $task->id) }}" class="block text-white text-sm bg-white/5 hover:bg-white/10 rounded-lg px-3 py-2">
                {{ $task->display_title }}
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
