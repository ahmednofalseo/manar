@extends('layouts.app')

@section('title', __('Login') . ' - ' . \App\Helpers\SettingsHelper::systemName())

@section('content')
<div class="min-h-screen pattern-bg flex items-center justify-center p-4 relative">
    <!-- Geometric Pattern Overlay -->
    <div class="geometric-pattern"></div>
    
    <!-- Grid Lines -->
    <div class="grid-lines"></div>
    
    <!-- Floating Geometric Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>
    <div class="floating-shape shape-4"></div>
    <div class="floating-shape shape-5"></div>
    
    <!-- Pulsing Circles -->
    <div class="pulse-circle pulse-circle-1"></div>
    <div class="pulse-circle pulse-circle-2"></div>
    <div class="pulse-circle pulse-circle-3"></div>
    
    <!-- Connecting Lines -->
    <div class="connecting-lines">
        <div class="line line-1"></div>
        <div class="line line-2"></div>
        <div class="line line-3"></div>
    </div>
    <!-- Toast Notifications -->
    @if(session('success'))
    <div class="toast-auto-hide toast fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white" style="z-index: 9999;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast-auto-hide toast fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white" style="z-index: 9999;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Login Card -->
    <div class="glass-card w-full max-w-md rounded-2xl p-8 md:p-10 shadow-2xl relative z-10">
        <!-- Header -->
        <div class="relative mb-8">
            <!-- Language Toggle -->
            <a href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="absolute top-0 left-0 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300">
                {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
            </a>
            
            <!-- Logo -->
            <div class="text-center pt-2">
                @php
                    $systemName = \App\Helpers\SettingsHelper::systemName();
                    $systemLogo = \App\Helpers\SettingsHelper::systemLogo();
                @endphp
                <div class="flex items-center justify-center gap-3 mb-4 flex-wrap">
                    @if($systemLogo)
                        <img src="{{ $systemLogo }}" alt="{{ $systemName }}" class="h-16 w-auto object-contain flex-shrink-0">
                    @else
                        <i class="fas fa-tower-cell lighthouse-icon text-4xl text-white flex-shrink-0"></i>
                        <h1 class="text-3xl md:text-4xl font-bold text-white">{{ $systemName }}</h1>
                    @endif
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-white mb-2">{{ __('Login') }} {{ app()->getLocale() === 'ar' ? 'إلى نظام' : 'to' }} {{ $systemName }}</h2>
                <p class="text-gray-300 text-sm md:text-base">{{ app()->getLocale() === 'ar' ? 'أدخل بيانات حسابك للمتابعة وإدارة المشاريع والفواتير' : 'Enter your credentials to continue and manage projects and invoices' }}</p>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-gray-300 text-sm mb-2">{{ __('Email') }}</label>
                <div class="input-icon-wrapper">
                    <i class="fas fa-envelope icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="{{ __('Email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full bg-white/10 text-white placeholder-gray-400 rounded-lg px-4 py-3 {{ app()->getLocale() === 'ar' ? 'pr-11' : 'pl-11' }} border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-400/40 focus:border-primary-400/40 input-glow transition-all duration-300"
                    >
                </div>
                @error('email')
                <p class="mt-1 text-sm text-red-400 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-gray-300 text-sm mb-2">{{ __('Password') }}</label>
                <div class="input-icon-wrapper password-input">
                    <i class="fas fa-lock icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        placeholder="{{ __('Password') }}"
                        required
                        autocomplete="current-password"
                        class="w-full bg-white/10 text-white placeholder-gray-400 rounded-lg px-4 py-3 {{ app()->getLocale() === 'ar' ? 'pr-11 pl-11' : 'pl-11 pr-11' }} border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-400/40 focus:border-primary-400/40 input-glow transition-all duration-300"
                    >
                    <button 
                        type="button"
                        data-toggle-password
                        data-input-id="password"
                        data-icon-id="password-toggle-icon"
                        class="password-toggle"
                    >
                        <i id="password-toggle-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                <p class="mt-1 text-sm text-red-400 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-white cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="w-4 h-4 text-primary-500 bg-white/20 border-gray-300 rounded focus:ring-primary-500 focus:ring-2"
                    >
                    <span>{{ __('Remember Me') }}</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-primary-400 hover:text-primary-300 transition-colors duration-200 font-semibold" style="color: #4787a7 !important;">
                    {{ __('Forgot Password?') }}
                </a>
            </div>

            <!-- Login Button -->
            <button 
                type="submit"
                class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg border border-primary-400/30 hover:border-primary-400/50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
                <i class="fas fa-sign-in-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Login') }}
            </button>
        </form>

        <!-- Bottom Links -->
        <div class="mt-6">
            @php
                $supportEmail = \App\Models\Setting::get('support_email');
                $supportPhone = \App\Models\Setting::get('support_phone');
                $supportWhatsapp = \App\Models\Setting::get('support_whatsapp');
            @endphp
            @if($supportEmail || $supportPhone || $supportWhatsapp)
            <div class="space-y-2">
                @if($supportEmail)
                <a href="mailto:{{ $supportEmail }}" class="flex items-center justify-center gap-2 w-full bg-white/10 hover:bg-white/20 text-white py-2.5 px-4 rounded-lg transition-all duration-300 text-sm">
                    <i class="fas fa-envelope"></i>
                    <span>الدعم الفني: {{ $supportEmail }}</span>
                </a>
                @endif
                @if($supportPhone)
                <a href="tel:{{ $supportPhone }}" class="flex items-center justify-center gap-2 w-full bg-white/10 hover:bg-white/20 text-white py-2.5 px-4 rounded-lg transition-all duration-300 text-sm">
                    <i class="fas fa-phone"></i>
                    <span>{{ $supportPhone }}</span>
                </a>
                @endif
                @if($supportWhatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" class="flex items-center justify-center gap-2 w-full bg-white/10 hover:bg-white/20 text-white py-2.5 px-4 rounded-lg transition-all duration-300 text-sm">
                    <i class="fab fa-whatsapp"></i>
                    <span>واتساب: {{ $supportWhatsapp }}</span>
                </a>
                @endif
            </div>
            @else
            <a href="#" class="flex items-center justify-center gap-2 w-full bg-white/10 hover:bg-white/20 text-white py-2.5 px-4 rounded-lg transition-all duration-300 text-sm">
                <i class="fas fa-life-ring"></i>
                <span>الدعم الفني</span>
            </a>
            @endif
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-white/10 text-center">
            <p class="text-gray-400 text-xs">
                © {{ date('Y') }} نظام {{ \App\Helpers\SettingsHelper::systemName() }} – جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</div>
@endsection
