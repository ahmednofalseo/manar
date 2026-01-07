@extends('layouts.dashboard')

@section('title', __('Edit') . ' ' . __('Financials') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit') . ' ' . __('Financials'))

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
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit') }} {{ __('Financials') }}</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('financials.show', $id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('View') }} {{ __('Details') }}
        </a>
        <a href="{{ route('financials.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="{{ route('financials.update', $id) }}" enctype="multipart/form-data" x-data="invoiceForm()">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">العميل <span class="text-red-400">*</span></label>
                <select name="client_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر العميل</option>
                    <option value="1" {{ old('client_id', '1') == '1' ? 'selected' : '' }}>أحمد محمد</option>
                    <option value="2" {{ old('client_id') == '2' ? 'selected' : '' }}>فاطمة سالم</option>
                    <option value="3" {{ old('client_id') == '3' ? 'selected' : '' }}>خالد مطر</option>
                </select>
                @error('client_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">المشروع <span class="text-red-400">*</span></label>
                <select name="project_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Project') }}</option>
                    <option value="1" {{ old('project_id', '1') == '1' ? 'selected' : '' }}>مشروع فيلا رقم 1</option>
                    <option value="2" {{ old('project_id') == '2' ? 'selected' : '' }}>مشروع مجمع سكني</option>
                    <option value="3" {{ old('project_id') == '3' ? 'selected' : '' }}>مشروع مبنى تجاري</option>
                </select>
                @error('project_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم الفاتورة</label>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="invoice_number" 
                        value="{{ old('invoice_number', 'INV-2025-001') }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    >
                    <button type="button" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg text-sm">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <p class="text-gray-400 text-xs mt-1">سيتم توليد الرقم تلقائياً إذا تركت فارغاً</p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">قيمة الفاتورة <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input 
                        type="number" 
                        name="amount" 
                        required
                        step="0.01"
                        value="{{ old('amount', '50000.00') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-16 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">ر.س</span>
                </div>
                @error('amount')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">عدد الدفعات</label>
                <input 
                    type="number" 
                    name="installments" 
                    value="{{ old('installments', '1') }}"
                    min="1"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">طريقة الدفع</label>
                <select name="payment_method" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="cash" {{ old('payment_method', 'transfer') == 'cash' ? 'selected' : '' }}>نقدي</option>
                    <option value="transfer" {{ old('payment_method', 'transfer') == 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>شيك</option>
                    <option value="electronic" {{ old('payment_method') == 'electronic' ? 'selected' : '' }}>إلكتروني</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="unpaid" {{ old('status', 'partial') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                    <option value="partial" {{ old('status', 'partial') == 'partial' ? 'selected' : '' }}>جزئية</option>
                    <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الإصدار</label>
                <input 
                    type="date" 
                    name="issue_date" 
                    value="{{ old('issue_date', date('Y-m-d')) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الاستحقاق</label>
                <input 
                    type="date" 
                    name="due_date" 
                    value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>
        </div>
    </div>

    <!-- Third Party -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">طرف ثالث (اختياري)</h2>
            <button type="button" @click="addThirdParty()" class="px-3 py-1 bg-primary-400/20 hover:bg-primary-500/30 text-primary-400 rounded-lg text-sm">
                <i class="fas fa-plus ml-1"></i>
                إضافة
            </button>
        </div>
        <div class="space-y-4" x-show="thirdParties.length > 0">
            <template x-for="(thirdParty, index) in thirdParties" :key="index">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white/5 rounded-lg p-4">
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">الاسم</label>
                        <input 
                            type="text" 
                            x-model="thirdParty.name"
                            :name="'third_party[' + index + '][name]'"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">المبلغ</label>
                        <input 
                            type="number" 
                            x-model="thirdParty.amount"
                            :name="'third_party[' + index + '][amount]'"
                            step="0.01"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        >
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm mb-2">التاريخ</label>
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
        <p x-show="thirdParties.length === 0" class="text-gray-400 text-sm text-center">لا يوجد طرف ثالث</p>
    </div>

    <!-- Additional Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">معلومات إضافية</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">ملاحظات</label>
                <textarea 
                    name="notes" 
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="أي ملاحظات إضافية حول الفاتورة..."
                >{{ old('notes', '') }}</textarea>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">رفع ملف PDF (اختياري)</label>
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
                        <i class="fas fa-upload ml-2"></i>
                        اختر ملف
                    </label>
                    <span x-show="selectedFile" class="text-gray-300 text-sm" x-text="selectedFile"></span>
                    <span class="text-gray-400 text-xs">(سيتم استبدال الملف المرفوع حالياً)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('financials.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            إلغاء
        </a>
        <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-save ml-2"></i>
            حفظ التعديلات
        </button>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function invoiceForm() {
    return {
        thirdParties: [],
        selectedFile: null,
        init() {
            // يمكن تحميل بيانات الطرف الثالث الحالية هنا إذا كانت موجودة
        },
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
    }
}
</script>
@endpush

@endsection




