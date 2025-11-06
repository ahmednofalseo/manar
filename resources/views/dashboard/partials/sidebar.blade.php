<aside id="sidebar" class="fixed lg:static inset-y-0 right-0 w-64 bg-primary-700/50 backdrop-blur-lg border-l border-white/10 flex flex-col z-50 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <!-- Logo -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <i class="fas fa-tower-cell text-2xl text-primary-400"></i>
                <h1 class="text-xl font-bold text-white">المنار</h1>
            </div>
            <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-all duration-200" aria-label="إغلاق القائمة">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <a href="{{ route('dashboard.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('dashboard.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-gauge w-5"></i>
            <span>لوحة التحكم</span>
        </a>
        
        <a href="{{ route('projects.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('projects.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-diagram-project w-5"></i>
            <span>المشاريع</span>
        </a>
        
        <a href="{{ route('tasks.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-list-check w-5"></i>
            <span>المهام</span>
        </a>
        
        <a href="{{ route('clients.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('clients.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-user-tie w-5"></i>
            <span>العملاء</span>
        </a>
        
        <a href="{{ route('invoices.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('financials.*') || request()->routeIs('invoices.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-file-invoice-dollar w-5"></i>
            <span>الفواتير والدفعات</span>
        </a>
        
        <a href="{{ route('expenses.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-money-bill-trend-up w-5"></i>
            <span>المصروفات</span>
        </a>
        
        <a href="{{ route('admin.users.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.users.*') || request()->routeIs('users.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-users-gear w-5"></i>
            <span>المستخدمون والأدوار</span>
        </a>
        
        <a href="{{ route('settings.index') }}" onclick="closeMobileSidebarOnClick()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-white/10 ring-2 ring-primary-400/40' : '' }}">
            <i class="fas fa-sliders w-5"></i>
            <span>الإعدادات العامة</span>
        </a>
    </nav>
    
    <!-- Footer -->
    <div class="p-4 border-t border-white/10">
        <p class="text-xs text-gray-400 text-center">© 2025 نظام المنار</p>
    </div>
</aside>
