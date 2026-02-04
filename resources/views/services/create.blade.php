@extends('layouts.dashboard')

@section('title', 'إضافة خدمة جديدة - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إضافة خدمة جديدة')

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

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">إضافة خدمة جديدة</h1>
        <a href="{{ route('services.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('services.store') }}" method="POST" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        @csrf

        <div class="space-y-6">
            <!-- Basic Information -->
            <div>
                <h2 class="text-xl font-bold text-white mb-4">المعلومات الأساسية</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">اسم الخدمة <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="مثال: تصميم، إشراف، خدمات بلدي..."
                        >
                        @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">الوصف</label>
                        <textarea 
                            name="description" 
                            rows="3"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="وصف مختصر للخدمة..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">الأيقونة (Font Awesome)</label>
                        <input 
                            type="text" 
                            name="icon" 
                            value="{{ old('icon') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="مثال: fas fa-building, fas fa-cog"
                        >
                        <p class="text-gray-400 text-xs mt-1">اتركه فارغاً لإظهار أيقونة افتراضية</p>
                        @error('icon')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Category & Parent -->
            <div>
                <h2 class="text-xl font-bold text-white mb-4">التصنيف والترتيب</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="select-wrapper">
                        <label class="block text-gray-300 text-sm mb-2">الفئة</label>
                        <select name="category_id" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                            <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">بدون فئة</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="select-wrapper">
                        <label class="block text-gray-300 text-sm mb-2">الخدمة الرئيسية (لخدمات بلدي الفرعية)</label>
                        <select name="parent_id" class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200" style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;">
                            <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">خدمة رئيسية</option>
                            @foreach($parentServices as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">ترتيب العرض</label>
                        <input 
                            type="number" 
                            name="order" 
                            value="{{ old('order', 0) }}"
                            min="0"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                        @error('order')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div>
                <h2 class="text-xl font-bold text-white mb-4">الخيارات</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_custom" 
                            value="1"
                            {{ old('is_custom') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                        >
                        <span class="text-gray-300">خدمة مخصصة</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="has_sub_services" 
                            value="1"
                            {{ old('has_sub_services') ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                        >
                        <span class="text-gray-300">يدعم خدمات فرعية (مثل خدمات بلدي)</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                        >
                        <span class="text-gray-300">نشط</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('services.index') }}" class="px-6 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
