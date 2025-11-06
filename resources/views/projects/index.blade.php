@extends('layouts.dashboard')

@section('title', 'المشاريع - المنار')
@section('page-title', 'المشاريع')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Toast Notifications -->
@if(session('success'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">المشاريع</h1>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <button onclick="openDisplaySettings()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-gear ml-2"></i>
            إعدادات العرض
        </button>
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-file-export ml-2"></i>
            تصدير
        </button>
        <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus ml-2"></i>
            مشروع جديد
        </a>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="filterState()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="بحث: اسم المشروع، رقم المشروع، المالك..." 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المدينة</label>
                <select x-model="city" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع المدن</option>
                    <option value="الرياض">الرياض</option>
                    <option value="جدة">جدة</option>
                    <option value="الدمام">الدمام</option>
                    <option value="مكة">مكة</option>
                    <option value="المدينة">المدينة</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحي</label>
                <select x-model="district" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأحياء</option>
                    <option value="العليا">العليا</option>
                    <option value="النخيل">النخيل</option>
                    <option value="الملك فهد">الملك فهد</option>
                    <option value="المرسلات">المرسلات</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المالك</label>
                <select x-model="owner" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الملاك</option>
                    <option value="أحمد محمد">أحمد محمد</option>
                    <option value="سارة علي">سارة علي</option>
                    <option value="خالد مطر">خالد مطر</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">نوع المشروع</label>
                <select x-model="type" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأنواع</option>
                    <option value="تصميم">تصميم</option>
                    <option value="تصميم وإشراف">تصميم وإشراف</option>
                    <option value="إشراف">إشراف</option>
                    <option value="تقرير فني">تقرير فني</option>
                    <option value="تقرير دفاع مدني">تقرير دفاع مدني</option>
                    <option value="تصميم دفاع مدني">تصميم دفاع مدني</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة/المرحلة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="قيد التنفيذ">قيد التنفيذ</option>
                    <option value="مكتمل">مكتمل</option>
                    <option value="متوقف">متوقف</option>
                    <option value="معماري">معماري</option>
                    <option value="إنشائي">إنشائي</option>
                    <option value="كهربائي">كهربائي</option>
                    <option value="ميكانيكي">ميكانيكي</option>
                    <option value="تقديم للبلدية">تقديم للبلدية</option>
                </select>
            </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">من تاريخ</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">إلى تاريخ</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex items-center gap-3">
            <button @click="applyFilters()" class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <button @click="clearFilters()" class="px-6 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times ml-2"></i>
                تفريغ
            </button>
        </div>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي المشاريع</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">45</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-diagram-project text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">قيد التنفيذ</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">28</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المكتملة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">15</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المتأخرة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">2</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg md:text-xl font-bold text-white">ملخص مالي</h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-gray-400 text-sm mb-2">إجمالي قيمة المشاريع</p>
            <p class="text-2xl md:text-3xl font-bold text-white">2,450,000 <span class="text-lg text-gray-400">ر.س</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المحصل هذا الشهر</p>
            <p class="text-2xl md:text-3xl font-bold text-primary-400">450,000 <span class="text-lg text-gray-400">ر.س</span></p>
            <div class="mt-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-gray-400 text-xs">المحصل</span>
                    <span class="text-white text-sm font-semibold">450,000 / 600,000 ر.س</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-2">
                    <div class="bg-primary-400 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects Grid -->
<div id="projectsGrid" x-data="projectsData()" x-show="projects.length > 0">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <template x-for="project in filteredProjects" :key="project.id">
            @include('components.cards.project-card')
        </template>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" x-data="projectsData()" x-show="projects.length === 0" class="text-center py-12 md:py-16">
    <div class="glass-card rounded-xl md:rounded-2xl p-8 md:p-12 max-w-md mx-auto">
        <i class="fas fa-folder-open text-6xl md:text-7xl text-gray-400 mb-6"></i>
        <h3 class="text-xl md:text-2xl font-bold text-white mb-3">لا توجد مشاريع</h3>
        <p class="text-gray-400 mb-6">ابدأ بإنشاء مشروع جديد لإدارة أعمالك</p>
        <a href="{{ route('projects.create') }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-plus ml-2"></i>
            مشروع جديد
        </a>
    </div>
</div>

@push('scripts')
<script>
    function filterState() {
        return {
            search: '',
            city: '',
            district: '',
            owner: '',
            type: '',
            status: '',
            dateFrom: '',
            dateTo: '',
            applyFilters() {
                // TODO: Apply filters logic
                console.log('Applying filters', this);
            },
            clearFilters() {
                this.search = '';
                this.city = '';
                this.district = '';
                this.owner = '';
                this.type = '';
                this.status = '';
                this.dateFrom = '';
                this.dateTo = '';
            }
        }
    }

    function projectsData() {
        return {
            projects: [
                {
                    id: 1,
                    name: 'فيلا سكنية - العليا',
                    projectNumber: 'PRJ-2025-001',
                    city: 'الرياض',
                    district: 'العليا',
                    owner: 'أحمد محمد',
                    type: 'تصميم وإشراف',
                    typeBadge: 'primary',
                    progress: 70,
                    currentStage: 'معماري',
                    stageBadge: 'blue',
                    value: 150000,
                    status: 'قيد التنفيذ',
                    statusBadge: 'green',
                    attachments: 5,
                    tasks: 3,
                    invoices: 2
                },
                {
                    id: 2,
                    name: 'مجمع تجاري - النخيل',
                    projectNumber: 'PRJ-2025-002',
                    city: 'جدة',
                    district: 'النخيل',
                    owner: 'سارة علي',
                    type: 'تصميم',
                    typeBadge: 'green',
                    progress: 30,
                    currentStage: 'إنشائي',
                    stageBadge: 'yellow',
                    value: 300000,
                    status: 'قيد التنفيذ',
                    statusBadge: 'green',
                    attachments: 2,
                    tasks: 1,
                    invoices: 0
                },
                {
                    id: 3,
                    name: 'عمارة سكنية - الملك فهد',
                    projectNumber: 'PRJ-2025-003',
                    city: 'الرياض',
                    district: 'الملك فهد',
                    owner: 'خالد مطر',
                    type: 'إشراف',
                    typeBadge: 'purple',
                    progress: 90,
                    currentStage: 'تقديم للبلدية',
                    stageBadge: 'green',
                    value: 200000,
                    status: 'قيد التنفيذ',
                    statusBadge: 'green',
                    attachments: 8,
                    tasks: 5,
                    invoices: 3
                }
            ],
            get filteredProjects() {
                // TODO: Apply filter logic
                return this.projects;
            }
        }
    }

    function openDisplaySettings() {
        // TODO: Open display settings modal
        alert('إعدادات العرض');
    }
</script>
@endpush

@endsection
