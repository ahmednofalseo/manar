<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">سجل الأنشطة</h2>

    @php
        // جمع جميع الأنشطة من مصادر مختلفة
        $activities = collect();
        
        // أنشطة المهام
        foreach ($project->tasks as $task) {
            foreach ($task->notes as $note) {
                $activities->push([
                    'type' => 'task',
                    'icon' => 'list-check',
                    'color' => 'bg-blue-500',
                    'title' => $note->action_description . ': ' . $task->title,
                    'user' => $note->user->name,
                    'date' => $note->created_at,
                ]);
            }
        }
        
        // أنشطة المرفقات
        foreach ($project->attachments as $attachment) {
            $activities->push([
                'type' => 'attachment',
                'icon' => 'paperclip',
                'color' => 'bg-green-500',
                'title' => 'تم رفع ملف: ' . $attachment->name,
                'user' => $attachment->uploader->name ?? 'غير معروف',
                'date' => $attachment->created_at,
            ]);
        }
        
        // أنشطة المراحل
        foreach ($project->projectStages as $stage) {
            if ($stage->updated_at != $stage->created_at) {
                $activities->push([
                    'type' => 'stage',
                    'icon' => 'diagram-project',
                    'color' => 'bg-purple-500',
                    'title' => 'تم تحديث مرحلة: ' . $stage->stage_name,
                    'user' => 'النظام',
                    'date' => $stage->updated_at,
                ]);
            }
        }
        
        // نشاط إنشاء المشروع
        $activities->push([
            'type' => 'project',
            'icon' => 'plus',
            'color' => 'bg-yellow-500',
            'title' => 'تم إنشاء المشروع',
            'user' => 'النظام',
            'date' => $project->created_at,
        ]);
        
        // ترتيب حسب التاريخ
        $activities = $activities->sortByDesc('date')->take(20);
    @endphp

    @if($activities->count() > 0)
    <!-- Activity Timeline -->
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
                        <p class="text-gray-400 text-sm">بواسطة: {{ $activity['user'] }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $activity['date']->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-clock-rotate-left text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">لا توجد أنشطة</h3>
        <p class="text-gray-400">ستظهر الأنشطة هنا عند القيام بأي إجراء على المشروع</p>
    </div>
    @endif
</div>
