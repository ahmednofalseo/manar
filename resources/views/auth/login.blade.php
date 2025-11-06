@extends('layouts.app')

@section('title', 'تسجيل الدخول - المنار')

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
            <button onclick="toggleLanguage()" class="absolute top-0 left-0 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300">
                EN
            </button>
            
            <!-- Logo -->
            <div class="text-center pt-2">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <i class="fas fa-tower-cell lighthouse-icon text-4xl text-white"></i>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">المنار</h1>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-white mb-2">تسجيل الدخول إلى نظام المنار</h2>
                <p class="text-gray-300 text-sm md:text-base">أدخل بيانات حسابك للمتابعة وإدارة المشاريع والفواتير</p>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Field -->
            <div>
                <div class="input-icon-wrapper">
                    <i class="fas fa-envelope icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="name@example.com"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full bg-white/90 text-gray-800 rounded-lg px-4 py-3 pr-11 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 input-glow transition-all duration-300"
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
                        placeholder="ادخل كلمة المرور"
                        required
                        autocomplete="current-password"
                        class="w-full bg-white/90 text-gray-800 rounded-lg px-4 py-3 pr-11 pl-11 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 input-glow transition-all duration-300"
                    >
                    <button 
                        type="button"
                        data-toggle-password
                        data-input-id="password"
                        data-icon-id="password-toggle-icon"
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

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-white cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="w-4 h-4 text-primary-500 bg-white/20 border-gray-300 rounded focus:ring-primary-500 focus:ring-2"
                    >
                    <span>تذكرني</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-primary-400 hover:text-primary-300 transition-colors duration-200">
                    نسيت كلمة المرور؟
                </a>
            </div>

            <!-- Login Button -->
            <button 
                type="submit"
                class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg btn-primary text-lg transition-all duration-300"
            >
                <i class="fas fa-sign-in-alt ml-2"></i>
                تسجيل الدخول
            </button>
        </form>

        <!-- Bottom Links -->
        <div class="mt-6">
            <a href="#" class="flex items-center justify-center gap-2 w-full bg-white/10 hover:bg-white/20 text-white py-2.5 px-4 rounded-lg transition-all duration-300 text-sm">
                <i class="fas fa-life-ring"></i>
                <span>الدعم الفني</span>
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-white/10 text-center">
            <p class="text-gray-400 text-xs">
                © 2025 نظام المنار – جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</div>
@endsection
