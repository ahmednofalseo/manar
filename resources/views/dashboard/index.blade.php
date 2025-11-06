@extends('layouts.dashboard')

@section('title', 'لوحة التحكم - المنار')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .kpi-card {
        transition: all 0.3s ease;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(29, 184, 248, 0.2);
    }
</style>
@endpush

@section('content')
<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-3 md:p-4 mb-4 md:mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">المدينة</label>
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option>جميع المدن</option>
                <option>الرياض</option>
                <option>جدة</option>
                <option>الدمام</option>
            </select>
        </div>
        
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">المالك</label>
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option>جميع الملاك</option>
                <option>أحمد محمد</option>
                <option>سارة علي</option>
            </select>
        </div>
        
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">الحالة</label>
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option>جميع الحالات</option>
                <option>قيد التنفيذ</option>
                <option>مكتمل</option>
                <option>متوقف</option>
            </select>
        </div>
        
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">المهندس</label>
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                <option>جميع المهندسين</option>
                <option>محمد أحمد</option>
                <option>فاطمة سالم</option>
            </select>
        </div>
        
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">من تاريخ</label>
            <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
        </div>
        
        <div class="sm:col-span-1">
            <label class="block text-xs md:text-sm text-gray-300 mb-1 md:mb-2">إلى تاريخ</label>
            <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
        </div>
        
        <div class="sm:col-span-1 sm:col-start-1 lg:col-start-auto flex items-end">
            <button class="w-full sm:w-auto px-4 md:px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-search ml-2"></i>
                بحث
            </button>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- KPI 1: Total Projects -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي المشاريع</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">45</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex-1 bg-white/5 rounded-full h-2">
                <div class="bg-primary-400 h-2 rounded-full" style="width: 68%"></div>
            </div>
            <span class="text-primary-400 text-sm font-semibold">68%</span>
        </div>
        <p class="text-gray-400 text-xs mt-2">تقدّم متوسط المراحل</p>
    </div>
    
    <!-- KPI 2: Tasks -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المهام</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">128</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-list-check text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">قيد التنفيذ</span>
                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">42</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">منجزة</span>
                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">76</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">متأخرة</span>
                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs">10</span>
            </div>
        </div>
    </div>
    
    <!-- KPI 3: Collections -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">التحصيلات هذا الشهر</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">450,000</h3>
                <p class="text-gray-400 text-xs mt-1">ر.س</p>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">المستحقة</span>
                <span class="text-white font-semibold">600,000 ر.س</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">المتبقي</span>
                <span class="text-red-400 font-semibold">150,000 ر.س</span>
            </div>
        </div>
    </div>
    
    <!-- KPI 4: Clients -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 kpi-card">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">العملاء النشطين</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">38</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-tie text-purple-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">جدد هذا الشهر</span>
                <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs">+5</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300 text-sm">موافقات قيد الانتظار</span>
                <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">12</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
    <!-- Left Column: Projects Grid -->
    <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <!-- Projects Grid -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <h2 class="text-lg md:text-xl font-bold text-white">المشاريع</h2>
                <a href="{{ route('projects.index') }}" class="text-primary-400 hover:text-primary-300 text-xs md:text-sm">
                    <span class="hidden sm:inline">عرض الكل</span>
                    <i class="fas fa-arrow-left mr-1"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <!-- Project Card 1 -->
                <div class="bg-white/5 rounded-lg md:rounded-xl p-3 md:p-4 border border-white/10 hover:border-primary-400/40 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-white font-semibold">فيلا سكنية - العليا</h3>
                            <p class="text-gray-400 text-sm mt-1">الرياض - العليا | أحمد محمد</p>
                        </div>
                        <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs">سكني</span>
                    </div>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="relative w-12 h-12">
                                <svg class="transform -rotate-90" width="48" height="48">
                                    <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="none"/>
                                    <circle cx="24" cy="24" r="20" stroke="#1db8f8" stroke-width="4" fill="none" 
                                            stroke-dasharray="125.6" stroke-dashoffset="37.68" stroke-linecap="round"/>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold">70%</span>
                            </div>
                            <span class="text-gray-400 text-xs">معماري</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button class="flex-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-eye ml-1"></i>
                            عرض
                        </button>
                        <button class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-tasks ml-1"></i>
                            مهمة
                        </button>
                        <button class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-file-invoice ml-1"></i>
                            فاتورة
                        </button>
                        <button class="bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-white/10">
                        <span class="text-gray-400 text-xs">
                            <i class="fas fa-file ml-1"></i>
                            5 مرفقات
                        </span>
                        <span class="text-gray-400 text-xs">
                            <i class="fas fa-comment ml-1"></i>
                            3 ملاحظات
                        </span>
                        <span class="text-yellow-400 text-xs">
                            <i class="fas fa-star"></i>
                        </span>
                    </div>
                </div>
                
                <!-- Project Card 2 -->
                <div class="bg-white/5 rounded-xl p-4 border border-white/10 hover:border-primary-400/40 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-white font-semibold">مجمع تجاري - النخيل</h3>
                            <p class="text-gray-400 text-sm mt-1">جدة - النخيل | سارة علي</p>
                        </div>
                        <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">تجاري</span>
                    </div>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="relative w-12 h-12">
                                <svg class="transform -rotate-90" width="48" height="48">
                                    <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="none"/>
                                    <circle cx="24" cy="24" r="20" stroke="#1db8f8" stroke-width="4" fill="none" 
                                            stroke-dasharray="125.6" stroke-dashoffset="87.92" stroke-linecap="round"/>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold">30%</span>
                            </div>
                            <span class="text-gray-400 text-xs">إنشائي</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <button class="flex-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-eye ml-1"></i>
                            عرض
                        </button>
                        <button class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-tasks ml-1"></i>
                            مهمة
                        </button>
                        <button class="flex-1 bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-file-invoice ml-1"></i>
                            فاتورة
                        </button>
                        <button class="bg-white/5 hover:bg-white/10 text-white px-3 py-1.5 rounded text-xs transition-all duration-200">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-white/10">
                        <span class="text-gray-400 text-xs">
                            <i class="fas fa-file ml-1"></i>
                            2 مرفقات
                        </span>
                        <span class="text-gray-400 text-xs">
                            <i class="fas fa-comment ml-1"></i>
                            1 ملاحظة
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tasks Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-3">
                <h2 class="text-lg md:text-xl font-bold text-white">المهام القادمة</h2>
                <button onclick="openTaskModal()" class="w-full sm:w-auto bg-primary-500 hover:bg-primary-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm transition-all duration-200">
                    <i class="fas fa-plus ml-1"></i>
                    إنشاء مهمة
                </button>
            </div>
            
            <div class="overflow-x-auto -mx-4 md:mx-0">
                <div class="min-w-full inline-block">
                    <table class="w-full">
                        <thead>
                            <tr class="text-right border-b border-white/10">
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-0">المهمة</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-0 hidden md:table-cell">المكلّف</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-0 hidden lg:table-cell">تاريخ الاستحقاق</th>
                                <th class="text-gray-400 text-xs md:text-sm font-normal pb-2 md:pb-3 px-2 md:px-0">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="space-y-2">
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-2 md:py-3 text-white text-xs md:text-sm px-2 md:px-0">
                                    <div class="flex flex-col">
                                        <span>مراجعة المخططات المعمارية</span>
                                        <span class="text-gray-400 text-xs md:hidden">محمد أحمد • 2025-11-10</span>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden md:table-cell">محمد أحمد</td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden lg:table-cell">2025-11-10</td>
                                <td class="py-2 md:py-3 px-2 md:px-0"><span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">قيد التنفيذ</span></td>
                            </tr>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-2 md:py-3 text-white text-xs md:text-sm px-2 md:px-0">
                                    <div class="flex flex-col">
                                        <span>إعداد تقرير التقدم</span>
                                        <span class="text-gray-400 text-xs md:hidden">فاطمة سالم • 2025-11-08</span>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden md:table-cell">فاطمة سالم</td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden lg:table-cell">2025-11-08</td>
                                <td class="py-2 md:py-3 px-2 md:px-0"><span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">جديد</span></td>
                            </tr>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                <td class="py-2 md:py-3 text-white text-xs md:text-sm px-2 md:px-0">
                                    <div class="flex flex-col">
                                        <span>فحص الموقع</span>
                                        <span class="text-gray-400 text-xs md:hidden">خالد مطر • 2025-11-12</span>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden md:table-cell">خالد مطر</td>
                                <td class="py-2 md:py-3 text-gray-300 text-xs md:text-sm px-2 md:px-0 hidden lg:table-cell">2025-11-12</td>
                                <td class="py-2 md:py-3 px-2 md:px-0"><span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">منجز</span></td>
                            </tr>
                                                 </tbody>
                     </table>
                 </div>
             </div>
         </div>
         
                   <!-- Invoices Widget -->
          <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
             <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-3">
                 <h2 class="text-lg md:text-xl font-bold text-white">الفواتير والدفعات</h2>
                 <button onclick="openInvoiceModal()" class="w-full sm:w-auto bg-primary-500 hover:bg-primary-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm transition-all duration-200">
                    <i class="fas fa-plus ml-1"></i>
                    إنشاء فاتورة
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300 text-sm">تم التحصيل</span>
                    <span class="text-white font-semibold">450,000 / 600,000 ر.س</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-3">
                    <div class="bg-primary-400 h-3 rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-gray-400 text-xs mt-2">المتبقي: 150,000 ر.س</p>
            </div>
            
            <!-- Invoices Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-right border-b border-white/10">
                            <th class="text-gray-400 text-sm font-normal pb-3">رقم الفاتورة</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">العميل</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">المشروع</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">المبلغ</th>
                            <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                            <th class="text-gray-400 text-sm font-normal pb-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 text-white text-sm">#INV-2025-001</td>
                            <td class="py-3 text-gray-300 text-sm">أحمد محمد</td>
                            <td class="py-3 text-gray-300 text-sm">فيلا سكنية</td>
                            <td class="py-3 text-white font-semibold">150,000 ر.س</td>
                            <td class="py-3"><span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">مدفوع</span></td>
                            <td class="py-3"><button class="text-primary-400 hover:text-primary-300"><i class="fas fa-file-pdf"></i></button></td>
                        </tr>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 text-white text-sm">#INV-2025-002</td>
                            <td class="py-3 text-gray-300 text-sm">سارة علي</td>
                            <td class="py-3 text-gray-300 text-sm">مجمع تجاري</td>
                            <td class="py-3 text-white font-semibold">200,000 ر.س</td>
                            <td class="py-3"><span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs">جزئي</span></td>
                            <td class="py-3"><button class="text-primary-400 hover:text-primary-300"><i class="fas fa-file-pdf"></i></button></td>
                        </tr>
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="py-3 text-white text-sm">#INV-2025-003</td>
                            <td class="py-3 text-gray-300 text-sm">خالد مطر</td>
                            <td class="py-3 text-gray-300 text-sm">عمارة سكنية</td>
                            <td class="py-3 text-white font-semibold">100,000 ر.س</td>
                            <td class="py-3"><span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs">غير مدفوع</span></td>
                            <td class="py-3"><button class="text-primary-400 hover:text-primary-300"><i class="fas fa-file-pdf"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Widgets -->
    <div class="space-y-4 md:space-y-6">
        <!-- Completion Rate Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h2 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">معدل الإتمام</h2>
            <div class="flex justify-center">
                <canvas id="completionChart" width="200" height="200" class="max-w-full h-auto"></canvas>
            </div>
            <p class="text-center text-gray-400 text-xs md:text-sm mt-3 md:mt-4">هذا الشهر</p>
        </div>
        
        <!-- Top Performers Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h2 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">أفضل الأداء</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-full flex items-center justify-center">
                            <span class="text-primary-400 font-bold">1</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">محمد أحمد</p>
                            <p class="text-gray-400 text-xs">مهندس معماري</p>
                        </div>
                    </div>
                    <span class="text-green-400 font-bold">94%</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-full flex items-center justify-center">
                            <span class="text-primary-400 font-bold">2</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">فاطمة سالم</p>
                            <p class="text-gray-400 text-xs">مهندسة إنشائية</p>
                        </div>
                    </div>
                    <span class="text-green-400 font-bold">89%</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-full flex items-center justify-center">
                            <span class="text-primary-400 font-bold">3</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">خالد مطر</p>
                            <p class="text-gray-400 text-xs">مهندس كهرباء</p>
                        </div>
                    </div>
                    <span class="text-green-400 font-bold">85%</span>
                </div>
            </div>
        </div>
        
        <!-- Clients Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <h2 class="text-base md:text-lg font-bold text-white">العملاء</h2>
                <button onclick="openClientModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-2 md:px-3 py-1 md:py-1.5 rounded-lg text-xs transition-all duration-200">
                    <i class="fas fa-plus ml-1"></i>
                    <span class="hidden sm:inline">إضافة</span>
                </button>
            </div>
            <div class="mb-3 md:mb-4">
                <p class="text-2xl md:text-3xl font-bold text-white">38</p>
                <p class="text-gray-400 text-xs md:text-sm">عملاء نشطين</p>
                <p class="text-primary-400 text-xs mt-1">+5 جدد هذا الشهر</p>
            </div>
            <div class="space-y-2">
                <div class="p-3 bg-white/5 rounded-lg">
                    <p class="text-white text-sm">موافقة جديدة من أحمد محمد</p>
                    <p class="text-gray-400 text-xs mt-1">منذ ساعتين</p>
                </div>
                <div class="p-3 bg-white/5 rounded-lg">
                    <p class="text-white text-sm">ملاحظة من سارة علي</p>
                    <p class="text-gray-400 text-xs mt-1">منذ 5 ساعات</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Settings Widget -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h2 class="text-base md:text-lg font-bold text-white mb-3 md:mb-4">إعدادات سريعة</h2>
            <div class="space-y-2">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-200 text-white text-sm">
                    <i class="fas fa-image"></i>
                    <span>الشعار</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-200 text-white text-sm">
                    <i class="fas fa-language"></i>
                    <span>اللغة</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-200 text-white text-sm">
                    <i class="fas fa-envelope"></i>
                    <span>البريد</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-200 text-white text-sm">
                    <i class="fas fa-credit-card"></i>
                    <span>الدفع</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-lg transition-all duration-200 text-white text-sm">
                    <i class="fas fa-shield-alt"></i>
                    <span>الأدوار والصلاحيات</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="closeModalOnBackdrop(event, 'taskModal')">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-lg md:text-xl font-bold text-white">إنشاء مهمة جديدة</h3>
            <button onclick="closeModal('taskModal')" class="text-gray-400 hover:text-white p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">اسم المهمة</label>
                <input type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">الوصف</label>
                <textarea class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">المشروع</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>اختر المشروع</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">المكلّف</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>اختر المكلّف</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">تاريخ الاستحقاق</label>
                    <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الأولوية</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                        <option>عادية</option>
                        <option>عالية</option>
                        <option>منخفضة</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('taskModal')" class="flex-1 bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4" onclick="closeModalOnBackdrop(event, 'invoiceModal')">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-md w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-lg md:text-xl font-bold text-white">إنشاء فاتورة جديدة</h3>
            <button onclick="closeModal('invoiceModal')" class="text-gray-400 hover:text-white p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">العميل</label>
                <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option>اختر العميل</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">المشروع</label>
                <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option>اختر المشروع</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">المبلغ</label>
                <input type="number" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40" placeholder="0.00">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الاستحقاق</label>
                <input type="date" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('invoiceModal')" class="flex-1 bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition-all duration-200">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Completion Chart
    const ctx = document.getElementById('completionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'قيد التنفيذ', 'متوقف'],
                datasets: [{
                    data: [68, 25, 7],
                    backgroundColor: [
                        '#1db8f8',
                        '#4787a7',
                        '#6b7280'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff',
                            padding: 15
                        }
                    }
                }
            }
        });
    }
    
    // Modal Functions
    function openTaskModal() {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('taskModal').classList.add('flex');
    }
    
    function openInvoiceModal() {
        document.getElementById('invoiceModal').classList.remove('hidden');
        document.getElementById('invoiceModal').classList.add('flex');
    }
    
    function openClientModal() {
        // Implement later
        showToast('سيتم تنفيذ هذه الوظيفة قريباً', 'info');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(modalId).classList.remove('flex');
    }
    
    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            closeModal(modalId);
        }
    }
    
    // Toast Function
    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${colors[type]} text-white`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
</script>
@endpush

@endsection
