@extends('layouts.dashboard')

@section('title', __('Projects') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Projects'))

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

@if(session('error'))
<div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Projects') }}</h1>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        @can('create', \App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('New Project') }}
        </a>
        @endcan
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="filterState()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="{{ __('Search: project name, number, owner...') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pe-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute end-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 md:gap-4">
            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('City') }}</label>
                <select x-model="city" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All Cities') }}</option>
                    @foreach($cities as $cityModel)
                        <option value="{{ $cityModel->name }}" {{ request('city') == $cityModel->name ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $cityModel->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('District') }}</label>
                <select x-model="district" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All Districts') }}</option>
                    @foreach($districts as $districtName)
                        <option value="{{ $districtName }}" {{ request('district') == $districtName ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $districtName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Owner') }}</label>
                <select x-model="owner" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All Owners') }}</option>
                    @foreach($owners as $ownerName)
                        <option value="{{ $ownerName }}" {{ request('owner') == $ownerName ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $ownerName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Project Type') }}</label>
                <select x-model="type" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All Types') }}</option>
                    @foreach($projectTypes as $projectType)
                        <option value="{{ $projectType->name }}" {{ request('type') == $projectType->name ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $projectType->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="select-wrapper">
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status/Stage') }}</label>
                <select x-model="status" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                    <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All Statuses') }}</option>
                    <optgroup label="{{ __('Project status group') }}" style="background-color: #173343; color: #ffffff;">
                        @foreach($statusLabelMap as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $label }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="{{ __('Current Stage') }}" style="background-color: #173343; color: #ffffff;">
                        @foreach($stages as $stage)
                        <option value="{{ $stage->name }}" {{ request('status') == $stage->name ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $stage->display_name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('From Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('To Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Filter Actions -->
        <form method="GET" action="{{ route('projects.index') }}" class="flex items-center gap-3">
            <input type="hidden" name="search" :value="search">
            <input type="hidden" name="city" :value="city">
            <input type="hidden" name="district" :value="district">
            <input type="hidden" name="owner" :value="owner">
            <input type="hidden" name="type" :value="type">
            <input type="hidden" name="status" :value="status">
            <input type="hidden" name="from_date" :value="dateFrom">
            <input type="hidden" name="to_date" :value="dateTo">
            <button type="submit" class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Apply Filters') }}
            </button>
            <a href="{{ route('projects.index') }}" class="px-6 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </form>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $totalProjects }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('In Progress') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $activeProjects }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Completed') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $completedProjects }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Delayed Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ $delayedProjects }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
@if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg md:text-xl font-bold text-white">{{ __('Financial Summary') }}</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-gray-400 text-sm mb-2">{{ __('Total Projects Value') }}</p>
            <p class="text-2xl md:text-3xl font-bold text-white">{{ number_format($totalValue, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">{{ __('Collections This Month') }}</p>
            <p class="text-2xl md:text-3xl font-bold text-primary-400"><span class="text-primary-400">0</span> <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
            <p class="text-gray-400 text-xs mt-2">{{ __('Will be linked to invoices module') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Projects Grid -->
@if($projects->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    @foreach($projects as $project)
        @include('components.cards.project-card', [
            'project' => $project,
            'typeLabelMap' => $typeLabelMap,
            'stageLabelMap' => $stageLabelMap,
            'statusLabelMap' => $statusLabelMap,
        ])
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $projects->links() }}
</div>
@else
<!-- Empty State -->
<div class="text-center py-12 md:py-16">
    <div class="glass-card rounded-xl md:rounded-2xl p-8 md:p-12 max-w-md mx-auto">
        <i class="fas fa-folder-open text-6xl md:text-7xl text-gray-400 mb-6"></i>
        <h3 class="text-xl md:text-2xl font-bold text-white mb-3">{{ __('No projects found') }}</h3>
        <p class="text-gray-400 mb-6">{{ __('Start by creating a new project to manage your business') }}</p>
        <a href="{{ route('projects.create') }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('New Project') }}
        </a>
    </div>
</div>
@endif

@push('scripts')
<script>
    function filterState() {
        return {
            search: @json(request('search', '')),
            city: @json(request('city', '')),
            district: @json(request('district', '')),
            owner: @json(request('owner', '')),
            type: @json(request('type', '')),
            status: @json(request('status', '')),
            dateFrom: @json(request('from_date', '')),
            dateTo: @json(request('to_date', '')),
        };
    }
</script>
@endpush

@endsection
