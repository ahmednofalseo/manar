<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Access Denied') }} - {{ \App\Helpers\SettingsHelper::systemName() }}</title>
    
    <!-- TailwindCSS -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- TailwindCSS CDN (fallback when Vite manifest not found) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            'cairo': ['Cairo', 'sans-serif'],
                        },
                        colors: {
                            'primary': {
                                DEFAULT: '#173343',
                                50: '#e8f0f4',
                                100: '#d1e1e9',
                                200: '#a3c3d3',
                                300: '#75a5bd',
                                400: '#4787a7',
                                500: '#173343',
                                600: '#122936',
                                700: '#0e1f29',
                                800: '#09141c',
                                900: '#050a0f',
                            },
                        },
                    },
                },
            }
        </script>
    @endif
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
            }
            50% {
                box-shadow: 0 0 40px rgba(239, 68, 68, 0.6);
            }
        }
        
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="font-cairo antialiased bg-gradient-to-br from-[#0e1f27] to-[#173343] min-h-screen flex items-center justify-center p-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-2xl w-full">
        <div class="glass-card rounded-2xl p-8 md:p-12 text-center">
            <!-- Icon -->
            <div class="mb-6 flex justify-center">
                <div class="relative w-32 h-32 md:w-40 md:h-40 animate-float">
                    <div class="absolute inset-0 bg-red-500/20 rounded-full animate-pulse-glow"></div>
                    <div class="relative w-full h-full bg-red-500/10 rounded-full flex items-center justify-center border-4 border-red-500/30">
                        <i class="fas fa-shield-exclamation text-red-400 text-5xl md:text-6xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Error Code -->
            <h1 class="text-6xl md:text-8xl font-bold text-red-400 mb-4">403</h1>
            
            <!-- Title -->
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                {{ __('Access Denied') }}
            </h2>
            
            <!-- Message -->
            <p class="text-gray-300 text-lg md:text-xl mb-8 leading-relaxed">
                {{ __('Sorry, you do not have permission to access this page or perform this action.') }}
            </p>
            
            <!-- Additional Info -->
            <div class="bg-white/5 rounded-lg p-4 md:p-6 mb-8 border border-white/10">
                <div class="flex items-start gap-3 text-right">
                    <i class="fas fa-info-circle text-primary-400 mt-1 flex-shrink-0"></i>
                    <div class="flex-1">
                        <p class="text-gray-300 text-sm md:text-base">
                            {{ __('If you believe you should have access to this page, please contact your administrator.') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a 
                    href="{{ route('dashboard.index') }}" 
                    class="w-full sm:w-auto px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                    <i class="fas fa-home"></i>
                    {{ __('Go to Dashboard') }}
                </a>
                <button 
                    onclick="window.history.back()" 
                    class="w-full sm:w-auto px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 font-semibold flex items-center justify-center gap-2"
                >
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    {{ __('Go Back') }}
                </button>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-primary-400/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-red-500/10 rounded-full blur-xl"></div>
    </div>
</body>
</html>


