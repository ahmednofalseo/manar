@extends('layouts.dashboard')

@section('title', 'إنشاء فاتورة جديدة - المنار')
@section('page-title', 'إنشاء فاتورة جديدة')

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
                <label class="block text-gray-300 text-sm mb-2">العميل</label>
                <select name="client_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر العميل (اختياري)</option>
                    @foreach($clients as $client)
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
                <label class="block text-gray-300 text-sm mb-2">المشروع <span class="text-red-400">*</span></label>
                <select name="project_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Project') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
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
                        name="number" 
                        value="{{ old('number') }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="سيتم توليد الرقم تلقائياً"
                    >
                </div>
                <p class="text-gray-400 text-xs mt-1">سيتم توليد الرقم تلقائياً إذا تركت فارغاً</p>
                @error('number')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">قيمة الفاتورة <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input 
                        type="number" 
                        name="total_amount" 
                        value="{{ old('total_amount') }}"
                        required
                        step="0.01"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 pr-16 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="0.00"
                    >
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">ر.س</span>
                </div>
                @error('total_amount')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">عدد الدفعات</label>
                <input 
                    type="number" 
                    name="installments_count" 
                    value="{{ old('installments_count', 1) }}"
                    min="1"
                    max="100"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                <p class="text-gray-400 text-xs mt-1">سيتم إنشاء دفعات تلقائياً بناءً على هذا العدد</p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">طريقة الدفع</label>
                <select name="payment_method" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="cash">نقدي</option>
                    <option value="transfer">تحويل بنكي</option>
                    <option value="check">شيك</option>
                    <option value="electronic">إلكتروني</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الإصدار <span class="text-red-400">*</span></label>
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
                <label class="block text-gray-300 text-sm mb-2">تاريخ الاستحقاق <span class="text-red-400">*</span></label>
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
                ></textarea>
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
            حفظ
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
    }
}
</script>
@endpush

@endsection


