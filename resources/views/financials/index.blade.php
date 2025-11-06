@extends('layouts.dashboard')

@section('title', 'الفواتير والدفعات - المنار')
@section('page-title', 'الفواتير والدفعات')

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">الفواتير والدفعات</h1>
    <a href="{{ route('financials.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
        <i class="fas fa-plus ml-2"></i>
        إنشاء فاتورة جديدة
    </a>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي الفواتير هذا الشهر</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">28</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-invoice-dollar text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">إجمالي المبالغ المحصلة</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">450,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">المبالغ المتبقية</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2">150,000 <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">نسبة التحصيل</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">75%</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-percentage text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-white/5 rounded-full h-2">
                <div class="bg-primary-400 h-2 rounded-full" style="width: 75%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6" x-data="invoiceFilters()">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="بحث: اسم العميل، المشروع، رقم الفاتورة..." 
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
                <label class="block text-gray-300 text-xs md:text-sm mb-2">العميل</label>
                <select x-model="client" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع العملاء</option>
                    <option value="1">أحمد محمد</option>
                    <option value="2">فاطمة سالم</option>
                    <option value="3">خالد مطر</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">الحالة</label>
                <select x-model="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">جميع الحالات</option>
                    <option value="paid">مدفوعة</option>
                    <option value="partial">جزئية</option>
                    <option value="unpaid">غير مدفوعة</option>
                    <option value="overdue">متأخرة</option>
                </select>
            </div>

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

<!-- Invoices Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="invoicesData()">
    <!-- Bulk Actions -->
    <div x-show="selectedInvoices.length > 0" class="mb-4 p-3 bg-primary-500/20 rounded-lg flex items-center justify-between">
        <span class="text-white text-sm" x-text="selectedInvoices.length + ' فاتورة محددة'"></span>
        <div class="flex items-center gap-2">
            <button class="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-sm">
                <i class="fas fa-file-export ml-1"></i>
                تصدير
            </button>
            <button class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
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
                    <th class="text-gray-400 text-sm font-normal pb-3">رقم الفاتورة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">العميل</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المشروع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">تاريخ الإصدار</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">تاريخ الاستحقاق</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المبلغ الإجمالي</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المبلغ المدفوع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">طريقة الدفع</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="invoice in invoices" :key="invoice.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3">
                            <input type="checkbox" :value="invoice.id" x-model="selectedInvoices" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        </td>
                        <td class="py-3 text-white text-sm font-semibold" x-text="invoice.number"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="invoice.client"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="invoice.project"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="invoice.issueDate"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="invoice.dueDate"></td>
                        <td class="py-3 text-white font-semibold" x-text="formatCurrency(invoice.total)"></td>
                        <td class="py-3 text-white font-semibold" x-text="formatCurrency(invoice.paid)"></td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="{
                                    'bg-green-500/20 text-green-400': invoice.status === 'paid',
                                    'bg-yellow-500/20 text-yellow-400': invoice.status === 'partial',
                                    'bg-red-500/20 text-red-400': invoice.status === 'unpaid',
                                    'bg-orange-500/20 text-orange-400': invoice.status === 'overdue'
                                }"
                                x-text="getStatusText(invoice.status)"
                            ></span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="invoice.paymentMethod"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a :href="'/financials/' + invoice.id" class="text-primary-400 hover:text-primary-300" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a :href="'/financials/' + invoice.id + '/edit'" class="text-blue-400 hover:text-blue-300" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button @click="generatePdf(invoice.id)" class="text-red-400 hover:text-red-300" title="توليد PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button @click="openPaymentModal(invoice.id)" class="text-green-400 hover:text-green-300" title="إضافة دفعة">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>
                                <button @click="linkThirdParty(invoice.id)" class="text-purple-400 hover:text-purple-300" title="ربط طرف ثالث">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button @click="deleteInvoice(invoice.id)" class="text-red-400 hover:text-red-300" title="حذف">
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
        <template x-for="invoice in invoices" :key="invoice.id">
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1" x-text="invoice.number"></h3>
                        <p class="text-gray-400 text-sm" x-text="invoice.client"></p>
                    </div>
                    <input type="checkbox" :value="invoice.id" x-model="selectedInvoices" class="rounded border-white/20 bg-white/5 text-primary-400">
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المشروع</span>
                        <span class="text-white text-sm" x-text="invoice.project"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">تاريخ الإصدار</span>
                        <span class="text-white text-sm" x-text="invoice.issueDate"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المبلغ الإجمالي</span>
                        <span class="text-white font-semibold" x-text="formatCurrency(invoice.total)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المدفوع</span>
                        <span class="text-green-400 font-semibold" x-text="formatCurrency(invoice.paid)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">الحالة</span>
                        <span 
                            class="px-2 py-1 rounded text-xs font-semibold"
                            :class="{
                                'bg-green-500/20 text-green-400': invoice.status === 'paid',
                                'bg-yellow-500/20 text-yellow-400': invoice.status === 'partial',
                                'bg-red-500/20 text-red-400': invoice.status === 'unpaid',
                                'bg-orange-500/20 text-orange-400': invoice.status === 'overdue'
                            }"
                            x-text="getStatusText(invoice.status)"
                        ></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <a :href="'/financials/' + invoice.id" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a :href="'/financials/' + invoice.id + '/edit'" class="text-blue-400 hover:text-blue-300">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button @click="generatePdf(invoice.id)" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                    <button @click="openPaymentModal(invoice.id)" class="px-3 py-1 bg-primary-500/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        <i class="fas fa-dollar-sign ml-1"></i>
                        دفعة
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Payment Modal -->
@include('components.modals.payment-modal')

@push('scripts')
<script>
function invoiceFilters() {
    return {
        search: '',
        project: '',
        client: '',
        status: '',
        dateFrom: '',
        dateTo: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.project = '';
            this.client = '';
            this.status = '';
            this.dateFrom = '';
            this.dateTo = '';
        }
    }
}

function invoicesData() {
    return {
        selectedInvoices: [],
        invoices: [
            {
                id: 1,
                number: 'INV-2025-001',
                client: 'أحمد محمد',
                project: 'مشروع فيلا رقم 1',
                issueDate: '2025-11-01',
                dueDate: '2025-11-15',
                total: 50000,
                paid: 50000,
                status: 'paid',
                paymentMethod: 'تحويل بنكي'
            },
            {
                id: 2,
                number: 'INV-2025-002',
                client: 'فاطمة سالم',
                project: 'مشروع مجمع سكني',
                issueDate: '2025-11-05',
                dueDate: '2025-11-20',
                total: 75000,
                paid: 25000,
                status: 'partial',
                paymentMethod: 'نقدي'
            },
            {
                id: 3,
                number: 'INV-2025-003',
                client: 'خالد مطر',
                project: 'مشروع مبنى تجاري',
                issueDate: '2025-10-28',
                dueDate: '2025-11-10',
                total: 100000,
                paid: 0,
                status: 'overdue',
                paymentMethod: 'شيك'
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
                'paid': 'مدفوعة',
                'partial': 'جزئية',
                'unpaid': 'غير مدفوعة',
                'overdue': 'متأخرة'
            };
            return statusMap[status] || status;
        },
        generatePdf(id) {
            window.location.href = '/financials/' + id + '/pdf';
        },
        openPaymentModal(id) {
            // Trigger event to open modal
            window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId: id } }));
        },
        linkThirdParty(id) {
            alert('ربط طرف ثالث للفاتورة #' + id);
        },
        deleteInvoice(id) {
            if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟')) {
                // TODO: Implement delete
                console.log('Deleting invoice:', id);
            }
        }
    }
}
</script>
@endpush

@endsection

