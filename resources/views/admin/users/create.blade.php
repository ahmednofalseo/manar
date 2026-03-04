@extends('layouts.dashboard')

@section('title', __('Add New Employee') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Add New Employee'))

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Add New Employee') }}</h1>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" x-data="userForm()">
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
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Example: Mohammed Ahmed Al-Ali') }}"
                >
                @error('name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Email') }} <span class="text-red-400">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    value="{{ old('email') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="example@manar.com"
                >
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Mobile Number') }}</label>
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="05XXXXXXXX"
                >
                @error('phone')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('National ID') }}</label>
                <input 
                    type="text" 
                    name="national_id" 
                    value="{{ old('national_id') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('10 digits') }}"
                >
                @error('national_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Password') }} <span class="text-red-400">*</span></label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Password Confirmation') }} <span class="text-red-400">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="••••••••"
                >
            </div>
        </div>
    </div>

    <!-- Job Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Job Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Job Title/Department') }}</label>
                <select name="job_title" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Job') }}</option>
                    <option value="مهندس معماري">{{ __('Architectural Engineer') }}</option>
                    <option value="مهندس إنشائي">{{ __('Structural Engineer') }}</option>
                    <option value="مهندس كهرباء">{{ __('Electrical Engineer') }}</option>
                    <option value="مهندس ميكانيكي">{{ __('Mechanical Engineer') }}</option>
                    <option value="مدير مشروع">{{ __('Project Manager') }}</option>
                    <option value="إداري">{{ __('Administrative') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Practice License Number/File') }}</label>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="practice_license_no" 
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('License Number') }}"
                    >
                    <input 
                        type="file" 
                        name="practice_license_file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        id="licenseFile"
                        @change="handleLicenseFile($event)"
                    >
                    <label for="licenseFile" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer text-sm">
                        <i class="fas fa-upload"></i>
                    </label>
                </div>
                <p class="text-gray-400 text-xs mt-1" x-show="licenseFileName" x-text="licenseFileName"></p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Engineer Rank Expiry Date') }}</label>
                <input 
                    type="date" 
                    name="engineer_rank_expiry" 
                    value="{{ old('engineer_rank_expiry') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Roles & Avatar -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Roles & Avatar') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Roles') }} <span class="text-red-400">*</span></label>
                <select name="roles[]" multiple required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 h-32">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('roles') && in_array($role->id, old('roles')) ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-gray-400 text-xs mt-1">{{ __('You can select multiple roles (Press Ctrl/CMD for multiple selection)') }}</p>
                @error('roles')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Avatar') }}</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-primary-400/20 rounded-full flex items-center justify-center overflow-hidden">
                        <img x-show="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                        <i x-show="!avatarPreview" class="fas fa-user-circle text-primary-400 text-4xl"></i>
                    </div>
                    <div class="flex-1">
                        <input 
                            type="file" 
                            name="avatar"
                            accept=".jpg,.jpeg,.png"
                            class="hidden"
                            id="avatarInput"
                            @change="handleAvatarSelect($event)"
                        >
                        <label for="avatarInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200 text-sm inline-block">
                            <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                            {{ __('Select Image') }}
                        </label>
                        <p class="text-gray-400 text-xs mt-1">{{ __('JPG, PNG (Max 2MB)') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
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
function userForm() {
    return {
        avatarPreview: null,
        licenseFileName: null,
        handleAvatarSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        handleLicenseFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.licenseFileName = file.name;
            }
        }
    }
}
</script>
@endpush

@endsection


