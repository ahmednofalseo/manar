@extends('layouts.app')

@section('title', 'تأكيد البريد الإلكتروني - ' . \App\Helpers\SettingsHelper::systemName())

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

    <!-- Verification Card -->
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
            <div class="w-20 h-20 bg-primary-400/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope-open text-primary-400 text-4xl"></i>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">تحقق من بريدك الإلكتروني</h2>
            <p class="text-gray-300 text-sm md:text-base">
                قبل المتابعة، يرجى التحقق من بريدك الإلكتروني للحصول على رابط التحقق.
            </p>
        </div>

        <!-- Info -->
        <div class="bg-primary-400/10 border border-primary-400/20 rounded-lg p-4 mb-6">
            <p class="text-primary-300 text-sm text-center">
                <i class="fas fa-info-circle ml-2"></i>
                إذا لم تستلم البريد الإلكتروني، انقر على الزر أدناه لإرسال رابط جديد.
            </p>
        </div>

        <!-- Resend Form -->
        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
            @csrf
            <button 
                type="submit"
                class="w-full bg-primary-500 hover:bg-primary-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
                <i class="fas fa-paper-plane ml-2"></i>
                إرسال رابط التحقق مرة أخرى
            </button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button 
                type="submit"
                class="w-full bg-white/10 hover:bg-white/20 text-white py-3 px-6 rounded-lg transition-all duration-300 text-sm"
            >
                <i class="fas fa-sign-out-alt ml-2"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>
</div>
@endsection



