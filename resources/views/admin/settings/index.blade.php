@extends('layouts.dashboard')

@section('title', __('Settings') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Settings'))

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button.active {
        background: rgba(29, 184, 248, 0.2);
        border-color: #1db8f8;
    }
    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
    }
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Settings') }}</h1>
</div>

<!-- Toast Notifications -->
@if(session('success'))
<div class="mb-6 p-4 rounded-lg shadow-lg bg-green-500/90 text-white animate-slide-in border border-green-400/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2 hover:text-gray-200 transition-colors">
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
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2 hover:text-gray-200 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Tabs -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-6" x-data="{ activeTab: '{{ $group }}' }">
    <div class="flex flex-wrap gap-3 mb-6 border-b border-white/10 pb-4">
        <button 
            @click="activeTab = 'general'; window.location.href = '{{ route('admin.settings.index', ['group' => 'general']) }}'"
            :class="activeTab === 'general' ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300'"
            class="tab-button px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-cog {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('General Settings') }}
        </button>
        <button 
            @click="activeTab = 'email'; window.location.href = '{{ route('admin.settings.index', ['group' => 'email']) }}'"
            :class="activeTab === 'email' ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300'"
            class="tab-button px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-envelope {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Email Settings') }}
        </button>
        <button 
            @click="activeTab = 'support'; window.location.href = '{{ route('admin.settings.index', ['group' => 'support']) }}'"
            :class="activeTab === 'support' ? 'bg-primary-400/20 border-primary-500 text-primary-400' : 'bg-white/5 border-white/10 text-gray-300'"
            class="tab-button px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-semibold"
        >
            <i class="fas fa-life-ring {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Support Information') }}
        </button>
    </div>

    <!-- General Settings -->
    @if($group === 'general')
    <form action="{{ route('admin.settings.update', 'general') }}" method="POST" enctype="multipart/form-data" x-data="settingsForm()">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- System Name -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('System Name') }}</label>
                <input 
                    type="text" 
                    name="system_name" 
                    value="{{ old('system_name', $settings['system_name'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                @error('system_name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- System Logo -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('System Logo') }}</label>
                <div class="flex items-center gap-4">
                    @php
                        $currentLogo = $settings['system_logo'] ?? null;
                    @endphp
                    @if($currentLogo)
                        @if(\Storage::disk('public')->exists($currentLogo))
                            <img src="{{ asset('storage/' . $currentLogo) }}" alt="Logo" class="logo-preview rounded-lg object-contain bg-white/5 p-2" onerror="this.onerror=null; this.src=''; this.style.display='none';">
                        @else
                            <img src="{{ asset('storage/' . $currentLogo) }}" alt="Logo" class="logo-preview rounded-lg object-contain bg-white/5 p-2" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-32 h-20 bg-white/5 rounded-lg flex items-center justify-center" style="display: none;">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                    @else
                    <div class="w-32 h-20 bg-white/5 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <input 
                            type="file" 
                            name="system_logo" 
                            accept="image/*"
                            @change="handleLogoChange($event)"
                            class="hidden"
                            id="logoInput"
                        >
                        <label for="logoInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200 text-sm inline-block">
                            <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                            {{ __('Select Logo') }}
                        </label>
                        <p class="text-gray-400 text-xs mt-1">{{ __('JPG, PNG, SVG (Max 2MB)') }}</p>
                        @error('system_logo')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div x-show="logoPreview" class="mt-4">
                    <img :src="logoPreview" alt="Preview" class="logo-preview rounded-lg">
                </div>
            </div>

            <!-- Language -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Default Language') }}</label>
                <select 
                    name="language" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                    <option value="ar" {{ old('language', $settings['language'] ?? 'ar') === 'ar' ? 'selected' : '' }}>{{ __('Arabic') }}</option>
                    <option value="en" {{ old('language', $settings['language'] ?? 'ar') === 'en' ? 'selected' : '' }}>English</option>
                </select>
                @error('language')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Timezone -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Timezone') }}</label>
                <select 
                    name="timezone" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                    <option value="Asia/Riyadh" {{ old('timezone', $settings['timezone'] ?? 'Asia/Riyadh') === 'Asia/Riyadh' ? 'selected' : '' }}>{{ __('Riyadh (GMT+3)') }}</option>
                    <option value="Asia/Dubai" {{ old('timezone', $settings['timezone'] ?? 'Asia/Riyadh') === 'Asia/Dubai' ? 'selected' : '' }}>{{ __('Dubai (GMT+4)') }}</option>
                    <option value="Africa/Cairo" {{ old('timezone', $settings['timezone'] ?? 'Asia/Riyadh') === 'Africa/Cairo' ? 'selected' : '' }}>{{ __('Cairo (GMT+2)') }}</option>
                    <option value="UTC" {{ old('timezone', $settings['timezone'] ?? 'Asia/Riyadh') === 'UTC' ? 'selected' : '' }}>{{ __('UTC (GMT+0)') }}</option>
                </select>
                @error('timezone')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Format -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Date Format') }}</label>
                <select 
                    name="date_format" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                    <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>2025-01-06</option>
                    <option value="d/m/Y" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>06/01/2025</option>
                    <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>01/06/2025</option>
                    <option value="d-m-Y" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') === 'd-m-Y' ? 'selected' : '' }}>06-01-2025</option>
                </select>
                @error('date_format')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Format -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Time Format') }}</label>
                <select 
                    name="time_format" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                    <option value="H:i" {{ old('time_format', $settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' }}>{{ __('24 Hour (14:30)') }}</option>
                    <option value="h:i A" {{ old('time_format', $settings['time_format'] ?? 'H:i') === 'h:i A' ? 'selected' : '' }}>{{ __('12 Hour (02:30 PM)') }}</option>
                </select>
                @error('time_format')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <button 
                type="submit"
                class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 font-semibold"
            >
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Save Settings') }}
            </button>
        </div>
    </form>
    @endif

    <!-- Email Settings -->
    @if($group === 'email')
    <form action="{{ route('admin.settings.update', 'email') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Mail Mailer -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Mail Type') }}</label>
                <select 
                    name="mail_mailer" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                    <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer'] ?? 'smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                </select>
                @error('mail_mailer')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail Host -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Mail Server (Host)') }}</label>
                <input 
                    type="text" 
                    name="mail_host" 
                    value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                @error('mail_host')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail Port -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Mail Port') }}</label>
                <input 
                    type="number" 
                    name="mail_port" 
                    value="{{ old('mail_port', $settings['mail_port'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                @error('mail_port')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail Username -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Username') }}</label>
                <input 
                    type="text" 
                    name="mail_username" 
                    value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('mail_username')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail Password -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">كلمة المرور</label>
                <input 
                    type="password" 
                    name="mail_password" 
                    value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('mail_password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail Encryption -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Encryption') }}</label>
                <select 
                    name="mail_encryption" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                    <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                </select>
                @error('mail_encryption')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail From Address -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('From Address') }}</label>
                <input 
                    type="email" 
                    name="mail_from_address" 
                    value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                @error('mail_from_address')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mail From Name -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('From Name') }}</label>
                <input 
                    type="text" 
                    name="mail_from_name" 
                    value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    required
                >
                @error('mail_from_name')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <button 
                type="button"
                onclick="testEmail()"
                class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-all duration-200 font-semibold"
            >
                <i class="fas fa-paper-plane {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Test Email') }}
            </button>
            <button 
                type="submit"
                class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 font-semibold"
            >
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Save Settings') }}
            </button>
        </div>
    </form>
    @endif

    <!-- Support Settings -->
    @if($group === 'support')
    <form action="{{ route('admin.settings.update', 'support') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Support Email -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Support Email') }}</label>
                <input 
                    type="email" 
                    name="support_email" 
                    value="{{ old('support_email', $settings['support_email'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('support_email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Support Phone -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Support Phone') }}</label>
                <input 
                    type="text" 
                    name="support_phone" 
                    value="{{ old('support_phone', $settings['support_phone'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('support_phone')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Support WhatsApp -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Support WhatsApp') }}</label>
                <input 
                    type="text" 
                    name="support_whatsapp" 
                    value="{{ old('support_whatsapp', $settings['support_whatsapp'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('support_whatsapp')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Support Address -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Support Address') }}</label>
                <textarea 
                    name="support_address" 
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >{{ old('support_address', $settings['support_address'] ?? '') }}</textarea>
                @error('support_address')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Support Website -->
            <div>
                <label class="block text-gray-300 text-sm font-semibold mb-2">{{ __('Support Website') }}</label>
                <input 
                    type="url" 
                    name="support_website" 
                    value="{{ old('support_website', $settings['support_website'] ?? '') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('support_website')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <button 
                type="submit"
                class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 font-semibold"
            >
                <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Save Settings') }}
            </button>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script>
function settingsForm() {
    return {
        logoPreview: null,
        handleLogoChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
}

function testEmail() {
    const email = prompt('{{ __('Enter email address to send test email:') }}');
    if (email && email.includes('@')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.settings.test-email') }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="test_email" value="${email}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@endsection

