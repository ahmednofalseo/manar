@php
use Illuminate\Support\Facades\Auth;
@endphp
<header class="sticky top-0 z-[60] bg-primary-700/30 backdrop-blur-lg border-b border-white/10 px-4 md:px-6 py-4">
    <div class="flex items-center justify-between">
        <!-- Left Side: Mobile Menu + Logo -->
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="{{ __('Menu') }}">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <div class="flex items-center gap-2">
                @php
                    $systemName = \App\Helpers\SettingsHelper::systemName();
                    $systemLogo = \App\Helpers\SettingsHelper::systemLogo();
                @endphp
                @if($systemLogo)
                    <img src="{{ $systemLogo }}" alt="{{ $systemName }}" class="h-12 w-auto object-contain">
                @else
                    <i class="fas fa-tower-cell text-primary-400 text-xl"></i>
                    <span class="text-white font-bold text-lg">{{ $systemName }}</span>
                @endif
            </div>
            <span class="text-gray-400 text-sm mr-4 hidden md:inline">|</span>
            <h2 class="text-white font-semibold text-sm md:text-base">@yield('page-title', __('Dashboard'))</h2>
        </div>
        
        <!-- Right Side Actions -->
        <div class="flex items-center gap-2 md:gap-4">
            <!-- Language Toggle -->
            <a href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="px-2 md:px-3 py-1 bg-primary-600/50 hover:bg-primary-600 text-white text-xs rounded-full border border-white/10 transition-all duration-200">
                <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}</span>
                <span class="sm:hidden">{{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}</span>
            </a>
            
            <!-- Search -->
            <div class="relative hidden lg:block">
                <input type="text" placeholder="{{ __('Search') }}..." class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 {{ app()->getLocale() === 'ar' ? 'pr-10' : 'pl-10' }} text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 w-64">
                <i class="fas fa-search absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            
            <!-- Mobile Search Toggle -->
            <button onclick="toggleMobileSearch()" class="lg:hidden p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="{{ __('Search') }}">
                <i class="fas fa-search text-xl"></i>
            </button>
            
            <!-- Notifications -->
            <div class="relative" x-data="notificationsDropdown()" @click.away="close()">
                <button @click="toggle()" class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="{{ __('Notifications') }}">
                    <i class="fas fa-bell text-lg md:text-xl"></i>
                    <span x-show="unreadCount > 0" class="absolute {{ app()->getLocale() === 'ar' ? 'top-0 left-0' : 'top-0 right-0' }} bg-red-500 text-white text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center text-[10px] md:text-xs" x-text="unreadCount"></span>
                </button>
                
                <!-- Notifications Dropdown -->
                <div x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-80 md:w-96 rounded-lg shadow-xl border border-white/10 py-2 z-50 max-h-[500px] overflow-y-auto"
                     style="display: none; background-color: #09141c;"
                     >
                    <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                        <h3 class="text-white font-semibold text-sm">{{ __('Notifications') }}</h3>
                        <div class="flex items-center gap-2">
                            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-primary-400 hover:text-primary-300">
                                {{ __('Mark all as read') }}
                            </button>
                            <button @click="deleteAll()" class="text-xs text-red-400 hover:text-red-300">
                                {{ __('Clear all') }}
                            </button>
                        </div>
                    </div>
                    
                    <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-400 text-sm">
                        {{ __('No notifications') }}
                    </div>
                    
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer"
                             :class="{ 'bg-primary-400/10': !notification.read }"
                             @click="openNotification(notification)">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i :class="notification.type === 'task_assigned' ? 'fas fa-tasks text-primary-400' : 'fas fa-comment text-blue-400'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white text-sm font-semibold" x-text="notification.title"></p>
                                    <p class="text-gray-400 text-xs mt-1" x-text="notification.message"></p>
                                    <p class="text-gray-500 text-xs mt-1" x-text="formatDate(notification.created_at)"></p>
                                </div>
                                <div class="flex-shrink-0 flex items-center gap-2">
                                    <span x-show="!notification.read" class="w-2 h-2 bg-primary-400 rounded-full"></span>
                                    <button @click.stop="deleteNotification(notification.id)" class="text-gray-500 hover:text-red-400 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Messages -->
            <div class="relative" x-data="projectMessagesDropdown()" @click.away="close()">
                <button @click="toggle()" class="relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 hidden sm:block" aria-label="{{ __('Messages') }}">
                    <i class="fas fa-comments text-lg md:text-xl"></i>
                    <span x-show="unreadCount > 0" class="absolute {{ app()->getLocale() === 'ar' ? 'top-0 left-0' : 'top-0 right-0' }} bg-blue-500 text-white text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center text-[10px] md:text-xs font-semibold" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                </button>
                
                <!-- Messages Dropdown -->
                <div x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-80 md:w-96 rounded-lg shadow-xl border border-white/10 py-2 z-50 max-h-[500px] overflow-y-auto"
                     style="display: none; background-color: #09141c;"
                     >
                    <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                        <h3 class="text-white font-semibold text-sm">{{ __('Project Messages') }}</h3>
                        <span x-show="unreadCount > 0" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full" x-text="unreadCount + ' {{ __('unread') }}'"></span>
                    </div>
                    
                    <div x-show="loading" class="px-4 py-8 text-center">
                        <i class="fas fa-spinner fa-spin text-primary-400 text-xl"></i>
                    </div>
                    
                    <div x-show="!loading && projectsWithMessages.length === 0" class="px-4 py-8 text-center text-gray-400 text-sm">
                        {{ __('No new messages') }}
                    </div>
                    
                    <template x-for="project in projectsWithMessages" :key="project.project_id">
                        <a :href="'/projects/' + project.project_id + '#chat'" 
                           class="block px-4 py-3 border-b border-white/5 hover:bg-white/5 transition-colors cursor-pointer"
                           :class="{ 'bg-blue-500/10': project.unread_count > 0 }">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <i class="fas fa-comments text-blue-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white text-sm font-semibold" x-text="project.project_name"></p>
                                    <template x-if="project.last_message">
                                        <div>
                                            <p class="text-gray-400 text-xs mt-1 truncate" x-text="project.last_message.user.name + ': ' + (project.last_message.message || attachmentText)"></p>
                                            <p class="text-gray-500 text-xs mt-1" x-text="formatDate(project.last_message.created_at)"></p>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-shrink-0">
                                    <span x-show="project.unread_count > 0" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-semibold" x-text="project.unread_count > 99 ? '99+' : project.unread_count"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            
            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" class="p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 hidden sm:block" aria-label="{{ __('Settings') }}">
                <i class="fas fa-cog text-lg md:text-xl"></i>
            </a>
            
            <!-- User Avatar Dropdown -->
            @php
                $currentUser = Auth::user();
                $userAvatar = $currentUser->avatar 
                    ? asset('storage/' . $currentUser->avatar) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=1db8f8&color=fff&size=128';
            @endphp
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 p-1 md:p-2 hover:bg-white/10 rounded-lg transition-all duration-200">
                    <img src="{{ $userAvatar }}" alt="{{ $currentUser->name }}" class="w-7 h-7 md:w-8 md:h-8 rounded-full border-2 border-primary-400 object-cover">
                    <span class="text-white text-xs md:text-sm hidden lg:block truncate max-w-[120px]">{{ $currentUser->name }}</span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden sm:block transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-48 rounded-lg shadow-xl border border-white/10 py-2 z-50"
                     style="display: none; background-color: #09141c;"
                     >
                    <div class="px-4 py-3 border-b border-white/10 mb-2">
                        <p class="text-white text-sm font-semibold truncate">{{ $currentUser->name }}</p>
                        <p class="text-gray-400 text-xs truncate">{{ $currentUser->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-white hover:bg-white/10 text-sm transition-colors">
                        <i class="fas fa-user {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Profile') }}
                    </a>
                    @can('viewAny', \App\Models\Setting::class)
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 text-sm transition-colors">
                        <i class="fas fa-cog {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Settings') }}
                    </a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} px-4 py-2 text-white hover:bg-white/10 text-sm transition-colors">
                            <i class="fas fa-sign-out-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
