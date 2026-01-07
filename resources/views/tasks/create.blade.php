@extends('layouts.dashboard')

@section('title', __('New Task') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('New Task'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .input-error {
        border-color: #ef4444 !important;
        background-color: rgba(239, 68, 68, 0.1) !important;
    }
    .input-error:focus {
        ring-color: #ef4444 !important;
        border-color: #ef4444 !important;
    }
</style>
@endpush

@section('content')
<!-- Validation Errors Alert -->
@if($errors->any())
<div class="mb-4 glass-card rounded-xl p-4 border border-red-500/30 bg-red-500/10">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-red-400 font-semibold mb-2">{{ __('Please correct the following errors') }}:</h3>
            <ul class="list-disc list-inside text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('New Task') }}</h1>
    <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" x-data="taskForm()" x-init="init()">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">{{ __('Task Title') }} <span class="text-red-400">*</span></label>
                <input 
                    type="text" 
                    name="title" 
                    required
                    value="{{ old('title') }}"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('title') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                    placeholder="مثال: مراجعة المخططات المعمارية"
                >
                @error('title')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Related Project') }} <span class="text-red-400">*</span></label>
                <select 
                    name="project_id" 
                    required 
                    x-model="selectedProjectId"
                    @change="fetchProjectStages()"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('project_id') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                >
                    <option value="">{{ __('Select Project') }}</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" data-stages='@json($proj->projectStages)' {{ (old('project_id', $selectedProjectId ?? '') == $proj->id) ? 'selected' : '' }}>
                            {{ $proj->name }} ({{ $proj->project_number ?? 'غير محدد' }})
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Stages') }}</label>
                <select 
                    name="project_stage_id" 
                    x-model="selectedStageId"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('project_stage_id') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                >
                    <option value="">{{ __('Select Stage') }} ({{ __('Optional') }})</option>
                    <template x-for="stage in projectStages" :key="stage.id">
                        <option :value="stage.id" x-text="stage.stage_name" :selected="stage.id == {{ old('project_stage_id', 'null') }}"></option>
                    </template>
                </select>
                @error('project_stage_id')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Assignee') }} <span class="text-red-400">*</span></label>
                <x-users-dropdown 
                    name="assignee_id" 
                    :selected="old('assignee_id')"
                    required 
                    placeholder="{{ __('Select Assignee') }}"
                />
                @error('assignee_id')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Priority') }}</label>
                <select name="priority" class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('priority') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror">
                    <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                    <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                </select>
                @error('priority')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ البدء</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ old('start_date', date('Y-m-d')) }}"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('start_date') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                >
                @error('start_date')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الانتهاء</label>
                <input 
                    type="date" 
                    name="due_date" 
                    value="{{ old('due_date') }}"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('due_date') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                >
                @error('due_date')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">وصف المهمة</label>
                <textarea 
                    name="description" 
                    rows="4"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('description') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                    placeholder="وصف تفصيلي للمهمة..."
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">ملاحظات مدير المشروع</label>
                <textarea 
                    name="manager_notes" 
                    rows="3"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 @error('manager_notes') border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @enderror"
                    placeholder="ملاحظات إضافية من مدير المشروع..."
                >{{ old('manager_notes') }}</textarea>
                @error('manager_notes')
                    <p class="text-red-400 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="new" selected>جديد</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="completed">منجز</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
        <div>
            <label class="block text-gray-300 text-sm mb-2">رفع المرفقات (اختياري)</label>
            <div class="flex items-center gap-4">
                <input 
                    type="file" 
                    name="attachments[]"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png,.dwg"
                    class="hidden"
                    id="attachmentsInput"
                    @change="handleFilesSelect($event)"
                >
                <label for="attachmentsInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200">
                    <i class="fas fa-upload ml-2"></i>
                    اختر الملفات
                </label>
                <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="selectedFiles.length + ' ملف محدد'"></span>
            </div>
            <p class="text-gray-400 text-xs mt-1">يمكن رفع عدة ملفات (PDF, JPG, PNG, DWG)</p>
        </div>

        <!-- Selected Files List -->
        <div x-show="selectedFiles.length > 0" class="mt-4 space-y-2">
            <template x-for="(file, index) in selectedFiles" :key="index">
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file text-primary-400"></i>
                        <span class="text-white text-sm" x-text="file.name"></span>
                        <span class="text-gray-400 text-xs" x-text="formatFileSize(file.size)"></span>
                    </div>
                    <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('tasks.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            إلغاء
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save ml-2"></i>
            حفظ
        </button>
    </div>
</form>

@push('scripts')
<script>
function taskForm() {
    return {
        selectedFiles: [],
        selectedProjectId: @if(old('project_id', $selectedProjectId)){{ old('project_id', $selectedProjectId) }}@else null @endif,
        selectedStageId: @if(old('project_stage_id')){{ old('project_stage_id') }}@else null @endif,
        projectStages: [],
        init() {
            // تحميل المراحل للمشروع المحدد مسبقاً
            if (this.selectedProjectId) {
                // تحديث قيمة الـ select
                const projectSelect = document.querySelector('select[name="project_id"]');
                if (projectSelect) {
                    projectSelect.value = this.selectedProjectId;
                    // تحميل المراحل
                    this.$nextTick(() => {
                        this.fetchProjectStages();
                    });
                }
            }
        },
        fetchProjectStages() {
            const projectSelect = document.querySelector('select[name="project_id"]');
            if (!projectSelect) return;
            
            const selectedOption = projectSelect.options[projectSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const stagesData = selectedOption.getAttribute('data-stages');
                if (stagesData) {
                    try {
                        this.projectStages = JSON.parse(stagesData);
                        this.selectedStageId = null; // إعادة تعيين المرحلة المختارة
                    } catch (e) {
                        console.error('Error parsing stages data:', e);
                        this.projectStages = [];
                    }
                } else {
                    this.projectStages = [];
                }
            } else {
                this.projectStages = [];
                this.selectedStageId = null;
            }
        },
        handleFilesSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files.map(file => ({
                name: file.name,
                size: file.size
            }));
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            const input = document.getElementById('attachmentsInput');
            if (input) {
                input.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }
}
</script>
@endpush

@endsection


