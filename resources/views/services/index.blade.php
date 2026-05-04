@extends('layouts.dashboard')

@section('title', __('Services management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Services management'))

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

<div x-data="servicesPage()">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Services management') }}</h1>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            @can('create', \App\Models\Service::class)
            <a href="{{ route('services.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Add new service') }}
            </a>
            <a href="{{ route('workflow-templates.create') }}" class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-sitemap {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Design new workflow') }}
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <div class="space-y-4">
            <!-- Search Bar -->
            <div class="relative">
                <input 
                    type="text" 
                    x-model="search"
                    @input.debounce.300ms="applyFilters()"
                    placeholder="{{ __('Services search placeholder') }}" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pe-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
                <i class="fas fa-search absolute end-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Category') }}</label>
                    <select x-model="categoryId" @change="applyFilters()" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                    <select x-model="isActive" @change="applyFilters()" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All') }}</option>
                        <option value="1" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('Active') }}</option>
                        <option value="0" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="select-wrapper">
                    <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Type') }}</label>
                    <select x-model="mainOnly" @change="applyFilters()" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-3 md:px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200 text-sm md:text-base" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('All') }}</option>
                        <option value="1" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ __('Main services only') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Services List -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        @if($services->count() > 0)
        <div class="space-y-4">
            @foreach($services as $service)
            <div class="bg-white/5 rounded-lg p-4 md:p-6 hover:bg-white/10 transition-all duration-200">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            @if($service->icon)
                            <i class="{{ $service->icon }} text-2xl text-[#1db8f8]"></i>
                            @else
                            <div class="w-10 h-10 rounded-lg bg-[#1db8f8]/20 flex items-center justify-center">
                                <i class="fas fa-cog text-[#1db8f8]"></i>
                            </div>
                            @endif
                            <div>
                                <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                                    {{ $service->name }}
                                    @if($service->is_custom)
                                    <span class="bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded text-xs">{{ __('Custom') }}</span>
                                    @endif
                                    @if(!$service->is_active)
                                    <span class="bg-red-500/20 text-red-400 px-2 py-0.5 rounded text-xs">{{ __('Inactive') }}</span>
                                    @endif
                                </h3>
                                @if($service->category)
                                <p class="text-gray-400 text-sm mt-1">
                                    <i class="fas fa-folder {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                    {{ $service->category->name }}
                                </p>
                                @endif
                                @if($service->description)
                                <p class="text-gray-300 text-sm mt-2">{{ $service->description }}</p>
                                @endif
                                @if($service->has_sub_services && $service->subServices->count() > 0)
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($service->subServices as $subService)
                                    <span class="bg-[#1db8f8]/20 text-[#1db8f8] px-2 py-1 rounded text-xs">
                                        <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                        {{ $subService->name }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @can('view', $service)
                        <a href="{{ route('services.show', $service) }}" class="px-3 py-2 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                            {{ __('View') }}
                        </a>
                        @endcan
                        @can('update', $service)
                        <a href="{{ route('services.edit', $service) }}" class="px-3 py-2 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                            <i class="fas fa-edit {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                            {{ __('Edit') }}
                        </a>
                        @endcan
                        @can('delete', $service)
                        <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm(@json(__('Confirm delete service')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                                <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                                {{ __('Delete') }}
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $services->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-500 mb-4"></i>
            <p class="text-gray-400 text-lg">{{ __('No services') }}</p>
            @can('create', \App\Models\Service::class)
            <a href="{{ route('services.create') }}" class="mt-4 inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Add new service') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function servicesPage() {
    return {
        search: new URLSearchParams(window.location.search).get('search') || '',
        categoryId: new URLSearchParams(window.location.search).get('category_id') || '',
        isActive: new URLSearchParams(window.location.search).get('is_active') || '',
        mainOnly: new URLSearchParams(window.location.search).get('main_only') || '',
        
        applyFilters() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.categoryId) params.set('category_id', this.categoryId);
            if (this.isActive) params.set('is_active', this.isActive);
            if (this.mainOnly) params.set('main_only', this.mainOnly);
            
            window.location.href = '{{ route("services.index") }}?' + params.toString();
        }
    }
}
</script>
@endpush
@endsection
