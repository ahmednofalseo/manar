@extends('layouts.dashboard')

@section('title', __('Financials') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Financials'))

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
<div x-data="financialsPage()">
<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Financials') }}</h1>
    <a href="{{ route('financials.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
        <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('New Invoice') }}
    </a>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Invoices This Month') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalInvoicesThisMonth) }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-invoice-dollar text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Collected Amounts') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">{{ number_format($totalCollected, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Remaining Amounts') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2">{{ number_format($totalRemaining, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Collection Rate') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($collectionRate, 1) }}%</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-percentage text-blue-400 text-xl md:text-2xl"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="w-full bg-white/5 rounded-full h-2">
                <div class="bg-primary-400 h-2 rounded-full" style="width: {{ min(100, $collectionRate) }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                x-model="search"
                placeholder="{{ __('Financials search placeholder') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pe-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute end-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Projects') }}</label>
                <select x-model="project" name="project_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Projects') }}</option>
                    @foreach($projects as $projectItem)
                        <option value="{{ $projectItem->id }}" {{ request('project_id') == $projectItem->id ? 'selected' : '' }}>
                            {{ $projectItem->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Clients') }}</label>
                <select x-model="client" name="client_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All clients') }}</option>
                    @foreach($clients as $clientItem)
                        <option value="{{ $clientItem->id }}" {{ request('client_id') == $clientItem->id ? 'selected' : '' }}>
                            {{ $clientItem->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                <select x-model="status" name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>{{ __('Partial') }}</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('From Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateFrom"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('To Date') }}</label>
                <input 
                    type="date" 
                    x-model="dateTo"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <form method="GET" action="{{ route('financials.index') }}" class="flex-1">
                <input type="hidden" name="search" :value="search">
                <input type="hidden" name="project_id" :value="project">
                <input type="hidden" name="client_id" :value="client">
                <input type="hidden" name="status" :value="status">
                <input type="hidden" name="date_from" :value="dateFrom">
                <input type="hidden" name="date_to" :value="dateTo">
                <button type="submit" class="w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                    <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Apply Filters') }}
                </button>
            </form>
            <a href="{{ route('financials.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </div>
    </div>
</div>

<!-- Invoices Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <!-- Bulk Actions -->
    <div x-show="selectedInvoices.length > 0" class="mb-4 p-3 bg-primary-400/20 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <span class="text-white text-sm" x-text="bulkSelectedLabel()"></span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" class="flex-1 sm:flex-none px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded text-sm">
                <i class="fas fa-file-export {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                <span class="hidden sm:inline">{{ __('Export') }}</span>
            </button>
            <button type="button" class="flex-1 sm:flex-none px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                <span class="hidden sm:inline">{{ __('Delete') }}</span>
            </button>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto -mx-4 md:mx-0">
        <div class="inline-block min-w-full align-middle">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="text-start border-b border-white/10">
                        <th class="pb-3 px-2">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400" @change="toggleAllInvoices($event)">
                        </th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Invoice Number') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Client') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Project') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Issue date') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Due Date') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Total Amount') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Paid Amount') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Status') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Payment Method') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                            <td class="py-3 px-2">
                                <input type="checkbox" value="{{ $invoice->id }}" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400" @change="toggleInvoice($event)" :checked="selectedInvoices.includes({{ $invoice->id }})">
                            </td>
                            <td class="py-3 px-2 text-white text-sm font-semibold whitespace-nowrap">{{ $invoice->number }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $invoice->client->name ?? __('Not specified') }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $invoice->project->name ?? __('Not specified') }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $invoice->due_date->format('Y-m-d') }}</td>
                            <td class="py-3 px-2 text-white font-semibold whitespace-nowrap">{{ number_format($invoice->total_amount, 2) }} {{ __('Currency SAR') }}</td>
                            <td class="py-3 px-2 text-white font-semibold whitespace-nowrap">{{ number_format($invoice->paid_amount, 2) }} {{ __('Currency SAR') }}</td>
                            <td class="py-3 px-2 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($invoice->status->value === 'paid') bg-green-500/20 text-green-400
                                    @elseif($invoice->status->value === 'partial') bg-yellow-500/20 text-yellow-400
                                    @elseif($invoice->status->value === 'unpaid') bg-red-500/20 text-red-400
                                    @elseif($invoice->status->value === 'overdue') bg-orange-500/20 text-orange-400
                                    @endif">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $invoice->payment_method_label ?? __('Not specified') }}</td>
                            <td class="py-3 px-2 whitespace-nowrap">
                                <div class="flex items-center gap-1 md:gap-2 flex-wrap">
                                    <a href="{{ route('financials.show', $invoice->id) }}" class="text-primary-400 hover:text-primary-300 p-1" title="{{ __('View') }}">
                                        <i class="fas fa-eye text-sm md:text-base"></i>
                                    </a>
                                    @can('update', $invoice)
                                    <a href="{{ route('financials.edit', $invoice->id) }}" class="text-blue-400 hover:text-blue-300 p-1" title="{{ __('Edit') }}">
                                        <i class="fas fa-pen text-sm md:text-base"></i>
                                    </a>
                                    @endcan
                                    <a href="{{ route('financials.pdf', $invoice->id) }}" target="_blank" class="text-red-400 hover:text-red-300 p-1" title="{{ __('Generate PDF') }}">
                                        <i class="fas fa-file-pdf text-sm md:text-base"></i>
                                    </a>
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId: {{ $invoice->id }} } }))" class="text-green-400 hover:text-green-300 p-1" title="{{ __('Add payment') }}">
                                        <i class="fas fa-dollar-sign text-sm md:text-base"></i>
                                    </button>
                                    @can('delete', $invoice)
                                    <form action="{{ route('financials.destroy', $invoice->id) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('Confirm delete invoice')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash text-sm md:text-base"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-gray-400">{{ __('No invoices') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @forelse($invoices as $invoice)
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1">{{ $invoice->number }}</h3>
                        <p class="text-gray-400 text-sm">{{ $invoice->client->name ?? __('Not specified') }}</p>
                    </div>
                    <input type="checkbox" value="{{ $invoice->id }}" class="rounded border-white/20 bg-white/5 text-primary-400" @change="toggleInvoice($event)" :checked="selectedInvoices.includes({{ $invoice->id }})">
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Project') }}</span>
                        <span class="text-white text-sm">{{ $invoice->project->name ?? __('Not specified') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Issue date') }}</span>
                        <span class="text-white text-sm">{{ $invoice->issue_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Total Amount') }}</span>
                        <span class="text-white font-semibold">{{ number_format($invoice->total_amount, 2) }} {{ __('Currency SAR') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Paid Amount') }}</span>
                        <span class="text-green-400 font-semibold">{{ number_format($invoice->paid_amount, 2) }} {{ __('Currency SAR') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Status') }}</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($invoice->status->value === 'paid') bg-green-500/20 text-green-400
                            @elseif($invoice->status->value === 'partial') bg-yellow-500/20 text-yellow-400
                            @elseif($invoice->status->value === 'unpaid') bg-red-500/20 text-red-400
                            @elseif($invoice->status->value === 'overdue') bg-orange-500/20 text-orange-400
                            @endif">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2 justify-center sm:justify-start">
                        <a href="{{ route('financials.show', $invoice->id) }}" class="text-primary-400 hover:text-primary-300 p-2" title="{{ __('View') }}">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $invoice)
                        <a href="{{ route('financials.edit', $invoice->id) }}" class="text-blue-400 hover:text-blue-300 p-2" title="{{ __('Edit') }}">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        <a href="{{ route('financials.pdf', $invoice->id) }}" target="_blank" class="text-red-400 hover:text-red-300 p-2" title="{{ __('Generate PDF') }}">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: { invoiceId: {{ $invoice->id }} } }))" class="w-full sm:w-auto px-3 py-2 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        <i class="fas fa-dollar-sign {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('Add payment') }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">{{ __('No invoices') }}</div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($invoices->hasPages())
    <div class="mt-6 overflow-x-auto">
        <div class="flex justify-center">
            {{ $invoices->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Payment Modal -->
@include('components.modals.payment-modal')

</div>

@push('scripts')
<script>
function financialsPage() {
    return {
        search: @json(request('search', '')),
        project: @json((string) request('project_id', '')),
        client: @json((string) request('client_id', '')),
        status: @json(request('status', '')),
        dateFrom: @json(request('date_from', '')),
        dateTo: @json(request('date_to', '')),
        selectedInvoices: [],
        bulkSelectedTpl: @json(__('Selected invoices count label')),

        bulkSelectedLabel() {
            return this.bulkSelectedTpl.replace(':count', String(this.selectedInvoices.length));
        },

        toggleInvoice(event) {
            const id = parseInt(event.target.value, 10);
            if (Number.isNaN(id)) return;
            if (event.target.checked) {
                if (!this.selectedInvoices.includes(id)) {
                    this.selectedInvoices.push(id);
                }
            } else {
                this.selectedInvoices = this.selectedInvoices.filter((i) => i !== id);
            }
        },

        toggleAllInvoices(event) {
            const checked = event.target.checked;
            const ids = @json($invoices->pluck('id')->values()->all());
            if (checked) {
                this.selectedInvoices = [...ids];
                this.$el.querySelectorAll('tbody input[type=checkbox]').forEach((cb) => { cb.checked = true; });
            } else {
                this.selectedInvoices = [];
                this.$el.querySelectorAll('tbody input[type=checkbox]').forEach((cb) => { cb.checked = false; });
            }
        },
    };
}
</script>
@endpush

@endsection
