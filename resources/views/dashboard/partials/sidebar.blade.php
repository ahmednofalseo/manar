<aside id="sidebar" class="fixed lg:static inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 border-l' : 'left-0 border-r' }} w-64 bg-primary-700/50 backdrop-blur-lg border-white/10 flex flex-col z-50 transform {{ app()->getLocale() === 'ar' ? 'translate-x-full' : '-translate-x-full' }} lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Logo -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @php
                    $systemName = \App\Helpers\SettingsHelper::systemName();
                    $systemLogo = \App\Helpers\SettingsHelper::systemLogo();
                @endphp
                @if($systemLogo)
                    <img src="{{ $systemLogo }}" alt="{{ $systemName }}" class="h-12 w-auto object-contain flex-shrink-0">
                @else
                    <i class="fas fa-tower-cell text-2xl text-primary-400 flex-shrink-0"></i>
                    <h1 class="text-xl font-bold text-white truncate">{{ $systemName }}</h1>
                @endif
            </div>
            <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="{{ __('Close') }}">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <a href="{{ route('dashboard.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('dashboard.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-gauge w-5"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
        
        @if(\App\Helpers\PermissionHelper::hasPermission('projects.view') || \App\Helpers\PermissionHelper::hasPermission('projects.manage'))
        <a href="{{ route('projects.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('projects.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-diagram-project w-5"></i>
            <span>{{ __('Projects') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('tasks.view') || \App\Helpers\PermissionHelper::hasPermission('tasks.manage'))
        <a href="{{ route('tasks.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-list-check w-5"></i>
            <span>{{ __('Tasks') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('approvals.view') || \App\Helpers\PermissionHelper::hasPermission('approvals.manage'))
        <a href="{{ route('approvals.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('approvals.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-clipboard-check w-5"></i>
            <span>{{ __('Approvals') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('clients.view') || \App\Helpers\PermissionHelper::hasPermission('clients.manage'))
        <a href="{{ route('clients.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('clients.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-user-tie w-5"></i>
            <span>{{ __('Clients') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('financials.view') || \App\Helpers\PermissionHelper::hasPermission('financials.manage'))
        <a href="{{ route('financials.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('financials.*') || request()->routeIs('invoices.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5"></i>
            <span>{{ __('Financials') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('expenses.view') || \App\Helpers\PermissionHelper::hasPermission('expenses.manage'))
        <a href="{{ route('expenses.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-money-bill-trend-up w-5"></i>
            <span>{{ __('Expenses') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('users.view') || \App\Helpers\PermissionHelper::hasPermission('users.manage'))
        <a href="{{ route('admin.users.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.users.*') || request()->routeIs('users.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-users-gear w-5"></i>
            <span>{{ __('Users') }}</span>
        </a>
        @endif
        
        @if(\App\Helpers\PermissionHelper::hasPermission('settings.view') || \App\Helpers\PermissionHelper::hasPermission('settings.manage'))
        <a href="{{ route('admin.settings.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-cog w-5"></i>
            <span>{{ __('Settings') }}</span>
        </a>
        @endif
    </nav>
    
    <!-- Footer -->
    <div class="p-4 border-t border-white/10">
        <p class="text-xs text-gray-400 text-center">© {{ date('Y') }} نظام {{ \App\Helpers\SettingsHelper::systemName() }}</p>
    </div>
</aside>
