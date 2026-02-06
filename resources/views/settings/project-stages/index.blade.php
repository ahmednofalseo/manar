@extends('layouts.dashboard')

@section('title', 'إدارة مراحل المشاريع - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إدارة مراحل المشاريع')

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
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white">إدارة مراحل المشاريع</h1>
        <p class="text-gray-400 text-sm mt-1">إدارة المراحل المستخدمة في المشاريع</p>
    </div>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
        @can('create', \App\Models\StageSetting::class)
        <a href="{{ route('settings.project-stages.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus ml-2"></i>
            إضافة مرحلة جديدة
        </a>
        @endcan
    </div>
</div>

<!-- Stages List -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    @if($stages->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">الترتيب</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">اسم المرحلة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">الأيقونة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">اللون</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3 px-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stages as $stage)
                <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                    <td class="py-3 px-4 text-white text-sm">{{ $stage->order }}</td>
                    <td class="py-3 px-4 text-white text-sm font-semibold">{{ $stage->name }}</td>
                    <td class="py-3 px-4">
                        @if($stage->icon)
                        <i class="{{ $stage->icon }} text-xl text-primary-400"></i>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($stage->color)
                        <span class="inline-block w-6 h-6 rounded-full" style="background-color: {{ $stage->color }}"></span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-semibold {{ $stage->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $stage->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                            @can('update', $stage)
                            <a href="{{ route('settings.project-stages.edit', $stage) }}" class="px-3 py-1 bg-[#1db8f8]/20 hover:bg-[#1db8f8]/30 text-[#1db8f8] rounded-lg transition-all duration-200 text-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('delete', $stage)
                            <form action="{{ route('settings.project-stages.destroy', $stage) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المرحلة؟')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-all duration-200 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
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
    <div class="text-center py-12">
        <i class="fas fa-layer-group text-6xl text-gray-500 mb-4"></i>
        <p class="text-gray-400 text-lg">لا توجد مراحل</p>
        @can('create', \App\Models\StageSetting::class)
        <a href="{{ route('settings.project-stages.create') }}" class="mt-4 inline-block px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-plus ml-2"></i>
            إضافة مرحلة جديدة
        </a>
        @endcan
    </div>
    @endif
</div>
@endsection
