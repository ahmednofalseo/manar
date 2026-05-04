<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">{{ __('Activity log') }}</h2>

    @php
        $activities = collect();

        foreach ($project->tasks as $task) {
            foreach ($task->notes as $note) {
                $activities->push([
                    'type' => 'task',
                    'icon' => 'list-check',
                    'color' => 'bg-blue-500',
                    'title' => $note->action_description . ': ' . $task->display_title,
                    'user' => optional($note->user)->name ?? __('Unknown user'),
                    'date' => $note->created_at,
                ]);
            }
        }

        foreach ($project->attachments as $attachment) {
            $activities->push([
                'type' => 'attachment',
                'icon' => 'paperclip',
                'color' => 'bg-green-500',
                'title' => __('Activity file uploaded', ['name' => $attachment->name]),
                'user' => $attachment->uploader->name ?? __('Unknown user'),
                'date' => $attachment->created_at,
            ]);
        }

        foreach ($project->projectStages as $stage) {
            if ($stage->updated_at != $stage->created_at) {
                $activities->push([
                    'type' => 'stage',
                    'icon' => 'diagram-project',
                    'color' => 'bg-purple-500',
                    'title' => __('Activity stage updated', ['stage' => $stageLabelMap[$stage->stage_name] ?? $stage->stage_name]),
                    'user' => __('System'),
                    'date' => $stage->updated_at,
                ]);
            }
        }

        $activities->push([
            'type' => 'project',
            'icon' => 'plus',
            'color' => 'bg-yellow-500',
            'title' => __('Activity project created'),
            'user' => __('System'),
            'date' => $project->created_at,
        ]);

        $activities = $activities->sortByDesc('date')->take(20);
    @endphp

    @if($activities->count() > 0)
    <div class="space-y-4">
        @foreach($activities as $index => $activity)
            @php
                $isLast = $loop->last;
                $diff = $activity['date']->diffForHumans();
            @endphp
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 {{ $activity['color'] }} rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $activity['icon'] }} text-white text-xs"></i>
                    </div>
                    @if(!$isLast)
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                    @endif
                </div>
                <div class="flex-1 pb-4">
                    <div class="bg-white/5 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-white font-semibold">{{ $activity['title'] }}</p>
                            <span class="text-gray-400 text-xs">{{ $diff }}</span>
                        </div>
                        <p class="text-gray-400 text-sm">{{ __('Activity by') }} {{ $activity['user'] }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $activity['date']->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-clock-rotate-left text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">{{ __('No activities') }}</h3>
        <p class="text-gray-400">{{ __('Activities empty hint') }}</p>
    </div>
    @endif
</div>
