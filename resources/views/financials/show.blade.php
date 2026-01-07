@extends('layouts.dashboard')

@section('title', __('Details') . ' - ' . __('Financials') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Details'))

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

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">فاتورة #INV-2025-001</h1>
        <p class="text-gray-400 text-sm">تاريخ الإصدار: 2025-11-01</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('financials.edit', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-edit ml-2"></i>
            {{ __('Edit') }}
        </a>
        <a href="{{ route('financials.pdf', $id) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-file-pdf {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Download') }} PDF
        </a>
        <a href="{{ route('financials.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Financial Summary -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
        <div>
            <p class="text-gray-400 text-sm mb-2">إجمالي الفاتورة</p>
            <p class="text-2xl md:text-3xl font-bold text-white">50,000 <span class="text-lg text-gray-400">ر.س</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المدفوع</p>
            <p class="text-2xl md:text-3xl font-bold text-green-400">50,000 <span class="text-lg text-gray-400">ر.س</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">المتبقي</p>
            <p class="text-2xl md:text-3xl font-bold text-red-400">0 <span class="text-lg text-gray-400">ر.س</span></p>
        </div>
    </div>
    <div class="w-full bg-white/5 rounded-full h-3">
        <div class="bg-green-400 h-3 rounded-full" style="width: 100%"></div>
    </div>
    <p class="text-center text-gray-400 text-sm mt-2">تم سداد 100% من الفاتورة</p>
</div>

<!-- Alert for Overdue/Unpaid -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6 bg-yellow-500/10 border border-yellow-500/20 hidden" id="alertOverdue">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
        <div>
            <p class="text-yellow-400 font-semibold">فاتورة متأخرة</p>
            <p class="text-gray-300 text-sm">هذه الفاتورة تجاوزت تاريخ الاستحقاق ولم يتم سدادها بالكامل</p>
        </div>
    </div>
</div>

<!-- Invoice Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-6">معلومات الفاتورة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">العميل</p>
                <p class="text-white font-semibold">أحمد محمد</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">المشروع</p>
                <p class="text-white font-semibold">مشروع فيلا رقم 1</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">رقم الفاتورة</p>
                <p class="text-white font-semibold">INV-2025-001</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">تاريخ الاستحقاق</p>
                <p class="text-white font-semibold">2025-11-15</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">طريقة الدفع</p>
                <p class="text-white font-semibold">تحويل بنكي</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">الحالة</p>
                <span class="inline-block bg-green-500/20 text-green-400 px-3 py-1 rounded-lg text-sm font-semibold">مدفوعة</span>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">ملاحظات</p>
                <p class="text-white text-sm">دفعة أولية للمشروع - تم السداد كاملاً</p>
            </div>
        </div>
    </div>

    <!-- Sidebar Widgets -->
    <div class="space-y-4 md:space-y-6">
        <!-- Monthly Payments Chart -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">تحليل الدفعات الشهرية</h3>
            <canvas id="paymentsChart" height="200"></canvas>
        </div>

        <!-- Payment Methods Chart -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">طرق الدفع</h3>
            <canvas id="paymentMethodsChart" height="200"></canvas>
        </div>

        <!-- Overdue Invoices -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">فواتير متأخرة</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-2 bg-red-500/10 rounded-lg">
                    <div>
                        <p class="text-white text-sm font-semibold">INV-2025-003</p>
                        <p class="text-gray-400 text-xs">خالد مطر</p>
                    </div>
                    <span class="text-red-400 text-sm font-semibold">100,000 ر.س</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-red-500/10 rounded-lg">
                    <div>
                        <p class="text-white text-sm font-semibold">INV-2025-005</p>
                        <p class="text-gray-400 text-xs">سارة أحمد</p>
                    </div>
                    <span class="text-red-400 text-sm font-semibold">75,000 ر.س</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="paymentsData()">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">جدول الدفعات</h2>
        <button @click="openPaymentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            إضافة دفعة
        </button>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">رقم الدفعة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">التاريخ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">المبلغ</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الحالة</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الملاحظات</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="payment in payments" :key="payment.id">
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                        <td class="py-3 text-white text-sm font-semibold" x-text="payment.number"></td>
                        <td class="py-3 text-gray-300 text-sm" x-text="payment.date"></td>
                        <td class="py-3 text-white font-semibold" x-text="formatCurrency(payment.amount)"></td>
                        <td class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="{
                                    'bg-green-500/20 text-green-400': payment.status === 'paid',
                                    'bg-yellow-500/20 text-yellow-400': payment.status === 'pending',
                                    'bg-red-500/20 text-red-400': payment.status === 'failed'
                                }"
                                x-text="getStatusText(payment.status)"
                            ></span>
                        </td>
                        <td class="py-3 text-gray-300 text-sm" x-text="payment.notes || '-'"></td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <button @click="editPayment(payment.id)" class="text-primary-400 hover:text-primary-300" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deletePayment(payment.id)" class="text-red-400 hover:text-red-300" title="حذف">
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
        <template x-for="payment in payments" :key="payment.id">
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1" x-text="payment.number"></h3>
                        <p class="text-gray-400 text-sm" x-text="payment.date"></p>
                    </div>
                    <span 
                        class="px-2 py-1 rounded text-xs font-semibold"
                        :class="{
                            'bg-green-500/20 text-green-400': payment.status === 'paid',
                            'bg-yellow-500/20 text-yellow-400': payment.status === 'pending',
                            'bg-red-500/20 text-red-400': payment.status === 'failed'
                        }"
                        x-text="getStatusText(payment.status)"
                    ></span>
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">المبلغ</span>
                        <span class="text-white font-semibold" x-text="formatCurrency(payment.amount)"></span>
                    </div>
                    <div class="flex items-center justify-between" x-show="payment.notes">
                        <span class="text-gray-400 text-xs">الملاحظات</span>
                        <span class="text-gray-300 text-sm" x-text="payment.notes"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <button @click="editPayment(payment.id)" class="text-primary-400 hover:text-primary-300">
                        <i class="fas fa-edit ml-1"></i>
                        تعديل
                    </button>
                    <button @click="deletePayment(payment.id)" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-trash ml-1"></i>
                        حذف
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
function paymentsData() {
    return {
        payments: [
            {
                id: 1,
                number: 'PAY-001',
                date: '2025-11-05',
                amount: 50000,
                status: 'paid',
                notes: 'دفعة أولية - تحويل بنكي'
            }
        ],
        formatCurrency(amount) {
            return new Intl.NumberFormat('ar-SA').format(amount) + ' ر.س';
        },
        getStatusText(status) {
            const statusMap = {
                'paid': 'مدفوع',
                'pending': 'قيد الانتظار',
                'failed': 'فشل'
            };
            return statusMap[status] || status;
        },
        openPaymentModal() {
            // Trigger event to open modal
            window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId: '{{ $id }}' } }));
        },
        editPayment(id) {
            alert('تعديل دفعة #' + id);
        },
        deletePayment(id) {
            if (confirm('هل أنت متأكد من حذف هذه الدفعة؟')) {
                console.log('Deleting payment:', id);
            }
        }
    }
}

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Payments Line Chart
    const paymentsCtx = document.getElementById('paymentsChart');
    if (paymentsCtx) {
        new Chart(paymentsCtx, {
            type: 'line',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                datasets: [{
                    label: 'المبالغ المحصلة',
                    data: [45000, 52000, 48000, 61000, 55000, 67000],
                    borderColor: '#1db8f8',
                    backgroundColor: 'rgba(29, 184, 248, 0.1)',
                    tension: 0.4
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
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Doughnut Chart
    const methodsCtx = document.getElementById('paymentMethodsChart');
    if (methodsCtx) {
        new Chart(methodsCtx, {
            type: 'doughnut',
            data: {
                labels: ['تحويل بنكي', 'نقدي', 'شيك', 'إلكتروني'],
                datasets: [{
                    data: [40, 30, 20, 10],
                    backgroundColor: [
                        '#1db8f8',
                        '#10b981',
                        '#f59e0b',
                        '#8b5cf6'
                    ]
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
});
</script>
@endpush

@endsection

