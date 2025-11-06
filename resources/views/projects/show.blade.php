@extends('layouts.dashboard')

@section('title', 'تفاصيل المشروع - المنار')
@section('page-title', 'تفاصيل المشروع')

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
<div x-data="projectTabs()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">فيلا سكنية - العليا</h1>
            <p class="text-gray-400 text-sm">PRJ-2025-001</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.edit', $id) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-edit ml-2"></i>
                تحرير
            </a>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
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
                نظرة عامة
            </button>
            <button 
                @click="activeTab = 'stages'"
                :class="activeTab === 'stages' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-diagram-project"></i>
                المراحل
                <span class="bg-primary-500/20 text-primary-400 px-2 py-0.5 rounded text-xs">5</span>
            </button>
            <button 
                @click="activeTab = 'tasks'"
                :class="activeTab === 'tasks' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-list-check"></i>
                المهام
                <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs">12</span>
            </button>
            <button 
                @click="activeTab = 'attachments'"
                :class="activeTab === 'attachments' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-paperclip"></i>
                المرفقات
                <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-xs">8</span>
            </button>
            <button 
                @click="activeTab = 'financials'"
                :class="activeTab === 'financials' ? 'border-b-2 border-primary-400 text-primary-400' : 'text-gray-400 hover:text-white'"
                class="px-4 md:px-6 py-3 md:py-4 font-semibold text-sm md:text-base transition-all duration-200 flex items-center gap-2 whitespace-nowrap"
            >
                <i class="fas fa-file-invoice-dollar"></i>
                المالية
            </button>
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
    <div x-show="activeTab === 'financials'" class="space-y-4 md:space-y-6">
        @include('projects.tabs.financials')
    </div>

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
