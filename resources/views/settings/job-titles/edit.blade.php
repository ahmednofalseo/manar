@extends('layouts.dashboard')

@section('title', __('Edit job title') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit job title'))

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
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit job title') }}</h1>
    <a href="{{ route('settings.job-titles.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

@if($errors->any())
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-6 border-2 border-red-500/50 bg-red-500/10">
    <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('settings.job-titles.update', $jobTitle) }}" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Job title Arabic') }} <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $jobTitle->name) }}" required maxlength="255"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            @error('name')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Job title English') }}</label>
            <input type="text" name="name_en" value="{{ old('name_en', $jobTitle->name_en) }}" maxlength="255"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Order') }}</label>
                <input type="number" name="order" value="{{ old('order', $jobTitle->order) }}" min="0"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-3 cursor-pointer pb-1">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $jobTitle->is_active) ? 'checked' : '' }}
                        class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-gray-300 text-sm">{{ __('Active') }}</span>
                </label>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
            <a href="{{ route('settings.job-titles.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">{{ __('Cancel') }}</a>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>
@endsection
