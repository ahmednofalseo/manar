@extends('layouts.dashboard')

@php
    $total = (float) $invoice->total_amount;
    $paid = (float) $invoice->paid_amount;
    $remaining = (float) $invoice->remaining_amount;
    $paymentPercent = $total > 0 ? min(100, round(($paid / $total) * 100, 1)) : 0;
    $showOverdueAlert = $invoice->status === \App\Enums\InvoiceStatus::OVERDUE
        || ($invoice->due_date && $invoice->due_date->isPast() && $remaining > 0.00001);
    $progressBarClass = match ($invoice->status) {
        \App\Enums\InvoiceStatus::PAID => 'bg-green-400',
        \App\Enums\InvoiceStatus::PARTIAL => 'bg-yellow-400',
        default => 'bg-primary-400',
    };
    $statusChipClass = match ($invoice->status) {
        \App\Enums\InvoiceStatus::PAID => 'bg-green-500/20 text-green-400',
        \App\Enums\InvoiceStatus::PARTIAL => 'bg-yellow-500/20 text-yellow-400',
        \App\Enums\InvoiceStatus::OVERDUE => 'bg-orange-500/20 text-orange-400',
        default => 'bg-red-500/20 text-red-400',
    };
    $clientLabel = $invoice->client?->display_name ?? $invoice->client?->name ?? '—';
    $projectLabel = $invoice->project?->display_name ?? $invoice->project?->name ?? '—';
    $paymentsForAlpine = $invoice->payments->map(function ($p) {
        return [
            'id' => $p->id,
            'number' => $p->payment_no ?? ('#'.$p->id),
            'date' => optional($p->paid_at)->format('Y-m-d'),
            'amount' => (float) $p->amount,
            'status' => $p->status instanceof \BackedEnum ? $p->status->value : (string) $p->status,
            'notes' => $p->notes,
        ];
    })->values()->all();
@endphp

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
<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $invoice->number }}</h1>
        <p class="text-gray-400 text-sm">{{ __('Issue date') }}: {{ $invoice->issue_date?->format('Y-m-d') ?? '—' }}</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('financials.edit', $invoice->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-edit ml-2"></i>
            {{ __('Edit') }}
        </a>
        <a href="{{ route('financials.pdf', $invoice->id) }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
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
            <p class="text-gray-400 text-sm mb-2">{{ __('Invoice Total') }}</p>
            <p class="text-2xl md:text-3xl font-bold text-white">{{ number_format($total, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">{{ __('Invoice paid amount label') }}</p>
            <p class="text-2xl md:text-3xl font-bold text-green-400">{{ number_format($paid, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
        </div>
        <div>
            <p class="text-gray-400 text-sm mb-2">{{ __('Remaining') }}</p>
            <p class="text-2xl md:text-3xl font-bold text-red-400">{{ number_format($remaining, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
        </div>
    </div>
    <div class="w-full bg-white/5 rounded-full h-3">
        <div class="{{ $progressBarClass }} h-3 rounded-full transition-all" style="width: {{ $paymentPercent }}%"></div>
    </div>
    <p class="text-center text-gray-400 text-sm mt-2">{{ __('Invoice progress settled', ['percent' => $paymentPercent]) }}</p>
</div>

<!-- Alert for Overdue/Unpaid -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6 bg-yellow-500/10 border border-yellow-500/20 {{ $showOverdueAlert ? '' : 'hidden' }}" id="alertOverdue">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
        <div>
            <p class="text-yellow-400 font-semibold">{{ __('Invoice overdue alert title') }}</p>
            <p class="text-gray-300 text-sm">{{ __('Invoice overdue alert body') }}</p>
        </div>
    </div>
</div>

<!-- Invoice Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Invoice information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Client') }}</p>
                <p class="text-white font-semibold">{{ $clientLabel }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Project') }}</p>
                <p class="text-white font-semibold">{{ $projectLabel }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Invoice Number') }}</p>
                <p class="text-white font-semibold">{{ $invoice->number }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Due date') }}</p>
                <p class="text-white font-semibold">{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Payment method') }}</p>
                <p class="text-white font-semibold">{{ $invoice->payment_method_label ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">{{ __('Status') }}</p>
                <span class="inline-block {{ $statusChipClass }} px-3 py-1 rounded-lg text-sm font-semibold">{{ $invoice->status_label }}</span>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-400 text-sm mb-1">{{ __('Notes') }}</p>
                <p class="text-white text-sm">{{ filled($invoice->notes) ? $invoice->notes : '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Sidebar Widgets -->
    <div class="space-y-4 md:space-y-6">
        <!-- Monthly Payments Chart -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">{{ __('Monthly payments chart title') }}</h3>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="paymentsChart"></canvas>
            </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">{{ __('Payment methods chart title') }}</h3>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
            <h3 class="text-lg font-bold text-white mb-4">{{ __('Overdue invoices list title') }}</h3>
            <div class="space-y-3">
                @forelse($overdueInvoices as $ov)
                    @php
                        $ovRemaining = max(0, (float) $ov->total_amount - (float) $ov->paid_amount);
                        $ovClient = $ov->client?->display_name ?? $ov->client?->name ?? '—';
                    @endphp
                    <a href="{{ route('financials.show', $ov->id) }}" class="flex items-center justify-between p-2 bg-red-500/10 rounded-lg hover:bg-red-500/20 transition-all">
                        <div>
                            <p class="text-white text-sm font-semibold">{{ $ov->number }}</p>
                            <p class="text-gray-400 text-xs">{{ $ovClient }}</p>
                        </div>
                        <span class="text-red-400 text-sm font-semibold">{{ number_format($ovRemaining, 2) }} {{ __('Currency SAR') }}</span>
                    </a>
                @empty
                    <p class="text-gray-400 text-sm">{{ __('No other overdue invoices') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="paymentsData()">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">{{ __('Payments table title') }}</h2>
        <button @click="openPaymentModal()" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
            <i class="fas fa-plus ml-2"></i>
            {{ __('Add payment button') }}
        </button>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Payment number') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Payment date') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Amount') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Notes') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="payments.length === 0">
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400 text-sm">{{ __('No payments chart placeholder') }}</td>
                    </tr>
                </template>
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
                                <button type="button" @click="editPayment(payment.id)" class="text-primary-400 hover:text-primary-300" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
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
        <template x-if="payments.length === 0">
            <p class="text-center text-gray-400 text-sm py-8">{{ __('No payments chart placeholder') }}</p>
        </template>
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
                        <span class="text-gray-400 text-xs">{{ __('Amount') }}</span>
                        <span class="text-white font-semibold" x-text="formatCurrency(payment.amount)"></span>
                    </div>
                    <div class="flex items-center justify-between" x-show="payment.notes">
                        <span class="text-gray-400 text-xs">{{ __('Notes') }}</span>
                        <span class="text-gray-300 text-sm" x-text="payment.notes"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <button type="button" @click="editPayment(payment.id)" class="text-primary-400 hover:text-primary-300">
                        <i class="fas fa-edit ml-1"></i>
                        {{ __('Edit') }}
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
        payments: @json($paymentsForAlpine),
        formatCurrency(amount) {
            return new Intl.NumberFormat('ar-SA').format(amount) + ' {{ __('Currency SAR') }}';
        },
        getStatusText(status) {
            const statusMap = {
                'paid': '{{ __('Paid') }}',
                'pending': '{{ __('Pending') }}',
                'failed': '{{ __('Failed') }}'
            };
            return statusMap[status] || status;
        },
        openPaymentModal() {
            window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId: '{{ $invoice->id }}' } }));
        },
        editPayment(id) {
            alert('{{ __('Edit') }} #' + id);
        },
    }
}

// Initialize Charts
function initCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(initCharts, 100);
        return;
    }
    initChartsInternal();
}

function initChartsInternal() {
    const paymentsCtx = document.getElementById('paymentsChart');
    if (paymentsCtx) {
        try {
            const paymentsData = @json($invoice->payments ?? []);
            const monthlyData = {};

            paymentsData.forEach(payment => {
                if (payment.paid_at) {
                    const month = new Date(payment.paid_at).toLocaleDateString('ar-SA', { month: 'long' });
                    monthlyData[month] = (monthlyData[month] || 0) + parseFloat(payment.amount || 0);
                }
            });

            let labels = Object.keys(monthlyData);
            let data = Object.values(monthlyData);

            if (labels.length === 0) {
                labels = ['—'];
                data = [0];
            }

            new Chart(paymentsCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ __('Collected amounts label') }}',
                        data: data,
                        borderColor: '#1db8f8',
                        backgroundColor: 'rgba(29, 184, 248, 0.1)',
                        tension: 0.4,
                        fill: true
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
                                color: '#9ca3af',
                                callback: function(value) {
                                    return new Intl.NumberFormat('ar-SA').format(value) + ' {{ __('Currency SAR') }}';
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
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating payments chart:', error);
        }
    }

    const methodsCtx = document.getElementById('paymentMethodsChart');
    if (methodsCtx) {
        try {
            const paymentsData = @json($invoice->payments ?? []);
            const methodCounts = {};

            paymentsData.forEach(payment => {
                const method = payment.method || 'transfer';
                methodCounts[method] = (methodCounts[method] || 0) + 1;
            });

            const methodLabels = {
                'transfer': '{{ __('Payment method bank transfer') }}',
                'cash': '{{ __('Payment method cash') }}',
                'check': '{{ __('Payment method check') }}',
                'electronic': '{{ __('Payment method electronic') }}'
            };

            let labels = Object.keys(methodCounts).map(key => methodLabels[key] || key);
            let data = Object.values(methodCounts);

            if (labels.length === 0) {
                labels = ['{{ __('No payments chart placeholder') }}'];
                data = [1];
            }

            new Chart(methodsCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#1db8f8',
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6',
                            '#ef4444'
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
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating payment methods chart:', error);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
</script>
@endpush

@endsection
