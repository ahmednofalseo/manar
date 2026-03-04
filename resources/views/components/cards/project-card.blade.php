@php
    $typeColors = [
        'تصميم' => 'bg-green-500/20 text-green-400',
        'تصميم وإشراف' => 'bg-primary-400/20 text-primary-400',
        'إشراف' => 'bg-purple-500/20 text-purple-400',
        'تقرير فني' => 'bg-blue-500/20 text-blue-400',
        'تقرير دفاع مدني' => 'bg-yellow-500/20 text-yellow-400',
        'تصميم دفاع مدني' => 'bg-orange-500/20 text-orange-400',
        'تعديلات' => 'bg-pink-500/20 text-pink-400',
        'استشارات' => 'bg-cyan-500/20 text-cyan-400',
    ];
    
    $stageColors = [
        'معماري' => 'bg-blue-500/20 text-blue-400',
        'إنشائي' => 'bg-yellow-500/20 text-yellow-400',
        'كهربائي' => 'bg-green-500/20 text-green-400',
        'ميكانيكي' => 'bg-purple-500/20 text-purple-400',
        'تقديم للبلدية' => 'bg-green-500/20 text-green-400',
        'أخرى' => 'bg-gray-500/20 text-gray-400',
    ];
    
    $statusColors = [
        'قيد التنفيذ' => 'bg-green-500/20 text-green-400',
        'مكتمل' => 'bg-blue-500/20 text-blue-400',
        'متوقف' => 'bg-red-500/20 text-red-400',
        'ملغي' => 'bg-gray-500/20 text-gray-400',
    ];
    
    $typeColor = $typeColors[$project->type] ?? 'bg-primary-400/20 text-primary-400';
    $stageColor = $stageColors[$project->current_stage] ?? 'bg-gray-500/20 text-gray-400';
    $statusColor = $statusColors[$project->status] ?? 'bg-gray-500/20 text-gray-400';
    
    // حساب حالة المشروع
    $projectStatus = 'normal'; // normal, warning, danger
    $statusMessage = '';
    $daysDelay = 0;
    
    if ($project->end_date) {
        $today = now();
        $endDate = \Carbon\Carbon::parse($project->end_date);
        // استخدام diffInDays مع false للحصول على الفرق الموقع (سالب إذا انتهى التاريخ)
        $daysUntilEnd = $today->diffInDays($endDate, false);
        
        // عدد المهام غير المكتملة (استخدام العلاقة المحملة مسبقاً)
        $incompleteTasks = $project->tasks ? $project->tasks->whereNotIn('status', ['done', 'rejected'])->count() : 0;
        
        if ($daysUntilEnd < 0 && $incompleteTasks > 0) {
            // متأخر - تقريب إلى عدد صحيح
            $daysDelay = (int) floor(abs($daysUntilEnd));
            $statusMessage = 'متأخر: ' . $daysDelay . ' ' . ($daysDelay == 1 ? 'يوم' : 'أيام');
        } elseif ($daysUntilEnd <= 7 && $daysUntilEnd >= 0 && $incompleteTasks > 0) {
            // قريب من الانتهاء
            $projectStatus = 'warning';
            $statusMessage = __('Approaching deadline with incomplete tasks');
        }
    }
    
    // تحديد لون الحدود والرسالة
    $borderColor = 'border-white/10';
    $alertBg = '';
    if ($projectStatus === 'danger') {
        $borderColor = 'border-red-500/50';
        $alertBg = 'bg-red-500/10 border-red-500/30';
    } elseif ($projectStatus === 'warning') {
        $borderColor = 'border-orange-500/50';
        $alertBg = 'bg-orange-500/10 border-orange-500/30';
    }
@endphp

<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 hover:border-primary-400/40 transition-all duration-200 border-2 {{ $borderColor }}">
    <!-- Alert Message -->
    @if($projectStatus !== 'normal')
    <div class="mb-4 p-3 rounded-lg {{ $alertBg }} border">
        <div class="flex items-center gap-2">
            <i class="fas {{ $projectStatus === 'danger' ? 'fa-exclamation-circle text-red-400' : 'fa-exclamation-triangle text-orange-400' }}"></i>
            <span class="text-xs md:text-sm font-semibold {{ $projectStatus === 'danger' ? 'text-red-300' : 'text-orange-300' }}">
                {{ $statusMessage }}
            </span>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg md:text-xl font-bold text-white mb-1">{{ $project->name }}</h3>
            <p class="text-gray-400 text-xs md:text-sm">{{ $project->project_number ?? 'غير محدد' }}</p>
        </div>
        <span class="px-3 py-1 rounded-lg text-xs font-semibold whitespace-nowrap {{ $typeColor }}">
            {{ $project->type }}
        </span>
    </div>

    <!-- Location & Owner -->
    <div class="space-y-2 mb-4">
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-map-marker-alt text-primary-400"></i>
            <span>{{ $project->city }}@if($project->district) / {{ $project->district }}@endif</span>
        </div>
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-user text-primary-400"></i>
            <span>{{ $project->owner }}</span>
        </div>
        @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
        <div class="flex items-center gap-2 text-gray-300 text-sm">
            <i class="fas fa-money-bill-wave text-primary-400"></i>
            <span>{{ number_format($project->value, 2) }} ر.س</span>
        </div>
        @endif
        @if($project->start_date || $project->end_date)
        <div class="pt-2 border-t border-white/10 space-y-1.5">
            @if($project->start_date)
            <div class="flex items-center gap-2 text-gray-300 text-xs">
                <i class="fas fa-calendar-check text-green-400"></i>
                <span>{{ __('Start Date') }}: {{ $project->start_date->format('Y-m-d') }}</span>
            </div>
            @endif
            @if($project->end_date)
            <div class="flex items-center gap-2 text-gray-300 text-xs">
                <i class="fas fa-calendar-times text-orange-400"></i>
                <span>{{ __('End Date') }}: {{ $project->end_date->format('Y-m-d') }}</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Progress Circle -->
    <div class="flex items-center gap-4 mb-4">
        <div class="relative w-16 h-16 md:w-20 md:h-20">
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
                <span class="text-white font-bold text-sm md:text-base">{{ $project->progress }}%</span>
            </div>
        </div>
        <div class="flex-1">
            <p class="text-gray-400 text-xs mb-1">التقدّم</p>
            @if($project->current_stage)
            <span class="px-3 py-1 rounded-lg text-xs font-semibold {{ $stageColor }}">
                {{ $project->current_stage }}
            </span>
            @else
            <span class="text-gray-400 text-xs">لا توجد مرحلة حالية</span>
            @endif
            <div class="mt-1">
                <span class="px-2 py-0.5 rounded text-xs {{ $statusColor }}">
                    {{ $project->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Icons -->
    <div class="flex items-center gap-4 mb-4 pt-4 border-t border-white/10">
        <a href="{{ route('projects.show', $project->id) }}#attachments" class="flex items-center gap-2 text-gray-400 hover:text-primary-400 text-xs transition-colors">
            <i class="fas fa-paperclip"></i>
            <span>{{ $project->attachments->count() }}</span>
        </a>
        <a href="{{ route('projects.show', $project->id) }}#tasks" class="flex items-center gap-2 text-gray-400 hover:text-primary-400 text-xs transition-colors">
            <i class="fas fa-list-check"></i>
            <span>0</span>
        </a>
        @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
        <a href="{{ route('financials.index', ['project' => $project->id]) }}" class="flex items-center gap-2 text-gray-400 hover:text-primary-400 text-xs transition-colors">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>0</span>
        </a>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2">
        <a 
            href="{{ route('projects.show', $project->id) }}"
            class="flex-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 px-3 py-2 rounded-lg text-center text-xs md:text-sm transition-all duration-200"
        >
            <i class="fas fa-eye ml-1"></i>
            عرض
        </a>
        @can('update', $project)
        <a 
            href="{{ route('projects.edit', $project->id) }}"
            class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-2 rounded-lg text-center text-xs md:text-sm transition-all duration-200"
        >
            <i class="fas fa-edit ml-1"></i>
            تحرير
        </a>
        @endcan
        @if(($project->project_manager_id && $project->project_manager_id === auth()->id()) || auth()->user()->hasRole('super_admin'))
        <form action="{{ route('projects.toggle-hide', $project->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-3 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg text-xs md:text-sm transition-all duration-200" title="{{ __('Hide Project') }}">
                <i class="fas fa-eye-slash"></i>
            </button>
        </form>
        @endif
        @can('delete', $project)
        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-xs md:text-sm transition-all duration-200">
                <i class="fas fa-trash"></i>
            </button>
        </form>
        @endcan
    </div>
</div>
