@extends('layouts.dashboard')

@section('title', __('Project Details') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Project Details'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm sm:text-base">{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('warning'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-yellow-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text-sm sm:text-base">{{ session('warning') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div x-data="projectTabs()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $project->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $project->project_number ?? 'غير محدد' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.edit', $project->id) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Edit') }}
            </a>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-xl md:rounded-2xl mb-4 md:mb-6">
        <div class="border-b border-white/10 flex flex-wrap overflow-x-auto">
            <button 
                @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-gauge"></i>
                {{ __('Overview') }}
            </button>
            <button 
                @click="activeTab = 'stages'"
                :class="activeTab === 'stages' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-diagram-project"></i>
                {{ __('Stages') }}
                @if($stagesCount > 0)
                <span class="bg-primary-400/20 text-primary-400 px-2 py-0.5 rounded text-xs">{{ $stagesCount }}</span>
                @endif
            </button>
            <button 
                @click="activeTab = 'tasks'"
                :class="activeTab === 'tasks' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-list-check"></i>
                {{ __('Tasks') }}
                @if($tasksCount > 0)
                <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs">{{ $tasksCount }}</span>
                @endif
            </button>
            <button 
                @click="activeTab = 'attachments'"
                :class="activeTab === 'attachments' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-paperclip"></i>
                {{ __('Attachments') }}
                @if($attachmentsCount > 0)
                <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-xs">{{ $attachmentsCount }}</span>
                @endif
            </button>
            @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
            <button 
                @click="activeTab = 'financials'"
                :class="activeTab === 'financials' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-file-invoice-dollar"></i>
                المالية
            </button>
            @endif
            <button 
                @click="activeTab = 'thirdparty'"
                :class="activeTab === 'thirdparty' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-handshake"></i>
                الطرف الثالث
            </button>
            <button 
                @click="activeTab = 'activity'"
                :class="activeTab === 'activity' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-clock-rotate-left"></i>
                السجل
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.overview')
    </div>

    <!-- Stages Tab -->
    <div x-show="activeTab === 'stages'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.stages')
    </div>

    <!-- Tasks Tab -->
    <div x-show="activeTab === 'tasks'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.tasks')
    </div>

    <!-- Attachments Tab -->
    <div x-show="activeTab === 'attachments'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.attachments')
    </div>

    <!-- Financials Tab -->
    @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
    <div x-show="activeTab === 'financials'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.financials')
    </div>
    @endif

    <!-- Third Party Tab -->
    <div x-show="activeTab === 'thirdparty'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.thirdparty')
    </div>

    <!-- Activity Tab -->
    <div x-show="activeTab === 'activity'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.activity')
    </div>
</div>

@push('scripts')
<script>
    function projectTabs() {
        return {
            activeTab: 'overview'
        }
    }
</script>
@endpush

@endsection
