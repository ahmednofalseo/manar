@extends('layouts.dashboard')

@section('title', 'إدارة المهام - المنار')
@section('page-title', 'إدارة المهام')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .kanban-column {
        min-height: 500px;
    }
    .task-card {
        transition: all 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 184, 248, 0.2);
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
    <h1 class="text-2xl md:text-3xl font-bold text-white">إدارة المهام</h1>
    <div class="flex items-center gap-3">
        <button class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-chart-line ml-2"></i>
            تقرير الأداء
        </button>
        <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-plus-circle ml-2"></i>
            مهمة جديدة
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">عدد المهام الإجمالي</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">156</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tasks text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">قيد التنفيذ</p>
                <h3 class="text-2xl md:text-3xl font-bold text-primary-400 mt-1 md:mt-2">48</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-spinner text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المهام المنجزة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">98</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المهام المتأخرة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-red-400 mt-1 md:mt-2">10</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">نسبة الإنجاز</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">63%</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-percentage text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-white/5 rounded-full h-2">
                <div class="bg-primary-400 h-2 rounded-full" style="width: 63%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="taskFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="بحث: اسم المهمة، الوصف..." 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المشروع</label>
                <select x-model="project" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع المشاريع</option>
                    <option value="1">مشروع فيلا رقم 1</option>
                    <option value="2">مشروع مجمع سكني</option>
                    <option value="3">مشروع مبنى تجاري</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المهندس</label>
                <select x-model="engineer" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع المهندسين</option>
                    <option value="1">محمد أحمد</option>
                    <option value="2">فاطمة سالم</option>
                    <option value="3">خالد مطر</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">المرحلة</label>
                <select x-model="stage" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع المراحل</option>
                    <option value="معماري">معماري</option>
                    <option value="إنشائي">إنشائي</option>
                    <option value="كهربائي">كهربائي</option>
                    <option value="ميكانيكي">ميكانيكي</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="new">جديد</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="completed">منجز</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الفترة</label>
                <select x-model="period" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">كل الفترات</option>
                    <option value="today">اليوم</option>
                    <option value="week">هذا الأسبوع</option>
                    <option value="month">هذا الشهر</option>
                    <option value="custom">مخصص</option>
                </select>
            </div>
        </div>

        <!-- Date Range (when custom selected) -->
        <div x-show="period === 'custom'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
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

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button @click="applyFilters()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
            <button @click="clearFilters()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times ml-2"></i>
                تفريغ
            </button>
        </div>
    </div>
</div>

<!-- Kanban Board / Table View -->
<div class="mb-4 md:mb-6" x-data="tasksData()">
    <!-- View Toggle -->
    <div class="flex items-center justify-end gap-3 mb-4">
        <button @click="viewMode = 'kanban'" :class="viewMode === 'kanban' ? 'bg-primary-500 text-white' : 'bg-white/5 text-gray-300'" class="px-4 py-2 rounded-lg transition-all duration-200 text-sm">
            <i class="fas fa-columns ml-2"></i>
            Kanban
        </button>
        <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-primary-500 text-white' : 'bg-white/5 text-gray-300'" class="px-4 py-2 rounded-lg transition-all duration-200 text-sm">
            <i class="fas fa-table ml-2"></i>
            جدول
        </button>
    </div>

    <!-- Kanban Board -->
    <div x-show="viewMode === 'kanban'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- New Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">جديد</h3>
                <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('new').length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in getTasksByStatus('new')" :key="task.id">
                    <div class="task-card glass-card rounded-lg p-4 border border-white/10 cursor-move">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-white font-semibold text-sm" x-text="task.title"></h4>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-primary-400 h-1.5 rounded-full" :style="'width: ' + task.progress + '%'"></div>
                            </div>
                            <span class="text-gray-400 text-xs" x-text="task.progress + '%'"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openAttachmentModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" x-show="task.hasAttachment">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-primary-400">قيد التنفيذ</h3>
                <span class="bg-primary-500/20 text-primary-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('in_progress').length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in getTasksByStatus('in_progress')" :key="task.id">
                    <div class="task-card glass-card rounded-lg p-4 border border-primary-400/30 cursor-move">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-white font-semibold text-sm" x-text="task.title"></h4>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-primary-400 h-1.5 rounded-full" :style="'width: ' + task.progress + '%'"></div>
                            </div>
                            <span class="text-gray-400 text-xs" x-text="task.progress + '%'"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openAttachmentModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" x-show="task.hasAttachment">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-green-400">منجز</h3>
                <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('completed').length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in getTasksByStatus('completed')" :key="task.id">
                    <div class="task-card glass-card rounded-lg p-4 border border-green-400/30 cursor-move">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-white font-semibold text-sm" x-text="task.title"></h4>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 bg-white/5 rounded-full h-1.5 mr-2">
                                <div class="bg-green-400 h-1.5 rounded-full" style="width: 100%"></div>
                            </div>
                            <span class="text-green-400 text-xs">100%</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openAttachmentModal(task.id)" class="text-purple-400 hover:text-purple-300 text-xs" x-show="task.hasAttachment">
                                <i class="fas fa-paperclip"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Rejected Column -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 kanban-column">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-red-400">مرفوض</h3>
                <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs font-semibold" x-text="getTasksByStatus('rejected').length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in getTasksByStatus('rejected')" :key="task.id">
                    <div class="task-card glass-card rounded-lg p-4 border border-red-400/30 cursor-move">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="text-white font-semibold text-sm" x-text="task.title"></h4>
                            <div class="flex items-center gap-1">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300 text-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300 text-xs">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-xs mb-2" x-text="task.project"></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs" x-text="task.engineer"></span>
                            <span class="text-gray-400 text-xs" x-text="task.stage"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 pt-2 border-t border-white/10">
                            <button @click="openCommentModal(task.id)" class="text-yellow-400 hover:text-yellow-300 text-xs">
                                <i class="fas fa-comment-dots"></i>
                            </button>
                            <button @click="openStatusModal(task.id)" class="text-primary-400 hover:text-primary-300 text-xs">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">المهمة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المشروع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المهندس</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المرحلة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">التقدم</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="task in tasks" :key="task.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold" x-text="task.title"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="task.project"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="task.engineer"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="task.stage"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-white/5 rounded-full h-2 w-24">
                                    <div class="bg-primary-400 h-2 rounded-full" :style="'width: ' + task.progress + '%'"></div>
                                </div>
                                <span class="text-gray-400 text-xs" x-text="task.progress + '%'"></span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="{
                                    'bg-gray-500/20 text-gray-400': task.status === 'new',
                                    'bg-primary-500/20 text-primary-400': task.status === 'in_progress',
                                    'bg-green-500/20 text-green-400': task.status === 'completed',
                                    'bg-red-500/20 text-red-400': task.status === 'rejected'
                                }"
                                x-text="getStatusText(task.status)"
                            ></span>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/tasks/' + task.id" class="text-primary-400 hover:text-primary-300" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/tasks/' + task.id + '/edit'" class="text-blue-400 hover:text-blue-300" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="openStatusModal(task.id)" class="text-green-400 hover:text-green-300" title="تحديث الحالة">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteTask(task.id)" class="text-red-400 hover:text-red-300" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<!-- Sidebar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6" x-data="chartsData()" x-init="initCharts()">
    <!-- Project Completion Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">نسبة إنجاز المشاريع</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="projectCompletionChart"></canvas>
        </div>
    </div>

    <!-- Top Engineers Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">أفضل 5 مهندسين</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="topEngineersChart"></canvas>
        </div>
    </div>

    <!-- Tasks by Stage Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">عدد المهام حسب المرحلة</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="tasksByStageChart"></canvas>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
@include('components.modals.task-update')

@push('scripts')
<script>
function taskFilters() {
    return {
        search: '',
        project: '',
        engineer: '',
        stage: '',
        status: '',
        period: '',
        dateFrom: '',
        dateTo: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.project = '';
            this.engineer = '';
            this.stage = '';
            this.status = '';
            this.period = '';
            this.dateFrom = '';
            this.dateTo = '';
        }
    }
}

function tasksData() {
    return {
        viewMode: 'kanban',
        tasks: [
            {
                id: 1,
                title: 'مراجعة المخططات المعمارية',
                project: 'مشروع فيلا رقم 1',
                engineer: 'محمد أحمد',
                stage: 'معماري',
                status: 'new',
                progress: 0,
                hasAttachment: false
            },
            {
                id: 2,
                title: 'إعداد تقرير التقدم',
                project: 'مشروع مجمع سكني',
                engineer: 'فاطمة سالم',
                stage: 'إنشائي',
                status: 'in_progress',
                progress: 45,
                hasAttachment: true
            },
            {
                id: 3,
                title: 'فحص الشبكة الكهربائية',
                project: 'مشروع مبنى تجاري',
                engineer: 'خالد مطر',
                stage: 'كهربائي',
                status: 'in_progress',
                progress: 70,
                hasAttachment: true
            },
            {
                id: 4,
                title: 'توقيع المستندات',
                project: 'مشروع فيلا رقم 1',
                engineer: 'محمد أحمد',
                stage: 'معماري',
                status: 'completed',
                progress: 100,
                hasAttachment: false
            },
            {
                id: 5,
                title: 'مراجعة التصميم الميكانيكي',
                project: 'مشروع مجمع سكني',
                engineer: 'سارة علي',
                stage: 'ميكانيكي',
                status: 'rejected',
                progress: 30,
                hasAttachment: false
            }
        ],
        getTasksByStatus(status) {
            return this.tasks.filter(task => task.status === status);
        },
        getStatusText(status) {
            const statusMap = {
                'new': 'جديد',
                'in_progress': 'قيد التنفيذ',
                'completed': 'منجز',
                'rejected': 'مرفوض'
            };
            return statusMap[status] || status;
        },
        openStatusModal(id) {
            window.dispatchEvent(new CustomEvent('open-task-status-modal', { detail: { taskId: id } }));
        },
        openCommentModal(id) {
            alert('فتح نافذة التعليقات للمهمة #' + id);
        },
        openAttachmentModal(id) {
            alert('فتح نافذة المرفقات للمهمة #' + id);
        },
        deleteTask(id) {
            if (confirm('هل أنت متأكد من حذف هذه المهمة؟')) {
                console.log('Deleting task:', id);
            }
        }
    }
}

function chartsData() {
    return {
        projectChart: null,
        engineersChart: null,
        stageChart: null,
        initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initCharts(), 100);
                return;
            }

            // Project Completion Doughnut
            const projectCtx = document.getElementById('projectCompletionChart');
            if (projectCtx && !this.projectChart) {
                this.projectChart = new Chart(projectCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['مكتمل', 'قيد التنفيذ', 'متأخر'],
                        datasets: [{
                            data: [63, 31, 6],
                            backgroundColor: ['#10b981', '#1db8f8', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }

            // Top Engineers Bar Chart
            const engineersCtx = document.getElementById('topEngineersChart');
            if (engineersCtx && !this.engineersChart) {
                this.engineersChart = new Chart(engineersCtx, {
                    type: 'bar',
                    data: {
                        labels: ['محمد أحمد', 'فاطمة سالم', 'خالد مطر', 'سارة علي', 'أحمد محمد'],
                        datasets: [{
                            label: 'المهام المنجزة',
                            data: [45, 38, 32, 28, 25],
                            backgroundColor: '#1db8f8'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)',
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Tasks by Stage Pie Chart
            const stageCtx = document.getElementById('tasksByStageChart');
            if (stageCtx && !this.stageChart) {
                this.stageChart = new Chart(stageCtx, {
                    type: 'pie',
                    data: {
                        labels: ['معماري', 'إنشائي', 'كهربائي', 'ميكانيكي'],
                        datasets: [{
                            data: [45, 38, 35, 28],
                            backgroundColor: ['#1db8f8', '#10b981', '#f59e0b', '#8b5cf6']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush

@endsection


