@extends('layouts.dashboard')

@section('title', 'إضافة مدينة جديدة - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إضافة مدينة جديدة')

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

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">إضافة مدينة جديدة</h1>
    <a href="{{ route('settings.cities.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right ml-2"></i>
        رجوع
    </a>
</div>

<!-- Validation Errors -->
@if($errors->any())
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-6 border-2 border-red-500/50 bg-red-500/10">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-exclamation-circle text-red-400"></i>
                <span class="text-base font-semibold text-red-400">يرجى تصحيح الأخطاء التالية:</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1 mr-4 text-red-300">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Form -->
    <form method="POST" action="{{ route('settings.cities.store') }}" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    @csrf

    <div class="space-y-6">
        <div>
            <label class="block text-gray-300 text-sm mb-2">اسم المدينة <span class="text-red-400">*</span></label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                required
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="مثال: الرياض"
            >
            @error('name')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-2">الاسم بالإنجليزية</label>
            <input 
                type="text" 
                name="name_en" 
                value="{{ old('name_en') }}"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="Example: Riyadh"
            >
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-2">كود المدينة</label>
            <input 
                type="text" 
                name="code" 
                value="{{ old('code') }}"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="مثال: RUH"
            >
            @error('code')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الترتيب</label>
                <input 
                    type="number" 
                    name="order" 
                    value="{{ old('order', 0) }}"
                    min="0"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500"
                    >
                    <span class="text-gray-300 text-sm">نشط</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
            <a href="{{ route('settings.cities.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-save ml-2"></i>
                حفظ
            </button>
        </div>
    </div>
</form>
@endsection
