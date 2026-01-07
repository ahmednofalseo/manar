<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">المراحل</h2>
        @can('update', $project)
        <button onclick="openStageModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            تحديث مرحلة
        </button>
        @endcan
    </div>

    @if($project->projectStages->count() > 0)
    <!-- Timeline -->
    <div class="space-y-6">
        @foreach($project->projectStages as $index => $stage)
            @php
                $isLast = $loop->last;
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
                    @if(!$isLast)
                    <div class="w-0.5 h-full bg-gray-600 mt-2"></div>
                    @endif
                </div>
                <div class="flex-1 pb-6">
                    <div class="bg-white/5 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-white font-semibold">{{ $stage->stage_name }}</h3>
                            <span class="{{ $config['badge'] }} px-2 py-1 rounded text-xs">{{ $stage->status }}</span>
                        </div>
                        
                        @if($stage->start_date)
                        <p class="text-gray-400 text-sm mb-2">تاريخ البدء: {{ $stage->start_date->format('Y-m-d') }}</p>
                        @endif
                        
                        @if($stage->end_date)
                        <p class="text-gray-400 text-sm mb-2">
                            @if($stage->status === 'مكتمل')
                                تاريخ الانتهاء: {{ $stage->end_date->format('Y-m-d') }}
                            @else
                                تاريخ الانتهاء المتوقع: {{ $stage->end_date->format('Y-m-d') }}
                            @endif
                        </p>
                        @endif
                        
                        @if($stage->progress > 0)
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-gray-400 text-xs">التقدم</span>
                                <span class="text-white text-xs font-semibold">{{ $stage->progress }}%</span>
                            </div>
                            <div class="w-full bg-white/5 rounded-full h-2">
                                <div class="bg-primary-400 h-2 rounded-full" style="width: {{ $stage->progress }}%"></div>
                            </div>
                        </div>
                        @endif
                        
                        @if($stage->notes)
                        <p class="text-gray-300 text-sm mb-3">{{ $stage->notes }}</p>
                        @endif
                        
                        @if($stage->tasks->count() > 0)
                        <div class="mt-4 pt-4 border-t border-white/10">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2 text-sm text-gray-400">
                                    <i class="fas fa-list-check"></i>
                                    <span>{{ $stage->tasks->count() }} مهمة</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                @foreach($stage->tasks as $task)
                                    @php
                                        $taskStatusMap = [
                                            'new' => ['text' => 'جديد', 'class' => 'bg-gray-500/20 text-gray-400', 'icon' => 'circle'],
                                            'in_progress' => ['text' => 'قيد التنفيذ', 'class' => 'bg-primary-400/20 text-primary-400', 'icon' => 'clock'],
                                            'done' => ['text' => 'منجز', 'class' => 'bg-green-500/20 text-green-400', 'icon' => 'check'],
                                            'rejected' => ['text' => 'مرفوض', 'class' => 'bg-red-500/20 text-red-400', 'icon' => 'xmark'],
                                        ];
                                        $taskStatus = $taskStatusMap[$task->status] ?? ['text' => $task->status, 'class' => 'bg-gray-500/20 text-gray-400', 'icon' => 'circle'];
                                    @endphp
                                    <a href="{{ route('tasks.show', $task->id) }}" class="block bg-white/5 hover:bg-white/10 rounded-lg p-3 border border-white/5 transition-all duration-200 group">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    @php
                                                        $iconColorMap = [
                                                            'new' => 'text-gray-400',
                                                            'in_progress' => 'text-primary-400',
                                                            'done' => 'text-green-400',
                                                            'rejected' => 'text-red-400',
                                                        ];
                                                        $iconColor = $iconColorMap[$task->status] ?? 'text-gray-400';
                                                    @endphp
                                                    <i class="fas fa-{{ $taskStatus['icon'] }} text-xs {{ $iconColor }}"></i>
                                                    <h4 class="text-white text-sm font-semibold group-hover:text-primary-400 transition-colors truncate">{{ $task->title }}</h4>
                                                </div>
                                                @if($task->assignee)
                                                <p class="text-gray-400 text-xs flex items-center gap-1">
                                                    <i class="fas fa-user"></i>
                                                    {{ $task->assignee->name }}
                                                </p>
                                                @endif
                                                @if($task->due_date)
                                                <p class="text-gray-400 text-xs flex items-center gap-1 mt-1">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $task->due_date->format('Y-m-d') }}
                                                    @if($task->due_date->isPast() && !in_array($task->status, ['done', 'rejected']))
                                                        <span class="text-red-400">(متأخرة)</span>
                                                    @endif
                                                </p>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <span class="{{ $taskStatus['class'] }} px-2 py-1 rounded text-xs font-semibold whitespace-nowrap">
                                                    {{ $taskStatus['text'] }}
                                                </span>
                                                @if($task->progress > 0)
                                                <div class="w-12 h-12 relative">
                                                    <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                                                        <circle cx="18" cy="18" r="16" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
                                                        <circle 
                                                            cx="18" 
                                                            cy="18" 
                                                            r="16" 
                                                            fill="none" 
                                                            stroke="{{ $task->status === 'done' ? '#10b981' : '#1db8f8' }}" 
                                                            stroke-width="2"
                                                            stroke-dasharray="{{ $task->progress }}, 100"
                                                            stroke-linecap="round"
                                                        />
                                                    </svg>
                                                    <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-semibold">{{ $task->progress }}%</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="mt-4 pt-4 border-t border-white/10">
                            <p class="text-gray-400 text-sm text-center py-2">
                                <i class="fas fa-inbox ml-2"></i>
                                لا توجد مهام في هذه المرحلة
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-diagram-project text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-white mb-2">لا توجد مراحل</h3>
        <p class="text-gray-400 mb-4">قم بإضافة مراحل للمشروع من صفحة التعديل</p>
        <a href="{{ route('projects.edit', $project->id) }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-edit ml-2"></i>
            تعديل المشروع
        </a>
    </div>
    @endif
</div>

<!-- Stage Update Modal -->
<div id="stageModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center" onclick="closeStageModal(event)">
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-white">تحديث مرحلة</h3>
            <button onclick="closeStageModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('projects.stage.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">المرحلة</label>
                    <select name="stage_name" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option value="">اختر المرحلة</option>
                        @foreach($project->projectStages as $stage)
                            <option value="{{ $stage->stage_name }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                    <select name="status" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option value="جديد">جديد</option>
                        <option value="جارٍ">جارٍ</option>
                        <option value="مكتمل">مكتمل</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">تاريخ البدء</label>
                        <input type="date" name="start_date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">تاريخ الانتهاء</label>
                        <input type="date" name="end_date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">نسبة التقدم (%)</label>
                    <input type="number" name="progress" min="0" max="100" value="0" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                </div>
                
                <div>
                    <label class="block text-gray-300 text-sm mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"></textarea>
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="closeStageModal()" class="flex-1 px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStageModal() {
    document.getElementById('stageModal').classList.remove('hidden');
    document.getElementById('stageModal').classList.add('flex');
}

function closeStageModal(event) {
    if (!event || event.target === event.currentTarget || event.target.closest('button')) {
        document.getElementById('stageModal').classList.add('hidden');
        document.getElementById('stageModal').classList.remove('flex');
    }
}
</script>
