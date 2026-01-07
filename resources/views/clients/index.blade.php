@extends('layouts.dashboard')

@section('title', __('Client Management') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Client Management'))

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Client Management') }}</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
            <i class="fas fa-user-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('New Client') }}
        </a>
    </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Clients') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-white mt-1 md:mt-2" style="color: #4787a7 !important;">{{ $totalClients }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-primary-400/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-primary-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Active Clients') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-green-400 mt-1 md:mt-2">{{ $activeClients }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-check text-green-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Inactive Clients') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-gray-400 mt-1 md:mt-2">{{ $inactiveClients }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-slash text-gray-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">{{ __('Total Projects') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold text-yellow-400 mt-1 md:mt-2" style="color: #4787a7 !important;">{{ \App\Models\Project::whereHas('client')->count() }}</h3>
            </div>
            <div class="w-12 h-12 md:w-16 md:h-16 bg-yellow-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-project-diagram text-yellow-400 text-xl md:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-4 md:mb-6" x-data="chartsData()" x-init="initCharts()">
    <!-- New Clients Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('New Clients Monthly') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="newClientsChart"></canvas>
        </div>
    </div>

    <!-- Clients by Type Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Clients Distribution by Type') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="clientsByTypeChart"></canvas>
        </div>
    </div>

    <!-- Most Active Clients Chart -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
        <h3 class="text-lg font-bold text-white mb-4">{{ __('Most Active Clients') }}</h3>
        <div class="relative" style="height: 250px;">
            <canvas id="mostActiveClientsChart"></canvas>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<form method="GET" action="{{ route('clients.index') }}" class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
    <div class="space-y-4">
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('Search: name, phone, email...') }}" 
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 pr-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base"
            >
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('City') }}</label>
                <select name="city" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Cities') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-xs md:text-sm mb-2">{{ __('Client Type') }}</label>
                <select name="type" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 md:px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40 text-sm md:text-base">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>{{ __('Individual') }}</option>
                    <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>{{ __('Company') }}</option>
                    <option value="government" {{ request('type') == 'government' ? 'selected' : '' }}>{{ __('Government Entity') }}</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 text-sm md:text-base">
                <i class="fas fa-filter {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Apply Filters') }}
            </button>
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200 text-sm md:text-base text-center">
                <i class="fas fa-times {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                {{ __('Clear') }}
            </a>
        </div>
    </div>
</form>

<!-- Clients Table -->
<div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6" x-data="clientsTableData()">
    @if($clients->count() > 0)
    <!-- Bulk Actions Bar -->
    <div x-show="selectedClients.length > 0" class="mb-4 p-3 bg-primary-500/20 border border-primary-400/30 rounded-lg flex items-center justify-between">
        <span class="text-white text-sm">
            {{ __('clients selected') }} <span x-text="selectedClients.length" class="font-bold"></span>
        </span>
        <div class="flex items-center gap-2">
            @can('deleteAny', \App\Models\Client::class)
            <button @click="bulkDelete()" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded text-sm">
                <i class="fas fa-trash {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Delete Selected') }}
            </button>
            @endcan
            <button @click="clearSelection()" class="px-3 py-1 bg-white/5 hover:bg-white/10 text-white rounded text-sm">
                {{ __('Cancel') }}
            </button>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-right border-b border-white/10">
                    <th class="text-gray-400 text-sm font-normal pb-3 w-12">
                        <input type="checkbox" @change="toggleAll($event)" class="rounded border-white/20">
                    </th>
                    <th x-show="isVisible('name')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Full Name') }}</th>
                    <th x-show="isVisible('type')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Client Type') }}</th>
                    <th x-show="isVisible('phone')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Phone') }}</th>
                    <th x-show="isVisible('email')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Email') }}</th>
                    <th x-show="isVisible('city')" class="text-gray-400 text-sm font-normal pb-3">{{ __('City / District') }}</th>
                    <th x-show="isVisible('projects')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Projects Count') }}</th>
                    <th x-show="isVisible('status')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Status') }}</th>
                    <th x-show="isVisible('created_at')" class="text-gray-400 text-sm font-normal pb-3">{{ __('Created At') }}</th>
                    <th class="text-gray-400 text-sm font-normal pb-3">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition-all" :class="selectedClients.includes({{ $client->id }}) ? 'bg-primary-500/10' : ''">
                        <td class="py-3">
                            <input type="checkbox" :value="{{ $client->id }}" @change="toggleClient({{ $client->id }}, $event)" class="rounded border-white/20">
                        </td>
                        <td x-show="isVisible('name')" class="py-3 text-white text-sm font-semibold">{{ $client->name }}</td>
                        <td x-show="isVisible('type')" class="py-3 text-gray-300 text-sm">{{ $client->type_label }}</td>
                        <td x-show="isVisible('phone')" class="py-3 text-gray-300 text-sm">{{ $client->phone }}</td>
                        <td x-show="isVisible('email')" class="py-3 text-gray-300 text-sm">{{ $client->email ?? '-' }}</td>
                        <td x-show="isVisible('city')" class="py-3 text-gray-300 text-sm">{{ $client->city }}@if($client->district) / {{ $client->district }}@endif</td>
                        <td x-show="isVisible('projects')" class="py-3 text-white font-semibold" style="color: #4787a7 !important;">{{ $client->projects_count }}</td>
                        <td x-show="isVisible('status')" class="py-3">
                            <span 
                                class="px-2 py-1 rounded text-xs font-semibold {{ $client->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}"
                            >
                                {{ $client->status_label }}
                            </span>
                        </td>
                        <td x-show="isVisible('created_at')" class="py-3 text-gray-300 text-sm">{{ $client->created_at->format('Y-m-d') }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('clients.show', $client->id) }}" class="text-primary-400 hover:text-primary-300" :title="'{{ __('View') }}'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $client)
                                <a href="{{ route('clients.edit', $client->id) }}" class="text-blue-400 hover:text-blue-300" :title="'{{ __('Edit') }}'">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @endcan
                                @can('viewAttachments', $client)
                                <button onclick="openClientAttachmentModal({{ $client->id }})" class="text-purple-400 hover:text-purple-300" :title="'{{ __('Attachments') }}'">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                @endcan
                                @can('addNote', $client)
                                <button onclick="openClientNotesModal({{ $client->id }})" class="text-yellow-400 hover:text-yellow-300" :title="'{{ __('Notes') }}'">
                                    <i class="fas fa-comments"></i>
                                </button>
                                @endcan
                                @can('delete', $client)
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this client?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300" :title="'{{ __('Delete') }}'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        @foreach($clients as $client)
            <div class="glass-card rounded-xl p-4 border border-white/10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="text-white font-semibold mb-1">{{ $client->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ $client->type_label }}</p>
                    </div>
                    <span 
                        class="px-2 py-1 rounded text-xs font-semibold {{ $client->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}"
                    >
                        {{ $client->status_label }}
                    </span>
                </div>
                <div class="space-y-2 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Phone') }}</span>
                        <span class="text-white text-sm">{{ $client->phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Email') }}</span>
                        <span class="text-white text-sm">{{ $client->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('City') }}</span>
                        <span class="text-white text-sm">{{ $client->city }}@if($client->district) / {{ $client->district }}@endif</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-xs">{{ __('Projects') }}</span>
                        <span class="text-white font-semibold" style="color: #4787a7 !important;">{{ $client->projects_count }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-3 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('clients.show', $client->id) }}" class="text-primary-400 hover:text-primary-300">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('update', $client)
                        <a href="{{ route('clients.edit', $client->id) }}" class="text-blue-400 hover:text-blue-300">
                            <i class="fas fa-pen"></i>
                        </a>
                        @endcan
                        @can('viewAttachments', $client)
                        <button onclick="openClientAttachmentModal({{ $client->id }})" class="text-purple-400 hover:text-purple-300">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        @endcan
                    </div>
                    @can('addNote', $client)
                    <button onclick="openClientNotesModal({{ $client->id }})" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded text-sm">
                        <i class="fas fa-comments {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                        {{ __('Notes') }}
                    </button>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="mt-6">
        {{ $clients->links() }}
    </div>
    @endif

    @else
    <div class="text-center py-12">
        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-400 text-lg">{{ __('No clients found') }}</p>
        @can('create', \App\Models\Client::class)
        <a href="{{ route('clients.create') }}" class="mt-4 inline-block px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-user-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Add New Client') }}
        </a>
        @endcan
    </div>
    @endif
</div>

<!-- Modals -->
@include('components.modals.client-attachment')
@include('components.modals.client-notes')


@push('scripts')
<script>
function handleImport(input) {
    if (!input.files || !input.files[0]) return;
    
    const formData = new FormData();
    formData.append('file', input.files[0]);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
    
    const button = input.previousElementSibling;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الاستيراد...';
    
    fetch('{{ route("clients.import") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success !== false) {
            showToast('success', data.message || 'تم الاستيراد بنجاح');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('error', data.message || 'حدث خطأ أثناء الاستيراد');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'حدث خطأ أثناء الاستيراد');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
        input.value = '';
    });
}

function bulkActions() {
    return {
        selectedClients: [],
        toggleClient(id, event) {
            if (event.target.checked) {
                if (!this.selectedClients.includes(id)) {
                    this.selectedClients.push(id);
                }
            } else {
                this.selectedClients = this.selectedClients.filter(c => c !== id);
            }
        },
        toggleAll(event) {
            if (event.target.checked) {
                this.selectedClients = @json($clients->pluck('id')->toArray());
                document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = true);
            } else {
                this.selectedClients = [];
                document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = false);
            }
        },
        clearSelection() {
            this.selectedClients = [];
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        },
        async bulkDelete() {
            if (!confirm(`{{ __('Are you sure you want to delete') }} ${this.selectedClients.length} {{ __('clients?') }}`)) return;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("clients.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ ids: this.selectedClients })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success !== false) {
                    showToast('success', result.message || 'تم الحذف بنجاح');
                    this.clearSelection();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('error', result.message || 'حدث خطأ أثناء الحذف');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'حدث خطأ أثناء الحذف');
            }
        }
    }
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md text-white animate-slide-in ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="mr-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Global functions for opening modals
function openClientAttachmentModal(clientId) {
    window.dispatchEvent(new CustomEvent('open-client-attachment-modal', { 
        detail: { clientId: clientId } 
    }));
}

function openClientNotesModal(clientId) {
    window.dispatchEvent(new CustomEvent('open-client-notes-modal', { 
        detail: { clientId: clientId } 
    }));
}

function columnVisibility() {
    return {
        columns: {
            name: true,
            type: true,
            phone: true,
            email: true,
            city: true,
            projects: true,
            status: true,
            created_at: true
        },
        init() {
            // Load saved column visibility from localStorage
            const saved = localStorage.getItem('clients_display_settings');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (parsed.columns) {
                        this.columns = { ...this.columns, ...parsed.columns };
                    }
                } catch (e) {
                    console.error('Error loading column settings:', e);
                }
            }
        },
        isVisible(column) {
            return this.columns[column] !== false;
        }
    }
}

function clientsTableData() {
    return {
        ...bulkActions(),
        ...columnVisibility()
    }
}

function clientFilters() {
    return {
        search: '',
        city: '',
        status: '',
        type: '',
        applyFilters() {
            console.log('Applying filters:', this);
        },
        clearFilters() {
            this.search = '';
            this.city = '';
            this.status = '';
            this.type = '';
        }
    }
}

function clientsData() {
    return {
        clients: [
            {
                id: 1,
                name: 'أحمد محمد العلي',
                type: 'individual',
                phone: '0501234567',
                email: 'ahmed@example.com',
                city: 'الرياض',
                district: 'العليا',
                projectsCount: 5,
                status: 'active',
                createdAt: '2025-01-15'
            },
            {
                id: 2,
                name: 'شركة البناء المتقدم',
                type: 'company',
                phone: '0112345678',
                email: 'info@company.com',
                city: 'جدة',
                district: 'الكورنيش',
                projectsCount: 12,
                status: 'active',
                createdAt: '2024-11-20'
            },
            {
                id: 3,
                name: 'وزارة الشؤون البلدية',
                type: 'government',
                phone: '0119876543',
                email: 'contact@municipality.gov.sa',
                city: 'الرياض',
                district: 'الملك فهد',
                projectsCount: 8,
                status: 'active',
                createdAt: '2024-09-10'
            },
            {
                id: 4,
                name: 'فاطمة سالم',
                type: 'individual',
                phone: '0509876543',
                email: 'fatima@example.com',
                city: 'الدمام',
                district: 'الكورنيش',
                projectsCount: 2,
                status: 'inactive',
                createdAt: '2025-03-05'
            }
        ],
        getClientTypeText(type) {
            const typeMap = {
                'individual': 'فرد',
                'company': 'شركة',
                'government': 'جهة حكومية'
            };
            return typeMap[type] || type;
        },
        openAttachmentModal(id) {
            window.dispatchEvent(new CustomEvent('open-client-attachment-modal', { detail: { clientId: id } }));
        },
        openNotesModal(id) {
            window.dispatchEvent(new CustomEvent('open-client-notes-modal', { detail: { clientId: id } }));
        },
        linkProject(id) {
            alert('{{ __('Link New Project') }}');
        },
        deleteClient(id) {
            if (confirm('{{ __('Are you sure you want to delete this client?') }}')) {
                console.log('Deleting client:', id);
            }
        }
    }
}

function chartsData() {
    return {
        newClientsChart: null,
        clientsByTypeChart: null,
        mostActiveChart: null,
        initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initCharts(), 100);
                return;
            }

            // بيانات العملاء الجدد شهريًا
            const newClientsData = @json($newClientsByMonth ?? []);
            const months = [];
            const counts = [];
            
            // إنشاء قائمة الأشهر للـ 12 شهر الماضية
            for (let i = 11; i >= 0; i--) {
                const date = new Date();
                date.setMonth(date.getMonth() - i);
                const monthKey = date.toISOString().slice(0, 7); // YYYY-MM
                const monthName = date.toLocaleDateString('ar-SA', { month: 'long', year: 'numeric' });
                months.push(monthName);
                counts.push(newClientsData[monthKey] || 0);
            }

            // New Clients Line Chart
            const newCtx = document.getElementById('newClientsChart');
            if (newCtx && !this.newClientsChart) {
                this.newClientsChart = new Chart(newCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: '{{ __('New Clients Monthly') }}',
                            data: counts,
                            borderColor: '#4787a7',
                            backgroundColor: 'rgba(71, 135, 167, 0.1)',
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

            // بيانات توزيع العملاء حسب النوع
            const clientsByTypeData = @json($clientsByType ?? []);
            const typeLabels = {
                'individual': '{{ __('Individual') }}',
                'company': '{{ __('Company') }}',
                'government': '{{ __('Government Entity') }}'
            };
            const typeLabelsArray = Object.keys(clientsByTypeData).map(key => typeLabels[key] || key);
            const typeDataArray = Object.values(clientsByTypeData);

            // Clients by Type Pie Chart
            const typeCtx = document.getElementById('clientsByTypeChart');
            if (typeCtx && !this.clientsByTypeChart) {
                this.clientsByTypeChart = new Chart(typeCtx, {
                    type: 'pie',
                    data: {
                        labels: typeLabelsArray.length > 0 ? typeLabelsArray : ['لا توجد بيانات'],
                        datasets: [{
                            data: typeDataArray.length > 0 ? typeDataArray : [0],
                            backgroundColor: ['rgba(71, 135, 167, 0.8)', 'rgba(29, 184, 248, 0.8)', 'rgba(16, 185, 129, 0.8)'],
                            borderColor: ['#4787a7', '#1db8f8', '#10b981'],
                            borderWidth: 2
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

            // بيانات العملاء الأكثر نشاطًا
            const mostActiveClientsData = @json($mostActiveClients ?? []);
            const activeClientNames = mostActiveClientsData.map(client => client.name);
            const activeClientProjects = mostActiveClientsData.map(client => client.projects_count || 0);

            // Most Active Clients Bar Chart
            const activeCtx = document.getElementById('mostActiveClientsChart');
            if (activeCtx && !this.mostActiveChart) {
                this.mostActiveChart = new Chart(activeCtx, {
                    type: 'bar',
                    data: {
                        labels: activeClientNames.length > 0 ? activeClientNames : ['لا توجد بيانات'],
                        datasets: [{
                            label: '{{ __('Projects Count') }}',
                            data: activeClientProjects.length > 0 ? activeClientProjects : [0],
                            backgroundColor: 'rgba(71, 135, 167, 0.8)',
                            borderColor: '#4787a7',
                            borderWidth: 1
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
        }
    }
}
</script>
@endpush

@endsection



