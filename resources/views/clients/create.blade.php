@extends('layouts.dashboard')

@section('title', __('Add new client') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Add new client'))

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
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('New Client') }}</h1>
    <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" x-data="clientForm(@js(__('Selected files count label')), @js([__('File size unit B'), __('File size unit KB'), __('File size unit MB'), __('File size unit GB')]))">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Full Name') }} <span class="text-red-400">*</span></label>
                <input
                    type="text"
                    name="name"
                    required
                    value="{{ old('name') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Full name placeholder example') }}"
                >
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Full Name (English)') }}</label>
                <input
                    type="text"
                    name="name_en"
                    value="{{ old('name_en') }}"
                    lang="en"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Full name English placeholder') }}"
                >
                @error('name_en')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Client Type') }} <span class="text-red-400">*</span></label>
                <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Type') }}</option>
                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>{{ __('Individual') }}</option>
                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>{{ __('Company') }}</option>
                    <option value="government" {{ old('type') == 'government' ? 'selected' : '' }}>{{ __('Government Entity') }}</option>
                </select>
                @error('type')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('National ID or CR') }}</label>
                <input 
                    type="text" 
                    name="national_id_or_cr" 
                    value="{{ old('national_id_or_cr') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('National ID or CR placeholder') }}"
                >
                @error('national_id_or_cr')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Phone') }} <span class="text-red-400">*</span></label>
                <input 
                    type="tel" 
                    name="phone" 
                    required
                    value="{{ old('phone') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Phone placeholder SA') }}"
                >
                @error('phone')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Email') }}</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="example@email.com"
                >
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('City') }} <span class="text-red-400">*</span></label>
                <select name="city" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select city') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->name }}" {{ old('city') == $city->name ? 'selected' : '' }}>{{ $city->display_name }}</option>
                    @endforeach
                </select>
                @error('city')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('District') }}</label>
                <input 
                    type="text" 
                    name="district" 
                    value="{{ old('district') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('District name placeholder') }}"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">{{ __('Full address') }}</label>
                <textarea 
                    name="address" 
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Address detail placeholder') }}"
                >{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Attachments') }}</h2>
        <div>
            <label class="block text-gray-300 text-sm mb-2">{{ __('Upload attachments optional') }}</label>
            <div class="flex items-center gap-4">
                <input 
                    type="file" 
                    name="attachments[]"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="hidden"
                    id="attachmentsInput"
                    @change="handleFilesSelect($event)"
                >
                <label for="attachmentsInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200">
                    <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Select Files') }}
                </label>
                <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="filesCountLabel()"></span>
            </div>
            <p class="text-gray-400 text-xs mt-1">{{ __('Attachments formats hint') }}</p>
        </div>

        <!-- Selected Files List -->
        <div x-show="selectedFiles.length > 0" class="mt-4 space-y-2">
            <template x-for="(file, index) in selectedFiles" :key="index">
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file text-primary-400"></i>
                        <span class="text-white text-sm" x-text="file.name"></span>
                        <span class="text-gray-400 text-xs" x-text="formatFileSize(file.size)"></span>
                    </div>
                    <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Internal Notes -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Internal Notes') }}</h2>
        <div>
            <textarea 
                name="notes_internal" 
                rows="4"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="{{ __('Client internal notes placeholder') }}"
            >{{ old('notes_internal') }}</textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('clients.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function clientForm(selectedFilesCountTpl, fileSizeUnits) {
    return {
        selectedFiles: [],
        selectedFilesCountTpl,
        fileSizeUnits,

        filesCountLabel() {
            if (this.selectedFiles.length === 0) return '';
            return this.selectedFilesCountTpl.replace(':count', String(this.selectedFiles.length));
        },

        handleFilesSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files.map(file => ({
                name: file.name,
                size: file.size
            }));
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            const input = document.getElementById('attachmentsInput');
            if (input) {
                input.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 ' + (this.fileSizeUnits[0] || 'B');
            const k = 1024;
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            const unit = this.fileSizeUnits[i] || this.fileSizeUnits[this.fileSizeUnits.length - 1];
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + unit;
        }
    }
}
</script>
@endpush

@endsection
