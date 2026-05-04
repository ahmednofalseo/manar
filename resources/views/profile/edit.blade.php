@extends('layouts.dashboard')

@section('title', __('Edit personal account') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit personal account'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .avatar-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="mb-6 p-4 rounded-lg shadow-lg bg-green-500/90 text-white animate-slide-in border border-green-400/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.parentElement.remove()" class="mr-2 hover:text-gray-200 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-lg shadow-lg bg-red-500/90 text-white animate-slide-in border border-red-400/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.parentElement.remove()" class="mr-2 hover:text-gray-200 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit personal account') }}</h1>
    <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" x-data="profileForm({
    avatarSizeMsg: @json(__('Profile avatar max size alert')),
    avatarTypeMsg: @json(__('Profile avatar image only alert')),
})">
    @csrf
    @method('PUT')

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Profile photo section') }}</h2>
        <div class="flex items-center gap-6">
            <div class="flex-shrink-0">
                @php
                    $currentAvatar = $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1db8f8&color=fff&size=128';
                @endphp
                <img :src="avatarPreview || '{{ $currentAvatar }}'"
                     alt="{{ $user->name }}"
                     class="avatar-preview rounded-full border-4 border-primary-400/50 object-cover"
                     x-show="true">
            </div>
            <div class="flex-1">
                <input
                    type="file"
                    name="avatar"
                    accept="image/*"
                    @change="handleAvatarChange($event)"
                    class="hidden"
                    id="avatarInput"
                >
                <label for="avatarInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200 text-sm inline-block mb-2">
                    <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Choose photo') }}
                </label>
                <p class="text-gray-400 text-xs">{{ __('Profile avatar formats hint') }}</p>
                @error('avatar')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Profile full name') }} <span class="text-red-400">*</span></label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('name') border-red-500 @enderror"
                    placeholder="{{ __('Full name placeholder example') }}"
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
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('email') border-red-500 @enderror"
                    placeholder="example@manar.com"
                >
                @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Phone') }}</label>
                <input
                    type="tel"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('phone') border-red-500 @enderror"
                    placeholder="{{ __('Phone placeholder SA') }}"
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
                    value="{{ old('national_id', $user->national_id) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('national_id') border-red-500 @enderror"
                    placeholder="{{ __('National ID digits hint') }}"
                >
                @error('national_id')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Job title') }}</label>
                <input
                    type="text"
                    name="job_title"
                    value="{{ old('job_title', $user->job_title) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('job_title') border-red-500 @enderror"
                    placeholder="{{ __('Job title placeholder example') }}"
                >
                @error('job_title')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Change password') }}</h2>
        <p class="text-gray-400 text-sm mb-4">{{ __('Profile password optional hint') }}</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Current password') }} <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input
                        :type="showCurrentPassword ? 'text' : 'password'"
                        name="current_password"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('current_password') border-red-500 @enderror"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showCurrentPassword = !showCurrentPassword"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                        <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                @error('current_password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('New password') }}</label>
                <div class="relative">
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
                @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Confirm new password') }}</label>
                <div class="relative">
                    <input
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        name="password_confirmation"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="••••••••"
                    >
                    <button
                        type="button"
                        @click="showPasswordConfirmation = !showPasswordConfirmation"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                        <i :class="showPasswordConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('dashboard.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            {{ __('Cancel') }}
        </a>
        <button
            type="submit"
            class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 font-semibold"
        >
            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Save changes') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function profileForm(i18n) {
    return {
        avatarPreview: @if($user->avatar) '{{ asset("storage/" . $user->avatar) }}' @else null @endif,
        showCurrentPassword: false,
        showPassword: false,
        showPasswordConfirmation: false,
        avatarSizeMsg: i18n.avatarSizeMsg,
        avatarTypeMsg: i18n.avatarTypeMsg,
        init() {
            @if($user->avatar)
            this.avatarPreview = '{{ asset("storage/" . $user->avatar) }}';
            @endif
        },
        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert(this.avatarSizeMsg);
                    event.target.value = '';
                    return;
                }
                if (!file.type.match('image.*')) {
                    alert(this.avatarTypeMsg);
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    };
}
</script>
@endpush

@endsection
