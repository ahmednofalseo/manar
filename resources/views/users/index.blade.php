@extends('layouts.dashboard')

@section('title', 'المستخدمون والأدوار - المنار')
@section('page-title', 'المستخدمون والأدوار')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-card {
        transition: all 0.3s ease;
    }

    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(29, 184, 248, 0.2);
    }

    /* Better table scroll on mobile */
    @media (max-width: 768px) {
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
        
        table {
            min-width: 600px;
        }
    }

    /* Prevent text overflow */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<!-- Filters and Actions Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-3 md:p-4 lg:p-6 mb-4 md:mb-6">
    <div class="flex flex-col gap-3 md:gap-4">
        <div class="flex-1 w-full">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="بحث عن مستخدم..." 
                    class="w-full md:w-96 bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                <option>جميع الأدوار</option>
                <option>مدير</option>
                <option>مهندس</option>
                <option>محاسب</option>
                <option>مستخدم</option>
            </select>
            <select class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                <option>جميع الحالات</option>
                <option>نشط</option>
                <option>معطل</option>
                <option>قيد الانتظار</option>
            </select>
            <button onclick="openUserModal()" class="w-full bg-primary-500 hover:bg-primary-600 text-white px-4 md:px-6 py-2 rounded-lg transition-all duration-200 text-sm md:text-base whitespace-nowrap">
                <i class="fas fa-plus ml-2"></i>
                <span class="hidden sm:inline">إضافة مستخدم</span>
                <span class="sm:hidden">إضافة</span>
            </button>
        </div>
    </div>
</div>

<!-- Users Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي المستخدمين</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">24</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-gray-400 text-xs">+3 جدد هذا الشهر</p>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المستخدمون النشطون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">20</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-green-400 text-xs">83% من إجمالي المستخدمين</p>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المعطلون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">3</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-slash text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-red-400 text-xs">12% من إجمالي المستخدمين</p>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المديرون</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">5</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-shield text-purple-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <p class="text-purple-400 text-xs">21% من إجمالي المستخدمين</p>
    </div>
</div>

<!-- Users Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 md:mb-6">
        <h2 class="text-lg md:text-xl font-bold text-white">قائمة المستخدمين</h2>
        <div class="flex items-center gap-2">
            <button class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200" title="تصدير">
                <i class="fas fa-download"></i>
            </button>
            <button class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 hidden sm:block" title="طباعة">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto -mx-4 md:mx-0">
        <div class="min-w-full inline-block">
            <table class="w-full">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4 hidden sm:table-cell">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                        </th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">المستخدم</th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-0 hidden md:table-cell">البريد الإلكتروني</th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-0 hidden lg:table-cell">الدور</th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-0 hidden xl:table-cell">تاريخ التسجيل</th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">الحالة</th>
                        <th class="text-gray-400 text-xs md:text-sm font-normal pb-3 px-2 md:px-4">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <!-- User Row 1 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="py-3 md:py-4 px-2 md:px-4 hidden sm:table-cell">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-2 md:gap-3">
                                <img src="https://ui-avatars.com/api/?name=أحمد+محمد&background=1db8f8&color=fff&size=128" alt="أحمد محمد" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-primary-400 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-sm md:text-base truncate">أحمد محمد</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <p class="text-gray-400 text-xs md:hidden">ahmed@example.com</p>
                                        <span class="bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded text-xs lg:hidden">مدير</span>
                                        <span class="text-gray-400 text-xs xl:hidden">2024-01-15</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden md:table-cell text-gray-300 text-sm">ahmed@example.com</td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden lg:table-cell">
                            <span class="bg-purple-500/20 text-purple-400 px-2 py-1 rounded text-xs">مدير</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden xl:table-cell text-gray-300 text-xs md:text-sm">2024-01-15</td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs whitespace-nowrap">نشط</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-1 md:gap-2">
                                <button onclick="viewUser(1)" class="p-1.5 md:p-2 text-primary-400 hover:bg-primary-500/20 rounded-lg transition-all duration-200" title="عرض">
                                    <i class="fas fa-eye text-xs md:text-sm"></i>
                                </button>
                                <button onclick="editUser(1)" class="p-1.5 md:p-2 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-all duration-200" title="تعديل">
                                    <i class="fas fa-edit text-xs md:text-sm"></i>
                                </button>
                                <button onclick="deleteUser(1)" class="p-1.5 md:p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition-all duration-200" title="حذف">
                                    <i class="fas fa-trash text-xs md:text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- User Row 2 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="py-3 md:py-4 px-2 md:px-4 hidden sm:table-cell">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-2 md:gap-3">
                                <img src="https://ui-avatars.com/api/?name=فاطمة+سالم&background=1db8f8&color=fff&size=128" alt="فاطمة سالم" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-primary-400 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-sm md:text-base truncate">فاطمة سالم</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <p class="text-gray-400 text-xs md:hidden">fatima@example.com</p>
                                        <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs lg:hidden">مهندسة</span>
                                        <span class="text-gray-400 text-xs xl:hidden">2024-02-20</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden md:table-cell text-gray-300 text-sm">fatima@example.com</td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden lg:table-cell">
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">مهندسة</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden xl:table-cell text-gray-300 text-xs md:text-sm">2024-02-20</td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs whitespace-nowrap">نشط</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-1 md:gap-2">
                                <button onclick="viewUser(2)" class="p-1.5 md:p-2 text-primary-400 hover:bg-primary-500/20 rounded-lg transition-all duration-200" title="عرض">
                                    <i class="fas fa-eye text-xs md:text-sm"></i>
                                </button>
                                <button onclick="editUser(2)" class="p-1.5 md:p-2 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-all duration-200" title="تعديل">
                                    <i class="fas fa-edit text-xs md:text-sm"></i>
                                </button>
                                <button onclick="deleteUser(2)" class="p-1.5 md:p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition-all duration-200" title="حذف">
                                    <i class="fas fa-trash text-xs md:text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- User Row 3 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="py-3 md:py-4 px-2 md:px-4 hidden sm:table-cell">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-2 md:gap-3">
                                <img src="https://ui-avatars.com/api/?name=خالد+مطر&background=1db8f8&color=fff&size=128" alt="خالد مطر" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-primary-400 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-sm md:text-base truncate">خالد مطر</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <p class="text-gray-400 text-xs md:hidden">khalid@example.com</p>
                                        <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-xs lg:hidden">محاسب</span>
                                        <span class="text-gray-400 text-xs xl:hidden">2024-03-10</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden md:table-cell text-gray-300 text-sm">khalid@example.com</td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden lg:table-cell">
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs">محاسب</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden xl:table-cell text-gray-300 text-xs md:text-sm">2024-03-10</td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded text-xs whitespace-nowrap">قيد الانتظار</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-1 md:gap-2">
                                <button onclick="viewUser(3)" class="p-1.5 md:p-2 text-primary-400 hover:bg-primary-500/20 rounded-lg transition-all duration-200" title="عرض">
                                    <i class="fas fa-eye text-xs md:text-sm"></i>
                                </button>
                                <button onclick="editUser(3)" class="p-1.5 md:p-2 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-all duration-200" title="تعديل">
                                    <i class="fas fa-edit text-xs md:text-sm"></i>
                                </button>
                                <button onclick="deleteUser(3)" class="p-1.5 md:p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition-all duration-200" title="حذف">
                                    <i class="fas fa-trash text-xs md:text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- User Row 4 -->
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="py-3 md:py-4 px-2 md:px-4 hidden sm:table-cell">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500">
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-2 md:gap-3">
                                <img src="https://ui-avatars.com/api/?name=سارة+علي&background=1db8f8&color=fff&size=128" alt="سارة علي" class="w-10 h-10 md:w-12 md:h-12 rounded-full border-2 border-primary-400 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-sm md:text-base truncate">سارة علي</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <p class="text-gray-400 text-xs md:hidden">sara@example.com</p>
                                        <span class="bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded text-xs lg:hidden">مستخدم</span>
                                        <span class="text-gray-400 text-xs xl:hidden">2024-04-05</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden md:table-cell text-gray-300 text-sm">sara@example.com</td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden lg:table-cell">
                            <span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded text-xs">مستخدم</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-0 hidden xl:table-cell text-gray-300 text-xs md:text-sm">2024-04-05</td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded text-xs whitespace-nowrap">معطل</span>
                        </td>
                        <td class="py-3 md:py-4 px-2 md:px-4">
                            <div class="flex items-center gap-1 md:gap-2">
                                <button onclick="viewUser(4)" class="p-1.5 md:p-2 text-primary-400 hover:bg-primary-500/20 rounded-lg transition-all duration-200" title="عرض">
                                    <i class="fas fa-eye text-xs md:text-sm"></i>
                                </button>
                                <button onclick="editUser(4)" class="p-1.5 md:p-2 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-all duration-200" title="تعديل">
                                    <i class="fas fa-edit text-xs md:text-sm"></i>
                                </button>
                                <button onclick="deleteUser(4)" class="p-1.5 md:p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition-all duration-200" title="حذف">
                                    <i class="fas fa-trash text-xs md:text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4 md:mt-6 pt-4 md:pt-6 border-t border-white/10">
        <div class="text-gray-400 text-xs md:text-sm">
            عرض <span class="text-white font-semibold">1</span> إلى <span class="text-white font-semibold">4</span> من <span class="text-white font-semibold">24</span> مستخدم
        </div>
        <div class="flex items-center gap-1 md:gap-2">
            <button class="px-2 md:px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-xs md:text-sm" disabled>
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="px-3 md:px-4 py-2 bg-primary-500 text-white rounded-lg text-xs md:text-sm">1</button>
            <button class="px-3 md:px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-xs md:text-sm hidden sm:block">2</button>
            <button class="px-3 md:px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-xs md:text-sm hidden md:block">3</button>
            <button class="px-2 md:px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-xs md:text-sm">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-3 md:p-4" onclick="closeModalOnBackdrop(event, 'userModal')">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-2xl w-full max-h-[95vh] md:max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-base md:text-lg lg:text-xl font-bold text-white" id="modalTitle">إضافة مستخدم جديد</h3>
            <button onclick="closeModal('userModal')" class="text-gray-400 hover:text-white p-1">
                <i class="fas fa-times text-lg md:text-xl"></i>
            </button>
        </div>
        <form class="space-y-3 md:space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الاسم الأول</label>
                    <input type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">اسم العائلة</label>
                    <input type="text" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                </div>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">البريد الإلكتروني</label>
                <input type="email" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">كلمة المرور</label>
                    <input type="password" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">تأكيد كلمة المرور</label>
                    <input type="password" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الدور</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                        <option>اختر الدور</option>
                        <option>مدير</option>
                        <option>مهندس</option>
                        <option>محاسب</option>
                        <option>مستخدم</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                    <select class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                        <option>نشط</option>
                        <option>معطل</option>
                        <option>قيد الانتظار</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم الهاتف</label>
                <input type="tel" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('userModal')" class="flex-1 bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-lg transition-all duration-200">
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
    // Open User Modal
    function openUserModal(userId = null) {
        const modal = document.getElementById('userModal');
        const title = document.getElementById('modalTitle');
        
        if (userId) {
            title.textContent = 'تعديل مستخدم';
        } else {
            title.textContent = 'إضافة مستخدم جديد';
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // View User
    function viewUser(userId) {
        showToast('عرض بيانات المستخدم #' + userId, 'info');
        // Implement view logic
    }

    // Edit User
    function editUser(userId) {
        openUserModal(userId);
        // Implement edit logic
    }

    // Delete User
    function deleteUser(userId) {
        if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
            showToast('تم حذف المستخدم بنجاح', 'success');
            // Implement delete logic
        }
    }

    // Close Modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Close Modal on Backdrop Click
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
