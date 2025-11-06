@extends('layouts.dashboard')

@section('title', 'إدارة المصروفات - المنار')
@section('page-title', 'إدارة المصروفات')

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">إدارة المصروفات</h1>
    <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
        <i class="fas fa-plus-circle ml-2"></i>
        إضافة مصروف جديد
    </a>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي المصروفات هذا الشهر</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">125,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المصروفات المعتمدة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">95,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-circle-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المصروفات المرفوضة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-red-400 mt-1 md:mt-2">5,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-ban text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">بانتظار الموافقة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2">25,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="expenseFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="بحث: الوصف، رقم السند..." 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4">
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

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">القسم</label>
                <select x-model="department" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأقسام</option>
                    <option value="إدارة">إدارة</option>
                    <option value="مشاريع">مشاريع</option>
                    <option value="مالية">مالية</option>
                    <option value="مبيعات">مبيعات</option>
                    <option value="تسويق">تسويق</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">نوع المصروف</label>
                <select x-model="type" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الأنواع</option>
                    <option value="رواتب">رواتب</option>
                    <option value="إيجار">إيجار</option>
                    <option value="كهرباء">كهرباء</option>
                    <option value="مياه">مياه</option>
                    <option value="صيانة">صيانة</option>
                    <option value="معدات">معدات</option>
                    <option value="نقل">نقل</option>
                    <option value="أخرى">أخرى</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="approved">معتمد</option>
                    <option value="rejected">مرفوض</option>
                    <option value="pending">بانتظار</option>
                </select>
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

<!-- Expenses Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="expensesData()">
    <!-- Bulk Actions -->
    <div x-show="selectedExpenses.length > 0" class="mb-4 p-3 bg-primary-500/20 rounded-lg flex items-center justify-between">
        <span class="text-white text-sm" x-text="selectedExpenses.length + ' مصروف محدد'"></span>
        <div class="flex items-center gap-2">
            <button @click="bulkApprove()" class="px-3 py-1 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded text-sm">
                <i class="fas fa-circle-check ml-1"></i>
                اعتماد
            </button>
            <button @click="bulkReject()" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-ban ml-1"></i>
                رفض
            </button>
            <button @click="bulkDelete()" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash ml-1"></i>
                حذف
            </button>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="pb-3">
                        <input type="checkbox" @change="toggleAll()" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                    </th>
                    <th class="text-gray-400 text-sm font-normal pb-3">رقم السند</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">التاريخ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">القسم</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">نوع المصروف</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الوصف</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المبلغ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">طريقة الدفع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المعتمد</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="expense in expenses" :key="expense.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3">
                            <input type="checkbox" :value="expense.id" x-model="selectedExpenses" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        </td>
                        <td class="py-3 text-white text-sm font-semibold" x-text="expense.voucherNumber"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="expense.date"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="expense.department"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="expense.type"></td>
                        <td class="py-3 text-gray-300 text-sm max-w-xs truncate" x-text="expense.description"></td>
                        <td class="py-3 text-white font-semibold" x-text="formatCurrency(expense.amount)"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="expense.paymentMethod"></td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="{
                                    'bg-green-500/20 text-green-400': expense.status === 'approved',
                                    'bg-red-500/20 text-red-400': expense.status === 'rejected',
                                    'bg-yellow-500/20 text-yellow-400': expense.status === 'pending'
                                }"
                                x-text="getStatusText(expense.status)"
                            ></span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="expense.approver || '-'"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/expenses/' + expense.id" class="text-primary-400 hover:text-primary-300" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/expenses/' + expense.id + '/edit'" class="text-blue-400 hover:text-blue-300" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="downloadAttachment(expense.id)" class="text-purple-400 hover:text-purple-300" title="تحميل مرفق" x-show="expense.hasAttachment">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button @click="approveExpense(expense.id)" class="text-green-400 hover:text-green-300" title="اعتماد" x-show="expense.status === 'pending'">
                                    <i class="fas fa-circle-check"></i>
                                </button>
                                <button @click="rejectExpense(expense.id)" class="text-red-400 hover:text-red-300" title="رفض" x-show="expense.status === 'pending'">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <button @click="deleteExpense(expense.id)" class="text-red-400 hover:text-red-300" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <template x-for="expense in expenses" :key="expense.id">
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1" x-text="expense.voucherNumber"></h3>
                        <p class="text-gray-400 text-sm" x-text="expense.date"></p>
                    </div>
                    <input type="checkbox" :value="expense.id" x-model="selectedExpenses" class="rounded border-white/20 bg-white/5 text-primary-400">
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">القسم</span>
                        <span class="text-white text-sm" x-text="expense.department"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">النوع</span>
                        <span class="text-white text-sm" x-text="expense.type"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المبلغ</span>
                        <span class="text-white font-semibold" x-text="formatCurrency(expense.amount)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الحالة</span>
                        <span 
                            class="px-2 py-1 rounded text-xs font-semibold"
                            :class="{
                                'bg-green-500/20 text-green-400': expense.status === 'approved',
                                'bg-red-500/20 text-red-400': expense.status === 'rejected',
                                'bg-yellow-500/20 text-yellow-400': expense.status === 'pending'
                            }"
                            x-text="getStatusText(expense.status)"
                        ></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <a :href="'/expenses/' + expense.id" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a :href="'/expenses/' + expense.id + '/edit'" class="text-blue-400 hover:text-blue-300">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button @click="downloadAttachment(expense.id)" class="text-purple-400 hover:text-purple-300" x-show="expense.hasAttachment">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="approveExpense(expense.id)" class="text-green-400 hover:text-green-300" x-show="expense.status === 'pending'">
                            <i class="fas fa-circle-check"></i>
                        </button>
                        <button @click="rejectExpense(expense.id)" class="text-red-400 hover:text-red-300" x-show="expense.status === 'pending'">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Sidebar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6" x-data="chartsData()" x-init="initCharts()">
    <!-- Monthly Expenses Chart -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">المصروفات الشهرية</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>
    </div>

    <!-- Expense Types Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">توزيع المصروفات حسب النوع</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="expenseTypesChart"></canvas>
        </div>
    </div>
</div>

<!-- Top 5 Expenses -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mt-4 md:mt-6">
    <h3 class="text-lg font-bold text-white mb-4">أعلى 5 بنود صرف متكررة</h3>
    <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-primary-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">كهرباء</p>
                    <p class="text-gray-400 text-xs">12 مصروف</p>
                </div>
            </div>
            <span class="text-white font-semibold">45,000 ر.س</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tools text-blue-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">صيانة</p>
                    <p class="text-gray-400 text-xs">8 مصروفات</p>
                </div>
            </div>
            <span class="text-white font-semibold">30,000 ر.س</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-green-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">نقل</p>
                    <p class="text-gray-400 text-xs">15 مصروف</p>
                </div>
            </div>
            <span class="text-white font-semibold">25,000 ر.س</span>
        </div>
    </div>
</div>

<!-- Approval Modal -->
@include('components.modals.expense-approval')

@push('scripts')
<script>
function expenseFilters() {
    return {
        search: '',
        dateFrom: '',
        dateTo: '',
        department: '',
        type: '',
        status: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.department = '';
            this.type = '';
            this.status = '';
        }
    }
}

function expensesData() {
    return {
        selectedExpenses: [],
        expenses: [
            {
                id: 1,
                voucherNumber: 'EXP-2025-001',
                date: '2025-11-01',
                department: 'إدارة',
                type: 'كهرباء',
                description: 'فاتورة كهرباء المكتب',
                amount: 2500,
                paymentMethod: 'تحويل بنكي',
                status: 'approved',
                approver: 'محمد أحمد',
                hasAttachment: true
            },
            {
                id: 2,
                voucherNumber: 'EXP-2025-002',
                date: '2025-11-03',
                department: 'مشاريع',
                type: 'صيانة',
                description: 'صيانة معدات المشروع',
                amount: 5000,
                paymentMethod: 'نقدي',
                status: 'pending',
                approver: null,
                hasAttachment: false
            },
            {
                id: 3,
                voucherNumber: 'EXP-2025-003',
                date: '2025-11-05',
                department: 'مالية',
                type: 'نقل',
                description: 'تكاليف نقل المواد',
                amount: 1500,
                paymentMethod: 'شيك',
                status: 'rejected',
                approver: 'فاطمة سالم',
                hasAttachment: true
            }
        ],
        toggleAll() {
            // TODO: Implement toggle all
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('ar-SA').format(amount) + ' ر.س';
        },
        getStatusText(status) {
            const statusMap = {
                'approved': 'معتمد',
                'rejected': 'مرفوض',
                'pending': 'بانتظار'
            };
            return statusMap[status] || status;
        },
        approveExpense(id) {
            window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: id, action: 'approve' } }));
        },
        rejectExpense(id) {
            window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: id, action: 'reject' } }));
        },
        deleteExpense(id) {
            if (confirm('هل أنت متأكد من حذف هذا المصروف؟')) {
                console.log('Deleting expense:', id);
            }
        },
        downloadAttachment(id) {
            alert('تحميل مرفق المصروف #' + id);
        },
        bulkApprove() {
            if (this.selectedExpenses.length === 0) return;
            if (confirm('هل أنت متأكد من اعتماد ' + this.selectedExpenses.length + ' مصروف؟')) {
                console.log('Bulk approve:', this.selectedExpenses);
            }
        },
        bulkReject() {
            if (this.selectedExpenses.length === 0) return;
            if (confirm('هل أنت متأكد من رفض ' + this.selectedExpenses.length + ' مصروف؟')) {
                console.log('Bulk reject:', this.selectedExpenses);
            }
        },
        bulkDelete() {
            if (this.selectedExpenses.length === 0) return;
            if (confirm('هل أنت متأكد من حذف ' + this.selectedExpenses.length + ' مصروف؟')) {
                console.log('Bulk delete:', this.selectedExpenses);
            }
        }
    }
}

function chartsData() {
    return {
        monthlyChart: null,
        typesChart: null,
        initCharts() {
            // Wait for Chart.js to be loaded
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initCharts(), 100);
                return;
            }

            // Monthly Expenses Line Chart
            const monthlyCtx = document.getElementById('monthlyExpensesChart');
            if (monthlyCtx && !this.monthlyChart) {
                this.monthlyChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر'],
                        datasets: [{
                            label: 'المصروفات',
                            data: [120000, 130000, 115000, 140000, 125000, 135000, 128000, 132000, 125000, 130000, 125000],
                            borderColor: '#1db8f8',
                            backgroundColor: 'rgba(29, 184, 248, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#1db8f8',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(23, 51, 67, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#1db8f8',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#9ca3af',
                                    callback: function(value) {
                                        return new Intl.NumberFormat('ar-SA').format(value) + ' ر.س';
                                    }
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

            // Expense Types Doughnut Chart
            const typesCtx = document.getElementById('expenseTypesChart');
            if (typesCtx && !this.typesChart) {
                this.typesChart = new Chart(typesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['كهرباء', 'صيانة', 'نقل', 'إيجار', 'أخرى'],
                        datasets: [{
                            data: [30, 25, 20, 15, 10],
                            backgroundColor: [
                                '#1db8f8',
                                '#10b981',
                                '#f59e0b',
                                '#8b5cf6',
                                '#ef4444'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#9ca3af',
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(23, 51, 67, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#1db8f8',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed + '%';
                                        return label;
                                    }
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

