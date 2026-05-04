<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-white">{{ __('Tasks') }} ({{ $project->tasks->count() }})</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                <i class="fas fa-plus ml-2"></i>
                {{ __('Create task') }}
            </a>
        </div>
    </div>

    @if($project->tasks->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Task') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Stage') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Assignee') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Due date') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Progress') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->tasks as $task)
                    @php
                        $statusMap = [
                            'new' => ['text' => __('Task status new'), 'class' => 'bg-gray-500/20 text-gray-400'],
                            'in_progress' => ['text' => __('Task status in progress'), 'class' => 'bg-primary-400/20 text-primary-400'],
                            'done' => ['text' => __('Task status done'), 'class' => 'bg-green-500/20 text-green-400'],
                            'rejected' => ['text' => __('Task status rejected'), 'class' => 'bg-red-500/20 text-red-400'],
                        ];
                        $status = $statusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400'];
                    @endphp
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3">
                            <p class="text-white text-sm font-semibold">{{ $task->display_title }}</p>
                            @if($task->description)
                            <p class="text-gray-400 text-xs mt-1">{{ \Illuminate\Support\Str::limit($task->description, 50) }}</p>
                            @endif
                        </td>
                        <td class="py-3 text-gray-300 text-sm">
                            @php $stageName = optional($task->projectStage)->stage_name; @endphp
                            {{ $stageName ? ($stageLabelMap[$stageName] ?? $stageName) : __('Not specified') }}
                        </td>
                        <td class="py-3 text-gray-300 text-sm">
                            {{ optional($task->assignee)->name ?? __('Not specified') }}
                        </td>
                        <td class="py-3 text-gray-300 text-sm">
                            {{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}
                        </td>
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
    <div class="text-center py-12">
        <i class="fas fa-list-check text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">{{ __('No tasks yet') }}</h3>
        <p class="text-gray-400 mb-4">{{ __('Start by creating a task') }}</p>
        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-plus ml-2"></i>
            {{ __('Create task') }}
        </a>
    </div>
    @endif
</div>
