@extends('layouts.dashboard')

@section('title', 'إضافة مسار جديد - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إضافة مسار جديد')

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

<div class="w-full max-w-4xl mx-auto px-2 sm:px-4" style="box-sizing: border-box; overflow-x: hidden; width: 100%; max-width: min(100%, 56rem);">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="w-full sm:w-auto">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white break-words">إضافة مسار جديد</h1>
            <p class="text-gray-400 text-sm mt-1 break-words">{{ $project->name }}</p>
        </div>
        <a href="{{ route('projects.workflows.index', $project) }}" class="px-3 sm:px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm sm:text-base whitespace-nowrap">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('projects.workflows.store', $project) }}" method="POST" class="glass-card rounded-xl md:rounded-2xl p-3 sm:p-4 md:p-6" style="box-sizing: border-box; overflow-x: hidden; width: 100%;">
        @csrf

        <div class="space-y-6">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-white mb-4">معلومات المسار</h2>
                <div class="space-y-4">
                    <div class="w-full" style="box-sizing: border-box; overflow: hidden;">
                        <label class="block text-gray-300 text-xs sm:text-sm mb-2 w-full" style="box-sizing: border-box; overflow: hidden; word-wrap: break-word;">الخدمة <span class="text-red-400">*</span></label>
                        <select 
                            name="service_id" 
                            required
                            class="bg-[#173343]/90 border border-white/30 rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 text-white font-medium text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#1db8f8] focus:border-[#1db8f8] transition-all duration-200"
                            style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 500; width: 60% !important; box-sizing: border-box; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;"
                        >
                            <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 8px 12px;">اختر الخدمة</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 8px 12px;">
                                    {{ $service->name }}
                                </option>
                                @if($service->subServices && $service->subServices->count() > 0)
                                    @foreach($service->subServices as $subService)
                                        <option value="{{ $subService->id }}" {{ old('service_id') == $subService->id ? 'selected' : '' }} style="background-color: #1a3d4f; color: #ffffff; font-weight: 500; padding: 8px 12px; padding-right: 20px;">
                                            └ {{ $subService->name }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        @error('service_id')
                        <p class="text-red-400 text-xs sm:text-sm mt-1 flex items-center gap-1 w-full" style="word-wrap: break-word; overflow-wrap: break-word;">
                            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                            <span class="break-words">{{ $message }}</span>
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">اسم المسار <span class="text-red-400">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            placeholder="مثال: مسار التصميم الكامل"
                        >
                        @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 text-sm mb-2">تاريخ البدء</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="{{ old('start_date', date('Y-m-d')) }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                        @error('start_date')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($isParallel || $parentWorkflowId)
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_parallel" 
                                value="1"
                                {{ old('is_parallel', $isParallel) ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-white/10 bg-white/5 text-primary-500 focus:ring-primary-400/40"
                            >
                            <span class="text-gray-300">مسار متوازي</span>
                        </label>
                        @if($parentWorkflowId)
                        <input type="hidden" name="parent_workflow_id" value="{{ $parentWorkflowId }}">
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Note -->
            <div class="bg-blue-500/20 border border-blue-500/30 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                    <div>
                        <p class="text-blue-300 text-sm">
                            سيتم إنشاء المسار بناءً على القالب الافتراضي للخدمة المختارة. يمكنك تعديل المسار لاحقاً من صفحة إدارة المسار.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <a href="{{ route('projects.workflows.index', $project) }}" class="px-6 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    إنشاء المسار
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
