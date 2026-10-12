<div class="flex flex-wrap gap-3 mb-4 md:mb-6">
    <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 rounded-lg border text-sm font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300' }}">
        {{ __('General Settings') }}
    </a>
    <a href="{{ route('settings.project-types.index') }}" class="px-4 py-2 rounded-lg border text-sm font-semibold {{ request()->routeIs('settings.project-types.*') ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300' }}">
        {{ __('Project Settings') }}
    </a>
    <a href="{{ route('settings.project-stages.index') }}" class="px-4 py-2 rounded-lg border text-sm font-semibold {{ request()->routeIs('settings.project-stages.*') ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300' }}">
        <i class="fas fa-diagram-project {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Project stages tab') }}
    </a>
    <a href="{{ route('settings.cities.index') }}" class="px-4 py-2 rounded-lg border text-sm font-semibold {{ request()->routeIs('settings.cities.*') ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300' }}">
        {{ __('Cities Management') }}
    </a>
    <a href="{{ route('settings.job-titles.index') }}" class="px-4 py-2 rounded-lg border text-sm font-semibold {{ request()->routeIs('settings.job-titles.*') ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300' }}">
        {{ __('Job titles management') }}
    </a>
</div>
