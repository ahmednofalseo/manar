@extends('layouts.dashboard')

@section('title', __('New Invoice') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('New Invoice'))

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
@php
    $invoicePaymentMethods = [
        'cash' => __('Payment method cash'),
        'transfer' => __('Payment method bank transfer'),
        'check' => __('Payment method check'),
        'electronic' => __('Payment method electronic'),
    ];
@endphp
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('New Invoice') }}</h1>
    <a href="{{ route('financials.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('financials.store') }}" enctype="multipart/form-data" x-data="invoiceForm()">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Client') }}</label>
                <select name="client_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Client Optional') }}</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} - {{ $client->type_label }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Project') }} <span class="text-red-400">*</span></label>
                <select name="project_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Project') }}</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->display_name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Invoice Number') }}</label>
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="number"
                        value="{{ old('number') }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="{{ __('Will be generated automatically') }}"
                    >
                </div>
                <p class="text-gray-400 text-xs mt-1">{{ __('Invoice number auto hint') }}</p>
                @error('number')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Invoice Total') }} <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input
                        type="number"
                        name="total_amount"
                        value="{{ old('total_amount') }}"
                        required
                        step="0.01"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pe-16 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    <span class="absolute end-4 top-1/2 transform -translate-y-1/2 text-gray-400">{{ __('Currency SAR') }}</span>
                </div>
                @error('total_amount')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Installments count') }}</label>
                <input
                    type="number"
                    name="installments_count"
                    value="{{ old('installments_count', 1) }}"
                    min="1"
                    max="100"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                <p class="text-gray-400 text-xs mt-1">{{ __('Installments auto hint') }}</p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Payment Method') }}</label>
                <select name="payment_method" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    @foreach ($invoicePaymentMethods as $pmValue => $pmLabel)
                        <option value="{{ $pmValue }}" {{ old('payment_method', 'cash') == $pmValue ? 'selected' : '' }}>{{ $pmLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Issue Date') }} <span class="text-red-400">*</span></label>
                <input
                    type="date"
                    name="issue_date"
                    value="{{ old('issue_date', date('Y-m-d')) }}"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('issue_date')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Due Date') }} <span class="text-red-400">*</span></label>
                <input
                    type="date"
                    name="due_date"
                    value="{{ old('due_date') }}"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('due_date')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Third Party -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">{{ __('Third party optional heading') }}</h2>
            <button type="button" @click="addThirdParty()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded-lg text-sm">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}"></i>
                {{ __('Add') }}
            </button>
        </div>
        <div class="space-y-4" x-show="thirdParties.length > 0">
            <template x-for="(thirdParty, index) in thirdParties" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white/5 rounded-lg p-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Name') }}</label>
                        <input
                            type="text"
                            x-model="thirdParty.name"
                            :name="'third_party[' + index + '][name]'"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Amount') }}</label>
                        <input
                            type="number"
                            x-model="thirdParty.amount"
                            :name="'third_party[' + index + '][amount]'"
                            step="0.01"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">{{ __('Date') }}</label>
                        <div class="flex items-center gap-2">
                            <input
                                type="date"
                                x-model="thirdParty.date"
                                :name="'third_party[' + index + '][date]'"
                                class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            >
                            <button type="button" @click="removeThirdParty(index)" class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <p x-show="thirdParties.length === 0" class="text-gray-400 text-sm text-center">{{ __('No third party rows') }}</p>
    </div>

    <!-- Additional Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Additional Information') }}</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Notes') }}</label>
                <textarea
                    name="notes"
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Invoice notes placeholder') }}"
                >{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Upload invoice PDF optional') }}</label>
                <div class="flex items-center gap-4">
                    <input
                        type="file"
                        name="pdf_file"
                        accept=".pdf"
                        class="hidden"
                        id="pdfFile"
                        @change="handleFileSelect($event)"
                    >
                    <label for="pdfFile" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200">
                        <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Choose file') }}
                    </label>
                    <span x-show="selectedFile" class="text-gray-300 text-sm" x-text="selectedFile"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('financials.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Save') }}
        </button>
    </div>
</form>

@push('scripts')
<script>
function invoiceForm() {
    return {
        thirdParties: [],
        selectedFile: null,
        addThirdParty() {
            this.thirdParties.push({
                name: '',
                amount: '',
                date: ''
            });
        },
        removeThirdParty(index) {
            this.thirdParties.splice(index, 1);
        },
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file.name;
            }
        }
    };
}
</script>
@endpush

@endsection
