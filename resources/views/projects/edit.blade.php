@extends('layouts.dashboard')

@section('title', __('Edit') . ' ' . __('Projects') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit') . ' ' . __('Projects'))

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
<div class="max-w-5xl mx-auto">
    <!-- Toast Notifications -->
    @if(session('success'))
    <div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 flex-1">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm sm:text-base">{{ session('success') }}</span>
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
            <div class="flex items-center gap-2 flex-1">
                <i class="fas fa-exclamation-circle"></i>
                <span class="text-sm sm:text-base">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="mr-2 flex-shrink-0">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="fixed top-20 left-4 right-4 sm:right-auto sm:left-4 z-[70] p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0 mb-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" x-transition>
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm sm:text-base font-semibold">{{ __('Please correct the following errors') }}:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1 mr-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="mr-2 flex-shrink-0 mt-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit') }} {{ __('Projects') }}</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.show', $project->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('View') }}
            </a>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('projects.update', $project->id) }}" enctype="multipart/form-data" x-data="projectForm()">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-400"></i>
                {{ __('Basic Information') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Project Name') }} <span class="text-red-400">*</span></label>
                    <input 
                        type="text" 
                        name="name"
                        value="{{ old('name', $project->name) }}"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Project Name Example') }}"
                    >
                    @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Project Number') }}</label>
                    <input 
                        type="text" 
                        name="project_number"
                        value="{{ old('project_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Will be generated automatically') }}"
                    >
                    @error('project_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Project Type') }} <span class="text-red-400">*</span></label>
                    <select 
                        name="type"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">{{ __('Select Project Type') }}</option>
                        <option value="تصميم" {{ old('type', $project->type) == 'تصميم' ? 'selected' : '' }}>{{ __('Design') }}</option>
                        <option value="تصميم وإشراف" {{ old('type', $project->type) == 'تصميم وإشراف' ? 'selected' : '' }}>{{ __('Design & Supervision') }}</option>
                        <option value="إشراف" {{ old('type', $project->type) == 'إشراف' ? 'selected' : '' }}>{{ __('Supervision') }}</option>
                        <option value="تقرير فني" {{ old('type', $project->type) == 'تقرير فني' ? 'selected' : '' }}>{{ __('Technical Report') }}</option>
                        <option value="تقرير دفاع مدني" {{ old('type', $project->type) == 'تقرير دفاع مدني' ? 'selected' : '' }}>{{ __('Civil Defense Report') }}</option>
                        <option value="تصميم دفاع مدني" {{ old('type', $project->type) == 'تصميم دفاع مدني' ? 'selected' : '' }}>{{ __('Civil Defense Design') }}</option>
                        <option value="تعديلات" {{ old('type', $project->type) == 'تعديلات' ? 'selected' : '' }}>{{ __('Modifications') }}</option>
                        <option value="استشارات" {{ old('type', $project->type) == 'استشارات' ? 'selected' : '' }}>{{ __('Consultations') }}</option>
                    </select>
                    @error('type')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('City') }} <span class="text-red-400">*</span></label>
                    <select 
                        name="city"
                        required
                        x-model="city"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">{{ __('Select City') }}</option>
                        <option value="الرياض" {{ old('city', $project->city) == 'الرياض' ? 'selected' : '' }}>{{ __('Riyadh') }}</option>
                        <option value="جدة" {{ old('city', $project->city) == 'جدة' ? 'selected' : '' }}>{{ __('Jeddah') }}</option>
                        <option value="الدمام" {{ old('city', $project->city) == 'الدمام' ? 'selected' : '' }}>{{ __('Dammam') }}</option>
                        <option value="مكة" {{ old('city', $project->city) == 'مكة' ? 'selected' : '' }}>{{ __('Makkah') }}</option>
                        <option value="المدينة" {{ old('city', $project->city) == 'المدينة' ? 'selected' : '' }}>{{ __('Madinah') }}</option>
                    </select>
                    @error('city')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('District') }}</label>
                    <input 
                        type="text" 
                        name="district"
                        value="{{ old('district', $project->district) }}"
                        id="district"
                        autocomplete="off"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Enter District Name') }}"
                    >
                    @error('district')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Owner') }} <span class="text-red-400">*</span></label>
                    <input 
                        type="text" 
                        name="owner"
                        value="{{ old('owner') }}"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Enter Owner Name') }}"
                    >
                    @error('owner')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Value') }} <span class="text-red-400">*</span></label>
                    <input 
                        type="number" 
                        name="value"
                        value="{{ old('value') }}"
                        required
                        step="0.01"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    @error('value')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Contract Number') }}</label>
                    <input 
                        type="text" 
                        name="contract_number"
                        value="{{ old('contract_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('contract_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Contract File') }}</label>
                    <input 
                        type="file" 
                        name="contract_file"
                        accept=".pdf,.doc,.docx"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('contract_file')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Land Number') }}</label>
                    <input 
                        type="text" 
                        name="land_number"
                        id="land_number"
                        value="{{ old('land_number', $project->land_number) }}"
                        autocomplete="off"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Enter Land Number') }}"
                    >
                    @error('land_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Land Code') }}</label>
                    <input 
                        type="text" 
                        name="land_code"
                        id="land_code"
                        value="{{ old('land_code', $project->land_code) }}"
                        autocomplete="off"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Enter Land Code') }}"
                    >
                    @error('land_code')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Plan File') }}</label>
                    <input 
                        type="file" 
                        name="plan_file"
                        accept=".pdf,.jpg,.jpeg,.png,.dwg"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('plan_file')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Platforms & Third Party -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-link text-primary-400"></i>
                {{ __('Platforms & Third Party') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Baladi Platform Request Number') }}</label>
                    <input 
                        type="text" 
                        name="baladi_request_number"
                        value="{{ old('baladi_request_number') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('baladi_request_number')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Third Party Repeater -->
            <div x-data="thirdPartyRepeater()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">{{ __('Third Party') }}</h3>
                    <button type="button" @click="addItem()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                        <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Add') }}
                    </button>
                </div>
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-gray-300 text-sm mb-2">{{ __('Name/Entity') }}</label>
                                    <input 
                                        type="text" 
                                        :name="`third_party[${index}][name]`"
                                        x-model="item.name"
                                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-300 text-sm mb-2">{{ __('Date') }}</label>
                                    <input 
                                        type="date" 
                                        :name="`third_party[${index}][date]`"
                                        x-model="item.date"
                                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                                    >
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeItem(index)" class="w-full px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm">
                                        <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Stages -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-diagram-project text-primary-400"></i>
                {{ __('Stages') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="معماري" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Architectural') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="إنشائي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Structural') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="كهربائي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Electrical') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="ميكانيكي" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Mechanical') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="صحي/بيئي" {{ in_array('صحي/بيئي', old('stages', $project->stages ?? [])) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Sanitary/Environmental') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="تقديم للبلدية" {{ in_array('تقديم للبلدية', old('stages', $project->stages ?? [])) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Municipality Submission') }}</span>
                </label>
                <label class="flex items-center gap-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 cursor-pointer">
                    <input type="checkbox" name="stages[]" value="أخرى" {{ in_array('أخرى', old('stages', $project->stages ?? [])) ? 'checked' : '' }} class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                    <span class="text-white">{{ __('Other') }}</span>
                </label>
            </div>
        </div>

        <!-- Project Dates -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-calendar text-primary-400"></i>
                {{ __('Project Dates') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Start Date') }}</label>
                    <input 
                        type="date" 
                        name="start_date"
                        value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('End Date') }}</label>
                    <input 
                        type="date" 
                        name="end_date"
                        value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Team Assignment -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-users text-primary-400"></i>
                {{ __('Assign Team') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Project Manager') }}</label>
                    <select 
                        name="project_manager"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                        <option value="">{{ __('Select Project Manager') }}</option>
                        <option value="1">محمد أحمد</option>
                        <option value="2">فاطمة سالم</option>
                        <option value="3">خالد مطر</option>
                    </select>
                    @error('project_manager')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 text-sm mb-2">{{ __('Engineers/Technicians') }}</label>
                    <select 
                        name="engineers[]"
                        multiple
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        size="4"
                    >
                        <option value="1">محمد أحمد - مهندس معماري</option>
                        <option value="2">فاطمة سالم - مهندسة إنشائية</option>
                        <option value="3">خالد مطر - مهندس كهرباء</option>
                        <option value="4">سارة علي - مهندسة ميكانيكا</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-400">{{ __('Press Ctrl/Command to select multiple') }}</p>
                    @error('engineers')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Internal Notes -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <i class="fas fa-sticky-note text-primary-400"></i>
                {{ __('Internal Notes') }}
            </h2>

            <textarea 
                name="internal_notes"
                rows="4"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="{{ __('Internal Notes Placeholder') }}"
            >{{ old('internal_notes') }}</textarea>
            @error('internal_notes')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('projects.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Save Project') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function projectForm() {
        return {
            city: '{{ old("city", "") }}'
        }
    }

    function thirdPartyRepeater() {
        return {
            items: [],
            addItem() {
                this.items.push({ name: '', date: '' });
            },
            removeItem(index) {
                this.items.splice(index, 1);
            }
        }
    }
</script>
@endpush

@endsection
