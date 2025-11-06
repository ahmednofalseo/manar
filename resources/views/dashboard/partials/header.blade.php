<header class="sticky top-0 z-[60] bg-primary-700/30 backdrop-blur-lg border-b border-white/10 px-4 md:px-6 py-4">
    <div class="flex items-center justify-between">
        <!-- Left Side: Mobile Menu + Logo -->
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="القائمة">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <div class="flex items-center gap-2">
                <i class="fas fa-tower-cell text-primary-400 text-xl"></i>
                <span class="text-white font-bold text-lg">المنار</span>
            </div>
            <span class="text-gray-400 text-sm mr-4 hidden md:inline">|</span>
            <h2 class="text-white font-semibold text-sm md:text-base">@yield('page-title', 'لوحة التحكم')</h2>
        </div>
        
        <!-- Right Side Actions -->
        <div class="flex items-center gap-2 md:gap-4">
            <!-- Language Toggle -->
            <button class="px-2 md:px-3 py-1 bg-primary-600/50 hover:bg-primary-600 text-white text-xs rounded-full border border-white/10 transition-all duration-200" onclick="toggleLanguage()">
                <span class="hidden sm:inline">EN</span>
                <span class="sm:hidden">EN</span>
            </button>
            
            <!-- Search -->
            <div class="relative hidden lg:block">
                <input type="text" placeholder="بحث..." class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 w-64">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Mobile Search Toggle -->
            <button onclick="toggleMobileSearch()" class="lg:hidden p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="بحث">
                <i class="fas fa-search text-xl"></i>
            </button>
            
            <!-- Notifications -->
            <button class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="الإشعارات">
                <i class="fas fa-bell text-lg md:text-xl"></i>
                <span class="absolute top-0 left-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center text-[10px] md:text-xs">3</span>
            </button>
            
            <!-- Messages -->
            <button class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 hidden sm:block" aria-label="الرسائل">
                <i class="fas fa-envelope text-lg md:text-xl"></i>
                <span class="absolute top-0 left-0 bg-blue-500 text-white text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center text-[10px] md:text-xs">2</span>
            </button>
            
            <!-- Settings -->
            <a href="{{ route('settings.index') }}" class="p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 hidden sm:block" aria-label="الإعدادات">
                <i class="fas fa-cog text-lg md:text-xl"></i>
            </a>
            
            <!-- User Avatar Dropdown -->
            <div class="relative" x-data="{ open: false }" onclick="toggleUserDropdown(event)">
                <button onclick="event.stopPropagation(); toggleUserDropdown(event)" class="flex items-center gap-2 p-1 md:p-2 hover:bg-white/10 rounded-lg transition-all duration-200">
                    <img src="https://ui-avatars.com/api/?name=مستخدم&background=1db8f8&color=fff&size=128" alt="المستخدم" class="w-7 h-7 md:w-8 md:h-8 rounded-full border-2 border-primary-400">
                    <span class="text-white text-xs md:text-sm hidden lg:block">مستخدم</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden sm:block"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="userDropdown" x-show="open" @click.away="open = false" x-transition class="hidden absolute left-0 mt-2 w-48 bg-primary-800 rounded-lg shadow-xl border border-white/10 py-2 z-50">
                    <a href="#" class="block px-4 py-2 text-white hover:bg-white/10 text-sm">
                        <i class="fas fa-user ml-2"></i>
                        الملف الشخصي
                    </a>
                    <a href="#" class="block px-4 py-2 text-white hover:bg-white/10 text-sm">
                        <i class="fas fa-cog ml-2"></i>
                        الإعدادات
                    </a>
                    <a href="#" class="block px-4 py-2 text-white hover:bg-white/10 text-sm">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل الخروج
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
