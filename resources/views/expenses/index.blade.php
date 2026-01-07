@extends('layouts.dashboard')

@section('title', __('Expense Management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Expense Management'))

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
<div class="fixed top-4 left-4 right-4 sm:right-auto sm:left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-green-500 text-white animate-slide-in mx-auto sm:mx-0">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 left-4 right-4 sm:right-auto sm:left-4 z-50 p-4 rounded-lg shadow-lg max-w-md bg-red-500 text-white animate-slide-in mx-auto sm:mx-0">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 flex-1">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm sm:text-base">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" class="mr-2 flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 md:mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Expense Management') }}</h1>
    <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
        <i class="fas fa-plus-circle {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('New Expense') }}
    </a>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Expenses This Month') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2">{{ number_format($totalExpensesThisMonth, 2) }} <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Approved Expenses') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">{{ number_format($approvedExpenses, 2) }} <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-circle-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Rejected Expenses') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-red-400 mt-1 md:mt-2">{{ number_format($rejectedExpenses, 2) }} <span class="text-lg text-gray-400">ر.س</span></h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-red-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-ban text-red-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Pending Approval') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2">{{ number_format($pendingExpenses, 2) }} <span class="text-lg text-gray-400">ر.س</span></h3>
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
                name="search"
                value="{{ request('search', '') }}"
                placeholder="{{ __('Search: description, voucher number...') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 md:gap-4">
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

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Department') }}</label>
                <select x-model="department" name="department" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Departments') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Expense Type') }}</label>
                <select x-model="type" name="type" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $typeItem)
                        <option value="{{ $typeItem }}" {{ request('type') == $typeItem ? 'selected' : '' }}>{{ $typeItem }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                <select x-model="status" name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <form method="GET" action="{{ route('expenses.index') }}" class="flex-1">
                <input type="hidden" name="search" :value="search">
                <input type="hidden" name="date_from" :value="dateFrom">
                <input type="hidden" name="date_to" :value="dateTo">
                <input type="hidden" name="department" :value="department">
                <input type="hidden" name="type" :value="type">
                <input type="hidden" name="status" :value="status">
                <button type="submit" class="w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                    <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('Apply Filters') }}
                </button>
            </form>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </div>
    </div>
</div>

<!-- Expenses Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
    <!-- Bulk Actions -->
    <div x-data="{ selectedExpenses: [] }" x-show="selectedExpenses.length > 0" class="mb-4 p-3 bg-primary-400/20 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <span class="text-white text-sm" x-text="selectedExpenses.length + ' {{ __('expenses selected') }}'"></span>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none px-3 py-1 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded text-sm">
                <i class="fas fa-circle-check {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                <span class="hidden sm:inline">{{ __('Approve') }}</span>
            </button>
            <button class="flex-1 sm:flex-none px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-ban {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                <span class="hidden sm:inline">{{ __('Reject') }}</span>
            </button>
            <button class="flex-1 sm:flex-none px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                <span class="hidden sm:inline">{{ __('Delete') }}</span>
            </button>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto -mx-4 md:mx-0">
        <div class="inline-block min-w-full align-middle">
            <table class="w-full min-w-[1000px]">
                <thead>
                    <tr class="text-right border-b border-white/10">
                        <th class="pb-3 px-2">
                            <input type="checkbox" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                        </th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Voucher Number') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Date') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Department') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Expense Type') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2">{{ __('Description') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Amount') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Payment Method') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Status') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Approver') }}</th>
                        <th class="text-gray-400 text-sm font-normal pb-3 px-2 whitespace-nowrap">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                            <td class="py-3 px-2">
                                <input type="checkbox" value="{{ $expense->id }}" class="rounded border-white/20 bg-white/5 text-primary-400 focus:ring-primary-400">
                            </td>
                            <td class="py-3 px-2 text-white text-sm font-semibold whitespace-nowrap">{{ $expense->voucher_number }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $expense->date->format('Y-m-d') }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $expense->department }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $expense->type }}</td>
                            <td class="py-3 px-2 text-gray-300 text-sm max-w-xs truncate" title="{{ $expense->description }}">{{ Str::limit($expense->description, 50) }}</td>
                            <td class="py-3 px-2 text-white font-semibold whitespace-nowrap">{{ number_format($expense->amount, 2) }} ر.س</td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $expense->payment_method }}</td>
                            <td class="py-3 px-2 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($expense->status->value === 'approved') bg-green-500/20 text-green-400
                                    @elseif($expense->status->value === 'rejected') bg-red-500/20 text-red-400
                                    @elseif($expense->status->value === 'pending') bg-yellow-500/20 text-yellow-400
                                    @endif">
                                    {{ $expense->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-gray-300 text-sm whitespace-nowrap">{{ $expense->approver->name ?? '-' }}</td>
                            <td class="py-3 px-2 whitespace-nowrap">
                                <div class="flex items-center gap-1 md:gap-2 flex-wrap">
                                    <a href="{{ route('expenses.show', $expense->id) }}" class="text-primary-400 hover:text-primary-300 p-1" :title="'{{ __('View') }}'">
                                        <i class="fas fa-eye text-sm md:text-base"></i>
                                    </a>
                                    @can('update', $expense)
                                    <a href="{{ route('expenses.edit', $expense->id) }}" class="text-blue-400 hover:text-blue-300 p-1" :title="'{{ __('Edit') }}'">
                                        <i class="fas fa-pen text-sm md:text-base"></i>
                                    </a>
                                    @endcan
                                    @if($expense->has_attachments)
                                    <a href="{{ route('expenses.attachments.download', ['id' => $expense->id, 'attachmentId' => $expense->attachments->first()->id]) }}" class="text-purple-400 hover:text-purple-300 p-1" :title="'{{ __('Download Attachment') }}'">
                                        <i class="fas fa-file-pdf text-sm md:text-base"></i>
                                    </a>
                                    @endif
                                    @can('approve', $expense)
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'approve' } }))" class="text-green-400 hover:text-green-300 p-1" :title="'{{ __('Approve') }}'">
                                        <i class="fas fa-circle-check text-sm md:text-base"></i>
                                    </button>
                                    @endcan
                                    @can('reject', $expense)
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'reject' } }))" class="text-red-400 hover:text-red-300 p-1" :title="'{{ __('Reject') }}'">
                                        <i class="fas fa-ban text-sm md:text-base"></i>
                                    </button>
                                    @endcan
                                    @can('delete', $expense)
                                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this expense?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" :title="'{{ __('Delete') }}'">
                                            <i class="fas fa-trash text-sm md:text-base"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-gray-400">{{ __('No expenses found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @forelse($expenses as $expense)
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3 gap-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-semibold mb-1 truncate">{{ $expense->voucher_number }}</h3>
                        <p class="text-gray-400 text-sm">{{ $expense->date->format('Y-m-d') }}</p>
                    </div>
                    <input type="checkbox" value="{{ $expense->id }}" class="rounded border-white/20 bg-white/5 text-primary-400 flex-shrink-0">
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-400 text-xs flex-shrink-0">{{ __('Department') }}</span>
                        <span class="text-white text-sm text-left truncate">{{ $expense->department }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-400 text-xs flex-shrink-0">{{ __('Type') }}</span>
                        <span class="text-white text-sm text-left truncate">{{ $expense->type }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-400 text-xs flex-shrink-0">{{ __('Amount') }}</span>
                        <span class="text-white font-semibold whitespace-nowrap">{{ number_format($expense->amount, 2) }} ر.س</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-gray-400 text-xs flex-shrink-0">{{ __('Status') }}</span>
                        <span class="px-2 py-1 rounded text-xs font-semibold whitespace-nowrap
                            @if($expense->status->value === 'approved') bg-green-500/20 text-green-400
                            @elseif($expense->status->value === 'rejected') bg-red-500/20 text-red-400
                            @elseif($expense->status->value === 'pending') bg-yellow-500/20 text-yellow-400
                            @endif">
                            {{ $expense->status_label }}
                        </span>
                    </div>
                    @if($expense->description)
                    <div class="pt-2 border-t border-white/10">
                        <p class="text-gray-400 text-xs mb-1">{{ __('Description') }}:</p>
                        <p class="text-white text-sm line-clamp-2">{{ Str::limit($expense->description, 100) }}</p>
                    </div>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2 justify-center sm:justify-start flex-wrap">
                        <a href="{{ route('expenses.show', $expense->id) }}" class="text-primary-400 hover:text-primary-300 p-2" :title="'{{ __('View') }}'">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $expense)
                        <a href="{{ route('expenses.edit', $expense->id) }}" class="text-blue-400 hover:text-blue-300 p-2" :title="'{{ __('Edit') }}'">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        @if($expense->has_attachments)
                        <a href="{{ route('expenses.attachments.download', ['id' => $expense->id, 'attachmentId' => $expense->attachments->first()->id]) }}" class="text-purple-400 hover:text-purple-300 p-2" :title="'{{ __('Attachment') }}'">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 justify-center sm:justify-end flex-wrap">
                        @can('approve', $expense)
                        <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'approve' } }))" class="px-3 py-1.5 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded text-sm" :title="'{{ __('Approve') }}'">
                            <i class="fas fa-circle-check {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                            {{ __('Approve') }}
                        </button>
                        @endcan
                        @can('reject', $expense)
                        <button onclick="window.dispatchEvent(new CustomEvent('open-expense-approval', { detail: { expenseId: {{ $expense->id }}, action: 'reject' } }))" class="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm" :title="'{{ __('Reject') }}'">
                            <i class="fas fa-ban {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                            {{ __('Reject') }}
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8">{{ __('No expenses found') }}</div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
    <div class="mt-6 overflow-x-auto">
        <div class="flex justify-center">
            {{ $expenses->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Sidebar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mt-4 md:mt-6" x-data="chartsData()" x-init="initCharts()">
    <!-- Monthly Expenses Chart -->
    <div class="lg:col-span-2 glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Monthly Expenses') }}</h3>
        <div class="relative overflow-x-auto" style="height: 250px; min-height: 200px;">
            <canvas id="monthlyExpensesChart"></canvas>
        </div>
    </div>

    <!-- Expense Types Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Expense Distribution by Type') }}</h3>
        <div class="relative overflow-x-auto" style="height: 250px; min-height: 200px;">
            <canvas id="expenseTypesChart"></canvas>
        </div>
    </div>
</div>

@php
$monthNames = app()->getLocale() === 'ar' 
    ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
@endphp

@push('scripts')
<script>
const monthlyExpensesData = @json($monthlyExpenses);
const expensesByTypeData = @json($expensesByType);
const monthNames = @json($monthNames);
</script>
@endpush

<!-- Top 5 Expenses -->
@if($expensesByType->count() > 0)
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mt-4 md:mt-6">
    <h3 class="text-lg font-bold text-white mb-4">{{ __('Top 5 Most Frequent Expense Items') }}</h3>
    <div class="space-y-3">
        @foreach($expensesByType as $index => $item)
            @php
                $icons = ['fas fa-bolt', 'fas fa-tools', 'fas fa-truck', 'fas fa-building', 'fas fa-file-invoice'];
                $colors = ['primary-400', 'blue-400', 'green-400', 'purple-400', 'yellow-400'];
                $icon = $icons[$index % count($icons)] ?? 'fas fa-file';
                $color = $colors[$index % count($colors)] ?? 'primary-400';
            @endphp
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $color }}/20 rounded-lg flex items-center justify-center">
                        <i class="{{ $icon }} text-{{ $color }}"></i>
                    </div>
                    <div>
                        <p class="text-white font-semibold">{{ $item->type }}</p>
                        <p class="text-gray-400 text-xs">{{ $item->count }} {{ __('expenses') }}</p>
                    </div>
                </div>
                <span class="text-white font-semibold">{{ number_format($item->total, 2) }} ر.س</span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Approval Modal -->
@include('components.modals.expense-approval')

@push('scripts')
<script>
function expenseFilters() {
    return {
        search: '{{ request('search', '') }}',
        dateFrom: '{{ request('date_from', '') }}',
        dateTo: '{{ request('date_to', '') }}',
        department: '{{ request('department', '') }}',
        type: '{{ request('type', '') }}',
        status: '{{ request('status', '') }}',
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
                const months = monthNames;
                const monthlyData = [];
                for (let i = 1; i <= 12; i++) {
                    monthlyData.push(monthlyExpensesData[i] || 0);
                }
                
                this.monthlyChart = new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: '{{ __('Expenses') }}',
                            data: monthlyData,
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
            if (typesCtx && !this.typesChart && expensesByTypeData && expensesByTypeData.length > 0) {
                const labels = expensesByTypeData.map(item => item.type);
                const data = expensesByTypeData.map(item => parseFloat(item.total));
                const total = data.reduce((a, b) => a + b, 0);
                const percentages = data.map(val => total > 0 ? Math.round((val / total) * 100) : 0);
                
                this.typesChart = new Chart(typesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: percentages,
                            backgroundColor: [
                                '#1db8f8',
                                '#10b981',
                                '#f59e0b',
                                '#8b5cf6',
                                '#ef4444',
                                '#ec4899',
                                '#14b8a6'
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



