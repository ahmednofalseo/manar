<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Helpers\SettingsHelper::systemName() . ' - ' . __('System Management'))</title>
    
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
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    @stack('styles')
</head>
<body class="font-cairo antialiased">
    @yield('content')
    
    <!-- Custom Scripts -->
    <script src="{{ asset('js/custom.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
