@extends('layouts.dashboard')

@section('title', __('Edit') . ' ' . __('Tasks') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit') . ' ' . __('Tasks'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@section('content')
@php
    $isEn = app()->getLocale() === 'en';
    $taskLocaleDefault = $isEn ? 'en' : 'ar';
@endphp
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit') }} {{ __('Tasks') }}</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('tasks.show', $task->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('View') }}
        </a>
        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="{{ route('tasks.update', $task->id) }}" enctype="multipart/form-data" x-data="taskForm({{ $projects->toJson() }}, {{ $task->project_id }}, @json($task->project_stage_id), @js(['filesSelected' => __('Selected files count label'), 'sizeUnits' => [__('File size unit B'), __('File size unit KB'), __('File size unit MB'), __('File size unit GB')]]))" x-init="init()">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>

        <div x-data="{ taskLocaleTab: '{{ $taskLocaleDefault }}' }" class="mb-6 space-y-4">
            <div class="flex gap-2 border-b border-white/10 pb-1">
                <button type="button" @click="taskLocaleTab = 'ar'"
                    class="px-4 py-2 rounded-t-lg text-sm font-medium transition"
                    :class="taskLocaleTab === 'ar' ? 'bg-primary-500/30 text-white border-b-2 border-primary-400 -mb-px' : 'text-gray-400 hover:text-white'">
                    {{ __('Arabic') }}
                </button>
                <button type="button" @click="taskLocaleTab = 'en'"
                    class="px-4 py-2 rounded-t-lg text-sm font-medium transition"
                    :class="taskLocaleTab === 'en' ? 'bg-primary-500/30 text-white border-b-2 border-primary-400 -mb-px' : 'text-gray-400 hover:text-white'">
                    {{ __('English') }}
                </button>
            </div>

            <div x-show="taskLocaleTab === 'ar'" x-cloak class="space-y-4 md:space-y-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Task Title') }} <span class="text-red-400">*</span></label>
                    <input
                        type="text"
                        name="title"
                        required
                        value="{{ old('title', $task->title) }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Task title placeholder') }}"
                    >
                    @error('title')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Task Description') }} <span class="text-red-400">*</span></label>
                    <textarea
                        name="description"
                        rows="4"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Task description placeholder') }}"
                    >{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Manager Notes') }}</label>
                    <textarea
                        name="manager_notes"
                        rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Manager notes placeholder') }}"
                    >{{ old('manager_notes', $task->manager_notes) }}</textarea>
                </div>
            </div>

            <div x-show="taskLocaleTab === 'en'" x-cloak class="space-y-4 md:space-y-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Task Title (English)') }}</label>
                    <input
                        type="text"
                        name="title_en"
                        value="{{ old('title_en', $task->title_en) }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Task title placeholder English') }}"
                    >
                    @error('title_en')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Task Description (English)') }}</label>
                    <textarea
                        name="description_en"
                        rows="4"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Task description placeholder English') }}"
                    >{{ old('description_en', $task->description_en) }}</textarea>
                    @error('description_en')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Manager Notes (English)') }}</label>
                    <textarea
                        name="manager_notes_en"
                        rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Manager notes placeholder English') }}"
                    >{{ old('manager_notes_en', $task->manager_notes_en) }}</textarea>
                    @error('manager_notes_en')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Related Project') }} <span class="text-red-400">*</span></label>
                <select
                    name="project_id"
                    required
                    x-model="selectedProjectId"
                    @change="fetchProjectStages()"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                    <option value="">{{ __('Select Project') }}</option>
                    @foreach($projects as $project)
                        <option
                            value="{{ $project->id }}"
                            data-stages='@json($project->projectStages)'
                            {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}
                        >
                            {{ $project->display_name }}@if($project->project_number) ({{ $project->project_number }})@endif
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Stages') }}</label>
                <select
                    name="project_stage_id"
                    x-model="selectedStageId"
                    x-ref="stageSelect"
                    x-effect="updateStageOptions()"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                    <option value="">{{ __('Select Stage') }} ({{ __('Optional') }})</option>
                </select>
                @error('project_stage_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Assignee') }} <span class="text-red-400">*</span></label>
                <x-users-dropdown
                    name="assignee_id"
                    :selected="old('assignee_id', $task->assignee_id)"
                    :roleFilter="['engineer', 'project_manager']"
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
                <select name="priority" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                    <option value="medium" {{ old('priority', $task->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Start Date') }}</label>
                <input
                    type="date"
                    name="start_date"
                    value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Due Date') }}</label>
                <input
                    type="date"
                    name="due_date"
                    value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="new" {{ old('status', $task->status) == 'new' ? 'selected' : '' }}>{{ __('Task status new') }}</option>
                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>{{ __('Task status in progress') }}</option>
                    <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>{{ __('Task status done') }}</option>
                    <option value="rejected" {{ old('status', $task->status) == 'rejected' ? 'selected' : '' }}>{{ __('Task status rejected') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Progress') }} (%)</label>
                <input
                    type="number"
                    name="progress"
                    min="0"
                    max="100"
                    value="{{ old('progress', $task->progress ?? 0) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Attachments') }}</h2>
        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Upload attachments optional') }}</label>
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
                    <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Select Files') }}
                </label>
                <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="filesSelectedText"></span>
            </div>
            <p class="text-gray-400 text-xs mt-1">{{ __('Task attachments formats hint') }}</p>
        </div>

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
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function taskForm(initialProjects, initialProjectId, initialStageId, i18n) {
    i18n = i18n || { filesSelected: ':count', sizeUnits: ['B', 'KB', 'MB', 'GB'] };
    return {
        selectedFiles: [],
        filesSelectedTemplate: i18n.filesSelected,
        sizeUnits: i18n.sizeUnits || ['B', 'KB', 'MB', 'GB'],
        projects: initialProjects || [],
        selectedProjectId: initialProjectId || null,
        selectedStageId: initialStageId || null,
        projectStages: [],
        get filesSelectedText() {
            if (this.selectedFiles.length === 0) {
                return '';
            }
            return String(this.filesSelectedTemplate).replace(':count', String(this.selectedFiles.length));
        },
        updateStageOptions() {
            const select = this.$refs?.stageSelect;
            if (!select) return;
            const currentVal = this.selectedStageId;
            while (select.options.length > 1) select.remove(1);
            this.projectStages.forEach(stage => {
                const opt = document.createElement('option');
                opt.value = stage.id;
                opt.textContent = stage.stage_name;
                select.appendChild(opt);
            });
            const validStage = currentVal && this.projectStages.some(s => s.id == currentVal);
            if (validStage) {
                select.value = String(currentVal);
            } else {
                select.value = '';
                if (this.selectedStageId) this.selectedStageId = '';
            }
        },
        init() {
            if (this.selectedProjectId) {
                this.fetchProjectStages();
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
                        if (!this.projectStages.some(stage => stage.id == this.selectedStageId)) {
                            this.selectedStageId = null;
                        }
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
            if (bytes === 0) return '0 ' + this.sizeUnits[0];
            const k = 1024;
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + this.sizeUnits[i];
        }
    };
}
</script>
@endpush

@endsection
