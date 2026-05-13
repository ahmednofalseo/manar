<div class="space-y-4 md:space-y-6">
    <!-- Financial Summary -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <div>
                <p class="text-gray-400 text-sm mb-2">{{ __('Value') }}</p>
                <p class="text-2xl font-bold text-white">{{ number_format($project->value, 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">{{ __('Collected amount') }}</p>
                <p class="text-2xl font-bold text-green-400">{{ number_format($projectFinancials['collected'], 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
                @if($projectFinancials['collected'] <= 0)
                    <p class="text-gray-400 text-xs mt-1">{{ __('Financials will link to invoicing') }}</p>
                @endif
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-2">{{ __('Remaining') }}</p>
                <p class="text-2xl font-bold text-red-400">{{ number_format($projectFinancials['remaining'], 2) }} <span class="text-lg text-gray-400">{{ __('Currency SAR') }}</span></p>
            </div>
        </div>
        <div class="w-full bg-white/5 rounded-full h-3">
            <div class="bg-primary-400 h-3 rounded-full transition-all" style="width: {{ $projectFinancials['progress_percent'] }}%"></div>
        </div>
    </div>

    <!-- Payments Timeline -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">{{ __('Payments timeline') }}</h2>
            <a href="{{ route('financials.index') }}?project_id={{ $project->id }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                {{ __('Manage invoices') }}
            </a>
        </div>

        @if($projectInvoices->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-file-invoice-dollar text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('No project invoices') }}</h3>
                <p class="text-gray-400 mb-4">{{ __('Create invoices from financials hint') }}</p>
                <a href="{{ route('financials.create', ['project_id' => $project->id]) }}" class="inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('Create invoice') }}
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($projectInvoices as $inv)
                    @php
                        $invClient = $inv->client?->display_name ?? $inv->client?->name ?? '—';
                        $invRemaining = max(0, (float) $inv->total_amount - (float) $inv->paid_amount);
                    @endphp
                    <a href="{{ route('financials.show', $inv->id) }}" class="block p-4 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-white font-semibold">{{ $inv->number }}</p>
                                <p class="text-gray-400 text-sm">{{ $invClient }} · {{ $inv->issue_date?->format('Y-m-d') ?? '—' }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="text-white font-semibold">{{ number_format((float) $inv->paid_amount, 2) }} / {{ number_format((float) $inv->total_amount, 2) }} {{ __('Currency SAR') }}</p>
                                <p class="text-gray-400 text-xs">{{ __('Remaining') }}: {{ number_format($invRemaining, 2) }} {{ __('Currency SAR') }} · {{ $inv->status_label }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6 text-center">
                <a href="{{ route('financials.create', ['project_id' => $project->id]) }}" class="inline-block px-6 py-3 bg-primary-500/80 hover:bg-primary-600 text-white rounded-lg text-sm transition-all duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    {{ __('Create invoice') }}
                </a>
            </div>
        @endif
    </div>
</div>
