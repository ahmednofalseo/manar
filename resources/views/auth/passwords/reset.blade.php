@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور - ' . \App\Helpers\SettingsHelper::systemName())

@section('content')
<div class="min-h-screen pattern-bg flex items-center justify-center p-4 relative">
    <!-- Geometric Pattern Overlay -->
    <div class="geometric-pattern"></div>
    
    <!-- Grid Lines -->
    <div class="grid-lines"></div>
    
    <!-- Toast Notifications -->
    @if(session('status'))
    <div class="toast-auto-hide toast fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white" style="z-index: 9999;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Reset Password Card -->
    <div class="glass-card w-full max-w-md rounded-2xl p-8 md:p-10 shadow-2xl relative z-10">
        <!-- Header -->
        <div class="text-center mb-8">
            @php
                $systemName = \App\Helpers\SettingsHelper::systemName();
                $systemLogo = \App\Helpers\SettingsHelper::systemLogo();
            @endphp
            <div class="flex items-center justify-center gap-3 mb-4 flex-wrap">
                @if($systemLogo)
                    <img src="{{ $systemLogo }}" alt="{{ $systemName }}" class="h-12 w-auto object-contain flex-shrink-0">
                @else
                    <i class="fas fa-tower-cell lighthouse-icon text-3xl text-white flex-shrink-0"></i>
                @endif
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">إعادة تعيين كلمة المرور</h2>
            <p class="text-gray-300 text-sm md:text-base">أدخل كلمة المرور الجديدة</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Field -->
            <div>
                <div class="input-icon-wrapper">
                    <i class="fas fa-envelope icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email', $email) }}"
                        placeholder="name@example.com"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full bg-white/10 text-white placeholder-gray-400 rounded-lg px-4 py-3 pr-11 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-400/40 focus:border-primary-400/40 input-glow transition-all duration-300 @error('email') border-red-500 @enderror"
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
                <div class="input-icon-wrapper password-input">
                    <i class="fas fa-lock icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        placeholder="كلمة المرور الجديدة"
                        required
                        autocomplete="new-password"
                        class="w-full bg-white/10 text-white placeholder-gray-400 rounded-lg px-4 py-3 pr-11 pl-11 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-400/40 focus:border-primary-400/40 input-glow transition-all duration-300 @error('password') border-red-500 @enderror"
                    >
                    <button 
                        type="button"
                        class="password-toggle"
                        onclick="togglePasswordVisibility('password', 'password-toggle-icon')"
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

            <!-- Confirm Password Field -->
            <div>
                <div class="input-icon-wrapper password-input">
                    <i class="fas fa-lock icon"></i>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation"
                        placeholder="تأكيد كلمة المرور"
                        required
                        autocomplete="new-password"
                        class="w-full bg-white/10 text-white placeholder-gray-400 rounded-lg px-4 py-3 pr-11 pl-11 border border-white/20 focus:outline-none focus:ring-2 focus:ring-primary-400/40 focus:border-primary-400/40 input-glow transition-all duration-300"
                    >
                    <button 
                        type="button"
                        class="password-toggle"
                        onclick="togglePasswordVisibility('password_confirmation', 'password-confirmation-toggle-icon')"
                    >
                        <i id="password-confirmation-toggle-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit"
                class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
                <i class="fas fa-key ml-2"></i>
                إعادة تعيين كلمة المرور
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-primary-400 hover:text-primary-300 transition-colors duration-200 text-sm">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة إلى تسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection

