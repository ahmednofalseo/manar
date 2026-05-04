@extends('layouts.dashboard')

@section('title', __('New Expense') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('New Expense'))

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
    $expenseDepartments = [
        'إدارة' => __('Expense Dept Administration'),
        'مشاريع' => __('Expense Dept Projects'),
        'مالية' => __('Expense Dept Finance'),
        'مبيعات' => __('Expense Dept Sales'),
        'تسويق' => __('Expense Dept Marketing'),
    ];
    $expenseTypes = [
        'رواتب' => __('Expense Type Salaries'),
        'إيجار' => __('Expense Type Rent'),
        'كهرباء' => __('Expense Type Electricity'),
        'مياه' => __('Expense Type Water'),
        'صيانة' => __('Expense Type Maintenance'),
        'معدات' => __('Expense Type Equipment'),
        'نقل' => __('Expense Type Transport'),
        'أخرى' => __('Expense Type Other'),
    ];
    $expensePaymentMethods = [
        'نقدي' => __('Payment method cash'),
        'تحويل بنكي' => __('Payment method bank transfer'),
        'شيك' => __('Payment method check'),
        'إلكتروني' => __('Payment method electronic'),
    ];
@endphp
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('New Expense') }}</h1>
    <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
        {{ __('Back') }}
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" x-data="expenseForm(@js(['filesSelected' => __('Selected files count label'), 'sizeUnits' => [__('File size unit B'), __('File size unit KB'), __('File size unit MB'), __('File size unit GB')]]))">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Voucher Number') }}</label>
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="voucher_number"
                        value="{{ old('voucher_number', \App\Models\Expense::generateVoucherNumber()) }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        readonly
                    >
                </div>
                <p class="text-gray-400 text-xs mt-1">{{ __('Voucher number auto hint') }}</p>
                @error('voucher_number')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Date') }} <span class="text-red-400">*</span></label>
                <input
                    type="date"
                    name="date"
                    required
                    value="{{ old('date', date('Y-m-d')) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('date')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Department') }} <span class="text-red-400">*</span></label>
                <select name="department" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Department') }}</option>
                    @foreach ($expenseDepartments as $deptValue => $deptLabel)
                        <option value="{{ $deptValue }}" {{ old('department') == $deptValue ? 'selected' : '' }}>{{ $deptLabel }}</option>
                    @endforeach
                </select>
                @error('department')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Expense Type') }} <span class="text-red-400">*</span></label>
                <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Type') }}</option>
                    @foreach ($expenseTypes as $typeValue => $typeLabel)
                        <option value="{{ $typeValue }}" {{ old('type') == $typeValue ? 'selected' : '' }}>{{ $typeLabel }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">{{ __('Description') }} <span class="text-red-400">*</span></label>
                <textarea
                    name="description"
                    required
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="{{ __('Expense description placeholder') }}"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Amount') }} <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input
                        type="number"
                        name="amount"
                        required
                        step="0.01"
                        value="{{ old('amount') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pe-16 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    <span class="absolute end-4 top-1/2 transform -translate-y-1/2 text-gray-400">{{ __('Currency SAR') }}</span>
                </div>
                @error('amount')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Payment Method') }} <span class="text-red-400">*</span></label>
                <select name="payment_method" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select payment method') }}</option>
                    @foreach ($expensePaymentMethods as $pmValue => $pmLabel)
                        <option value="{{ $pmValue }}" {{ old('payment_method') == $pmValue ? 'selected' : '' }}>{{ $pmLabel }}</option>
                    @endforeach
                </select>
                @error('payment_method')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('Pending Approval') }}</option>
                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Attachments') }}</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">{{ __('Upload attachments optional') }}</label>
                <div class="flex items-center gap-4">
                    <input
                        type="file"
                        name="attachments[]"
                        multiple
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="hidden"
                        id="attachmentsInput"
                        @change="handleFilesSelect($event)"
                    >
                    <label for="attachmentsInput" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg cursor-pointer transition-all duration-200">
                        <i class="fas fa-upload {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ __('Select Files') }}
                    </label>
                    <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="filesSelectedText"></span>
                </div>
                <p class="text-gray-400 text-xs mt-1">{{ __('Attachments formats hint') }}</p>
            </div>

            <!-- Selected Files List -->
            <div x-show="selectedFiles.length > 0" class="space-y-2">
                <template x-for="(file, index) in selectedFiles" :key="index">
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file text-primary-400"></i>
                            <span class="text-white text-sm" x-text="file.name"></span>
                            <span class="text-gray-400 text-xs" x-text="formatFileSize(file.size)"></span>
                        </div>
                        <button type="button" @click="removeFile(index)" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Additional Notes') }}</h2>
        <div>
            <textarea
                name="notes"
                rows="4"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="{{ __('Expense additional notes placeholder') }}"
            >{{ old('notes') }}</textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
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
function expenseForm(i18n) {
    i18n = i18n || { filesSelected: ':count', sizeUnits: ['Bytes', 'KB', 'MB', 'GB'] };
    return {
        selectedFiles: [],
        filesSelectedTemplate: i18n.filesSelected,
        sizeUnits: i18n.sizeUnits || ['Bytes', 'KB', 'MB', 'GB'],
        get filesSelectedText() {
            if (this.selectedFiles.length === 0) {
                return '';
            }
            return String(this.filesSelectedTemplate).replace(':count', String(this.selectedFiles.length));
        },
        handleFilesSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files.map(file => ({
                name: file.name,
                size: file.size
            }));
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            const input = document.getElementById('attachmentsInput');
            if (input) {
                input.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) {
                return '0 ' + this.sizeUnits[0];
            }
            const k = 1024;
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + this.sizeUnits[i];
        }
    };
}
</script>
@endpush

@endsection
