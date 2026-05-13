@extends('layouts.dashboard')

@section('title', __('Job titles management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Job titles management'))

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

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Job titles management') }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ __('Job titles management subtitle') }}</p>
    </div>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
        @can('create', \App\Models\JobTitle::class)
        <a href="{{ route('settings.job-titles.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Add job title') }}
        </a>
        @endcan
    </div>
</div>

<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    @if($jobTitles->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-start border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">{{ __('Order') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">{{ __('Job title Arabic') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">{{ __('Job title English') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobTitles as $jt)
                <tr class="border-b border-white/5 hover:bg-white/5">
                    <td class="py-3 px-4 text-white text-sm">{{ $jt->order }}</td>
                    <td class="py-3 px-4 text-white text-sm">{{ $jt->name }}</td>
                    <td class="py-3 px-4 text-gray-300 text-sm">{{ $jt->name_en ?? '—' }}</td>
                    <td class="py-3 px-4">
                        @if($jt->is_active)
                            <span class="px-2 py-1 rounded text-xs bg-green-500/20 text-green-400">{{ __('Active') }}</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs bg-gray-500/20 text-gray-400">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            @can('update', $jt)
                            <a href="{{ route('settings.job-titles.edit', $jt) }}" class="text-primary-400 hover:text-primary-300 text-sm">{{ __('Edit') }}</a>
                            @endcan
                            @can('delete', $jt)
                            <form method="POST" action="{{ route('settings.job-titles.destroy', $jt) }}" class="inline" onsubmit="return confirm('{{ __('Confirm delete job title') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm">{{ __('Delete') }}</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-gray-400 text-center py-8">{{ __('No job titles yet') }}</p>
    @endif
</div>
@endsection
