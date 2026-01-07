<!-- Project Info Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">معلومات المشروع</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div>
            <p class="text-gray-400 text-sm mb-1">نوع المشروع</p>
            <p class="text-white font-semibold">{{ $project->type }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">المدينة / الحي</p>
            <p class="text-white font-semibold">{{ $project->city }}@if($project->district) / {{ $project->district }}@endif</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">المالك</p>
            <p class="text-white font-semibold">{{ $project->owner }}</p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-1">قيمة المشروع</p>
            <p class="text-white font-semibold">{{ number_format($project->value, 2) }} ر.س</p>
        </div>
        @if($project->contract_number)
        <div>
            <p class="text-gray-400 text-sm mb-1">رقم العقد</p>
            <p class="text-white font-semibold">{{ $project->contract_number }}</p>
        </div>
        @endif
        @if($project->land_number)
        <div>
            <p class="text-gray-400 text-sm mb-1">رقم/كود الأرض</p>
            <p class="text-white font-semibold">{{ $project->land_number }}</p>
        </div>
        @endif
        @if($project->baladi_request_number)
        <div>
            <p class="text-gray-400 text-sm mb-1">رقم طلب منصة بلدي</p>
            <p class="text-white font-semibold">{{ $project->baladi_request_number }}</p>
        </div>
        @endif
        @if($project->start_date)
        <div>
            <p class="text-gray-400 text-sm mb-1">تاريخ البدء</p>
            <p class="text-white font-semibold">{{ $project->start_date->format('Y-m-d') }}</p>
        </div>
        @endif
        @if($project->end_date)
        <div>
            <p class="text-gray-400 text-sm mb-1">تاريخ الانتهاء المتوقع</p>
            <p class="text-white font-semibold">{{ $project->end_date->format('Y-m-d') }}</p>
        </div>
        @endif
        <div>
            <p class="text-gray-400 text-sm mb-1">الحالة</p>
            @php
                $statusColors = [
                    'قيد التنفيذ' => 'bg-green-500/20 text-green-400',
                    'مكتمل' => 'bg-blue-500/20 text-blue-400',
                    'متوقف' => 'bg-red-500/20 text-red-400',
                    'ملغي' => 'bg-gray-500/20 text-gray-400',
                ];
                $statusColor = $statusColors[$project->status] ?? 'bg-gray-500/20 text-gray-400';
            @endphp
            <span class="inline-block px-3 py-1 rounded text-sm font-semibold {{ $statusColor }}">{{ $project->status }}</span>
        </div>
    </div>
    
    @if($project->internal_notes)
    <div class="mt-6 pt-6 border-t border-white/10">
        <p class="text-gray-400 text-sm mb-2">ملاحظات داخلية</p>
        <p class="text-white">{{ $project->internal_notes }}</p>
    </div>
    @endif
</div>

<!-- Progress Card -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <h2 class="text-xl font-bold text-white mb-6">التقدم والمرحلة الحالية</h2>
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="relative w-32 h-32">
            <svg class="transform -rotate-90 w-full h-full" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="16" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2"/>
                <circle 
                    cx="18" 
                    cy="18" 
                    r="16" 
                    fill="none" 
                    stroke="#1db8f8" 
                    stroke-width="2" 
                    stroke-dasharray="100" 
                    stroke-dashoffset="{{ 100 - $project->progress }}" 
                    stroke-linecap="round"
                />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-white font-bold text-2xl">{{ $project->progress }}%</span>
            </div>
        </div>
        <div class="flex-1">
            @if($project->current_stage)
            <p class="text-gray-400 text-sm mb-2">المرحلة الحالية</p>
            <span class="inline-block bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg text-sm font-semibold mb-4">{{ $project->current_stage }}</span>
            @endif
            @if($project->projectStages->count() > 0)
            <div class="space-y-2">
                @foreach($project->projectStages->take(5) as $stage)
                    @php
                        $stageStatusMap = [
                            'جديد' => ['icon' => 'circle', 'text' => 'جديد', 'color' => 'text-gray-400'],
                            'جارٍ' => ['icon' => 'clock', 'text' => 'جارٍ', 'color' => 'text-yellow-400'],
                            'مكتمل' => ['icon' => 'check', 'text' => '✓ مكتمل', 'color' => 'text-green-400'],
                        ];
                        $stageStatus = $stageStatusMap[$stage->status] ?? ['icon' => 'circle', 'text' => $stage->status, 'color' => 'text-gray-400'];
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300 text-sm">{{ $stage->stage_name }}</span>
                        <span class="{{ $stageStatus['color'] }} text-sm">{{ $stageStatus['text'] }}</span>
                    </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm">لا توجد مراحل محددة</p>
            @endif
        </div>
    </div>
</div>

<!-- Team Card -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
    @if($project->projectManager)
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">مدير المشروع</h3>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-primary-400/20 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-primary-400 text-xl"></i>
            </div>
            <div>
                <p class="text-white font-semibold">{{ $project->projectManager->name }}</p>
                <p class="text-gray-400 text-sm">{{ $project->projectManager->job_title ?? 'مدير مشروع' }}</p>
                @if($project->projectManager->email)
                <p class="text-gray-400 text-xs">{{ $project->projectManager->email }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @if($project->teamUsers->count() > 0)
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">الفريق ({{ $project->teamUsers->count() }})</h3>
        <div class="space-y-3 max-h-48 overflow-y-auto">
            @foreach($project->teamUsers as $member)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-400/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-primary-400"></i>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">{{ $member->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $member->job_title ?? 'عضو فريق' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
