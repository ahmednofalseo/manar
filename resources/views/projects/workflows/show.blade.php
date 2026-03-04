@extends('layouts.dashboard')

@section('title', 'إدارة المسار - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إدارة المسار')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .step-item {
        cursor: move;
        transition: all 0.2s;
    }
    .step-item:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateX(-4px);
    }
    .step-item.sortable-ghost {
        opacity: 0.4;
        background: rgba(29, 184, 248, 0.2);
    }
    .step-item.sortable-drag {
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $workflow->name }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $project->name }}</p>
        </div>
        <a href="{{ route('projects.workflows.index', $project) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Workflow Info -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-gray-400 text-sm">الخدمة</label>
                <p class="text-white font-semibold">{{ $workflow->service->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <label class="text-gray-400 text-sm">التقدم</label>
                <p class="text-white font-semibold">{{ $workflow->progress }}%</p>
            </div>
            <div>
                <label class="text-gray-400 text-sm">الحالة</label>
                <p class="text-white font-semibold">
                    @if($workflow->status === 'active')
                    <span class="text-green-400">نشط</span>
                    @elseif($workflow->status === 'completed')
                    <span class="text-blue-400">مكتمل</span>
                    @else
                    <span class="text-red-400">ملغي</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Steps Management -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">خطوات المسار</h2>
            @can('update', $project)
            <button 
                @click="$dispatch('open-modal', 'add-step')"
                class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200"
            >
                <i class="fas fa-plus ml-2"></i>
                إضافة خطوة مخصصة
            </button>
            @endcan
        </div>

        <div id="steps-container" class="space-y-4">
            @foreach($workflow->steps as $step)
            <div 
                class="step-item bg-white/5 rounded-lg p-4 border-r-4 {{ $step->status === 'completed' ? 'border-green-500' : ($step->status === 'in_progress' ? 'border-yellow-500' : ($step->status === 'blocked' ? 'border-red-500' : 'border-[#1db8f8]')) }}"
                data-step-id="{{ $step->id }}"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            @can('update', $project)
                            <i class="fas fa-grip-vertical text-gray-400 cursor-move hover:text-[#1db8f8] transition-colors"></i>
                            @endcan
                            <span class="bg-[#1db8f8] text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ $step->order + 1 }}
                            </span>
                            <h3 class="text-lg font-bold text-white">{{ $step->name }}</h3>
                            <span class="bg-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-500/20 text-{{ $step->status === 'completed' ? 'green' : ($step->status === 'in_progress' ? 'yellow' : ($step->status === 'blocked' ? 'red' : 'blue')) }}-400 px-2 py-0.5 rounded text-xs">
                                @if($step->status === 'completed')
                                مكتمل
                                @elseif($step->status === 'in_progress')
                                قيد التنفيذ
                                @elseif($step->status === 'blocked')
                                معطل
                                @else
                                معلق
                                @endif
                            </span>
                            @if($step->is_custom)
                            <span class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded text-xs">مخصص</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                            <span><i class="fas fa-building ml-1"></i> {{ $step->department }}</span>
                            <span><i class="fas fa-clock ml-1"></i> {{ $step->duration_days }} يوم</span>
                            @if($step->assignedUser)
                            <span><i class="fas fa-user ml-1"></i> {{ $step->assignedUser->name }}</span>
                            @endif
                        </div>
                    </div>
                    @can('update', $project)
                    <div class="flex items-center gap-2">
                        <form action="{{ route('projects.workflows.steps.update-status', [$project, $workflow, $step]) }}" method="POST" class="inline">
                            @csrf
                            <select 
                                name="status" 
                                onchange="this.form.submit()"
                                class="bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 py-2 text-white font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                                style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                            >
                                <option value="pending" {{ $step->status === 'pending' ? 'selected' : '' }}>معلق</option>
                                <option value="in_progress" {{ $step->status === 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ $step->status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="skipped" {{ $step->status === 'skipped' ? 'selected' : '' }}>متخطى</option>
                                <option value="blocked" {{ $step->status === 'blocked' ? 'selected' : '' }}>معطل</option>
                            </select>
                        </form>
                        @if($step->is_custom)
                        <form action="{{ route('projects.workflows.steps.delete', [$project, $workflow, $step]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطوة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add Step Modal -->
@can('update', $project)
<div 
    x-data="{ show: false }"
    @open-modal.window="if ($event.detail === 'add-step') show = true"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
    @click.self="show = false"
>
    <div class="glass-card rounded-xl md:rounded-2xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-white mb-4">إضافة خطوة مخصصة</h3>
        <form action="{{ route('projects.workflows.steps.add', [$project, $workflow]) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">اسم الخطوة <span class="text-red-400">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                </div>
                <div class="select-wrapper">
                    <label class="block text-gray-300 text-sm mb-2">القسم <span class="text-red-400">*</span></label>
                    <select 
                        name="department" 
                        required
                        class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                        style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
                    >
                        <option value="معماري" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">معماري</option>
                        <option value="إنشائي" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">إنشائي</option>
                        <option value="كهربائي" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">كهربائي</option>
                        <option value="ميكانيكي" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">ميكانيكي</option>
                        <option value="مساحي" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">مساحي</option>
                        <option value="دفاع_مدني" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">دفاع مدني</option>
                        <option value="بلدي" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">بلدي</option>
                        <option value="أخرى" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">أخرى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">المدة بالأيام <span class="text-red-400">*</span></label>
                    <input 
                        type="number" 
                        name="duration_days" 
                        required
                        min="1"
                        value="7"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الترتيب</label>
                    <input 
                        type="number" 
                        name="order" 
                        value="{{ $workflow->steps->count() }}"
                        min="0"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="show = false" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @can('update', $project)
    const container = document.getElementById('steps-container');
    if (container) {
        const sortable = new Sortable(container, {
            animation: 150,
            handle: '.fa-grip-vertical',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                const stepIds = Array.from(container.children).map(item => item.dataset.stepId);
                
                fetch('{{ route("projects.workflows.reorder-steps", [$project, $workflow]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ steps: stepIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update step numbers
                        container.querySelectorAll('.step-item').forEach((item, index) => {
                            const numberSpan = item.querySelector('.bg-\\[\\#1db8f8\\]');
                            if (numberSpan) {
                                numberSpan.textContent = index + 1;
                            }
                        });
                        
                        // Show success toast
                        showToast('تم إعادة ترتيب الخطوات بنجاح', 'success');
                    } else {
                        showToast(data.message || 'حدث خطأ أثناء إعادة الترتيب', 'error');
                        // Reload page to restore original order
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('حدث خطأ أثناء إعادة الترتيب', 'error');
                    location.reload();
                });
            }
        });
    }
    @endcan
    
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white animate-slide-in mx-auto sm:mx-0`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="mr-2 flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>
@endpush
@endsection
