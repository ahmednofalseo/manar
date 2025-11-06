<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم - المنار')</title>
    
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @stack('styles')
</head>
<body class="font-cairo antialiased bg-gradient-to-br from-[#0e1f27] to-[#173343] min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleMobileSidebar()"></div>
        
        <!-- Sidebar -->
        @include('dashboard.partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
            <!-- Header -->
            @include('dashboard.partials.header')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Custom Scripts -->
    <script src="{{ asset('js/custom.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
