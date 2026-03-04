@extends('layouts.dashboard')

@section('title', 'إنشاء عرض سعر جديد - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', 'إنشاء عرض سعر جديد')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    .items-table th,
    .items-table td {
        padding: 12px;
        text-align: right;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .items-table th {
        background: rgba(23, 51, 67, 0.5);
        color: #fff;
        font-weight: 600;
    }
    .items-table td {
        background: rgba(255, 255, 255, 0.02);
    }
    .items-table input,
    .items-table textarea,
    .items-table select {
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 8px;
        border-radius: 4px;
    }
    .items-table input:focus,
    .items-table textarea:focus,
    .items-table select:focus {
        outline: none;
        border-color: #1db8f8;
        box-shadow: 0 0 0 2px rgba(29, 184, 248, 0.2);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-white">
                إنشاء عرض سعر جديد
            </h1>
            <a href="{{ route('documents.index', ['type' => 'quotation']) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('documents.store') }}" method="POST" x-data="quotationForm" @submit="submitForm($event)">
        @csrf
        <input type="hidden" name="type" value="quotation">
        <input type="hidden" name="title" value="عرض سعر">
        <input type="hidden" name="items" id="items-input" value="">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- معلومات العرض -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">معلومات العرض</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">رقم عرض السعر</label>
                            <input 
                                type="text" 
                                name="document_number"
                                value="{{ old('document_number', '') }}"
                                placeholder="سيتم التوليد تلقائياً"
                                readonly
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">تاريخ الإصدار <span class="text-red-400">*</span></label>
                            <input 
                                type="date" 
                                name="issue_date" 
                                x-model="issueDate"
                                required
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">صالح حتى</label>
                            <input 
                                type="date" 
                                name="valid_until" 
                                x-model="validUntil"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                            >
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">حالة العرض</label>
                            <select 
                                name="status" 
                                x-model="status"
                                class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8]"
                            >
                                <option value="draft">مسودة</option>
                                <option value="sent">مرسل</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- بيانات العميل -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">بيانات العميل</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="select-wrapper">
                            <label class="block text-gray-300 text-sm mb-2">العميل <span class="text-red-400">*</span></label>
                            <select 
                                name="client_id" 
                                x-model="clientId"
                                @change="loadClientData()"
                                required
                                class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8]"
                            >
                                <option value="">اختر العميل</option>
                                @foreach($clients as $cli)
                                <option value="{{ $cli->id }}" {{ old('client_id', $clientId ?? '') == $cli->id ? 'selected' : '' }}>{{ $cli->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm mb-2">المشروع (اختياري)</label>
                            <select 
                                name="project_id" 
                                x-model="projectId"
                                @change="loadProjectData()"
                                class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1db8f8]"
                            >
                                <option value="">اختر المشروع</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ old('project_id', $projectId ?? '') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-white/5 rounded-lg" x-show="clientData.name">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-400">الاسم:</span>
                                <span class="text-white font-semibold" x-text="clientData.name"></span>
                            </div>
                            <div>
                                <span class="text-gray-400">الجوال:</span>
                                <span class="text-white font-semibold" x-text="clientData.phone || 'غير محدد'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400">البريد:</span>
                                <span class="text-white font-semibold" x-text="clientData.email || 'غير محدد'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400">العنوان:</span>
                                <span class="text-white font-semibold" x-text="clientData.address || 'غير محدد'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول البنود -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">بنود عرض السعر</h2>
                        <button 
                            type="button"
                            @click="addItem()"
                            class="px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-plus ml-2"></i>
                            إضافة بند
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 25%;">اسم البند</th>
                                    <th style="width: 20%;">الوصف</th>
                                    <th style="width: 10%;">الكمية</th>
                                    <th style="width: 10%;">الوحدة</th>
                                    <th style="width: 15%;">سعر الوحدة</th>
                                    <th style="width: 15%;">الإجمالي</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="item.id || index">
                                    <tr>
                                        <td x-text="index + 1"></td>
                                        <td>
                                            <input 
                                                type="text" 
                                                x-model="item.item_name"
                                                placeholder="اسم البند"
                                                required
                                                class="w-full"
                                            >
                                        </td>
                                        <td>
                                            <textarea 
                                                x-model="item.description"
                                                placeholder="وصف مختصر"
                                                rows="2"
                                                class="w-full"
                                            ></textarea>
                                        </td>
                                        <td>
                                            <input 
                                                type="number" 
                                                x-model.number="item.qty"
                                                @input="calculateItemTotal(index)"
                                                min="0"
                                                step="0.01"
                                                required
                                                class="w-full"
                                            >
                                        </td>
                                        <td>
                                            <input 
                                                type="text" 
                                                x-model="item.unit"
                                                placeholder="قطعة"
                                                class="w-full"
                                            >
                                        </td>
                                        <td>
                                            <input 
                                                type="number" 
                                                x-model.number="item.unit_price"
                                                @input="calculateItemTotal(index)"
                                                min="0"
                                                step="0.01"
                                                required
                                                class="w-full"
                                            >
                                        </td>
                                        <td>
                                            <input 
                                                type="text" 
                                                :value="formatNumber(item.line_total)"
                                                readonly
                                                class="w-full bg-white/5"
                                            >
                                        </td>
                                        <td>
                                            <button 
                                                type="button"
                                                @click="removeItem(index)"
                                                class="text-red-400 hover:text-red-300"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="8" class="text-center text-gray-400 py-8">
                                        لا توجد بنود. اضغط "إضافة بند" لإضافة بند جديد.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- الملخص المالي -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">الملخص المالي</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">الإجمالي الفرعي:</span>
                            <span class="text-white font-bold text-lg" x-text="formatNumber(totals.subtotal)"></span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 text-sm mb-2">نوع الخصم</label>
                                <select 
                                    name="discount_type" 
                                    x-model="discountType"
                                    @change="calculateTotals()"
                                    class="w-full bg-[#173343]/90 border-2 border-white/30 rounded-lg px-4 py-3 text-white font-semibold"
                                >
                                    <option value="">بدون خصم</option>
                                    <option value="amount">مبلغ ثابت</option>
                                    <option value="percent">نسبة مئوية</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-300 text-sm mb-2">قيمة الخصم</label>
                                <input 
                                    type="number" 
                                    name="discount_value"
                                    x-model.number="discountValue"
                                    @input="calculateTotals()"
                                    min="0"
                                    step="0.01"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white"
                                >
                            </div>
                        </div>
                        <div x-show="discountType && discountValue > 0" class="flex justify-between items-center">
                            <span class="text-gray-300">الخصم:</span>
                            <span class="text-red-400 font-bold" x-text="'-' + formatNumber(totals.discount)"></span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 text-sm mb-2">نسبة الضريبة المضافة (%)</label>
                                <input 
                                    type="number" 
                                    name="vat_percent"
                                    x-model.number="vatPercent"
                                    @input="calculateTotals()"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white"
                                >
                            </div>
                            <div x-show="vatPercent > 0">
                                <label class="block text-gray-300 text-sm mb-2">مبلغ الضريبة</label>
                                <input 
                                    type="text" 
                                    :value="formatNumber(totals.vat)"
                                    readonly
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white"
                                >
                            </div>
                        </div>
                        <div class="border-t border-white/20 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-bold text-xl">الإجمالي الكلي:</span>
                                <span class="text-[#1db8f8] font-bold text-2xl" x-text="formatNumber(totals.total)"></span>
                            </div>
                            <div class="mt-2">
                                <label class="block text-gray-300 text-sm mb-2">الإجمالي بالحروف</label>
                                <input 
                                    type="text" 
                                    name="total_in_words"
                                    x-model="totalInWords"
                                    readonly
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white font-semibold"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الشروط والأحكام -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">الشروط والأحكام</h2>
                    <div 
                        id="terms-editor"
                        contenteditable="true"
                        class="min-h-[200px] bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        style="direction: rtl; text-align: right;"
                    >{!! $defaultTerms !!}</div>
                    <textarea 
                        name="terms_html" 
                        x-model="termsHtml"
                        class="hidden"
                    ></textarea>
                </div>

                <!-- ملاحظات داخلية -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-xl font-bold text-white mb-4">ملاحظات داخلية (لن تظهر في PDF)</h2>
                    <textarea 
                        name="notes_internal" 
                        rows="3"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        placeholder="ملاحظات داخلية للمكتب..."
                    >{{ old('notes_internal', '') }}</textarea>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- معلومات المكتب -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-lg font-bold text-white mb-4">معلومات المكتب</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-gray-400">اسم المكتب:</span>
                            <p class="text-white font-semibold">{{ $officeName }}</p>
                        </div>
                        @if($officeLicense)
                        <div>
                            <span class="text-gray-400">رقم السجل:</span>
                            <p class="text-white font-semibold">{{ $officeLicense }}</p>
                        </div>
                        @endif
                        @if($officeAddress)
                        <div>
                            <span class="text-gray-400">العنوان:</span>
                            <p class="text-white font-semibold">{{ $officeAddress }}</p>
                        </div>
                        @endif
                        @if($officePhone)
                        <div>
                            <span class="text-gray-400">الهاتف:</span>
                            <p class="text-white font-semibold">{{ $officePhone }}</p>
                        </div>
                        @endif
                        @if($officeEmail)
                        <div>
                            <span class="text-gray-400">البريد:</span>
                            <p class="text-white font-semibold">{{ $officeEmail }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6">
                    <h2 class="text-lg font-bold text-white mb-4">الإجراءات</h2>
                    <div class="space-y-2">
                        <button 
                            type="submit" 
                            :disabled="loading || items.length === 0"
                            :class="(loading || items.length === 0) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="w-full px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-save ml-2"></i>
                            <span x-show="!loading">حفظ مسودة</span>
                            <span x-show="loading">جاري الحفظ...</span>
                        </button>
                        <button 
                            type="button"
                            @click="previewPdf()"
                            :disabled="items.length === 0"
                            class="w-full px-4 py-2 bg-[#1db8f8] hover:bg-[#1db8f8]/80 text-white rounded-lg transition-all duration-200"
                        >
                            <i class="fas fa-file-pdf ml-2"></i>
                            معاينة PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
</div>
@endsection

@push('styles')
<script>
// Define quotationForm using Alpine.data() - will be available when Alpine loads
document.addEventListener('alpine:init', () => {
    Alpine.data('quotationForm', () => ({
        clientId: '{{ old("client_id", $clientId ?? "") }}',
        projectId: '{{ old("project_id", $projectId ?? "") }}',
        issueDate: '{{ old("issue_date", date("Y-m-d")) }}',
        validUntil: '{{ old("valid_until", "") }}',
        status: '{{ old("status", "draft") }}',
        items: @json(old('items', [])),
        discountType: '{{ old("discount_type", "") }}',
        discountValue: {{ old("discount_value", 0) }},
        vatPercent: {{ old("vat_percent", 0) }},
        totalInWords: '',
        termsHtml: '',
        loading: false,
        clientData: {},
        totals: {
            subtotal: 0,
            discount: 0,
            vat: 0,
            total: 0
        },
        
        init() {
            // Initialize items - ensure it's an array
            if (!Array.isArray(this.items)) {
                this.items = [];
            }
            
            // Clean up items - remove any invalid ones and ensure properties
            this.items = this.items
                .filter(item => item !== null && item !== undefined)
                .map((item, idx) => {
                    const qty = parseFloat(item.qty) || 1;
                    const unitPrice = parseFloat(item.unit_price) || 0;
                    return {
                        item_name: item.item_name || '',
                        description: item.description || '',
                        qty: qty,
                        unit: item.unit || 'قطعة',
                        unit_price: unitPrice,
                        line_total: parseFloat((qty * unitPrice).toFixed(2)),
                        id: item.id || `item-${Date.now()}-${idx}-${Math.random()}`,
                    };
                });
            
            // Add one item if empty
            if (this.items.length === 0) {
                this.addItem();
            }
            
            // Load client data if client is selected
            if (this.clientId) {
                this.loadClientData();
            }
            
            // Calculate totals
            this.calculateTotals();
            
            // Update terms HTML
            this.updateTermsHtml();
            
            // Watch terms editor
            const termsEditor = document.getElementById('terms-editor');
            if (termsEditor) {
                termsEditor.addEventListener('input', () => {
                    this.updateTermsHtml();
                });
            }
        },
        
        addItem() {
            const newItem = {
                item_name: '',
                description: '',
                qty: 1,
                unit: 'قطعة',
                unit_price: 0,
                line_total: 0,
                id: `item-${Date.now()}-${Math.random()}` // Unique ID for Alpine.js
            };
            this.items.push(newItem);
            // Recalculate totals after adding
            this.$nextTick(() => {
                this.calculateTotals();
            });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },
        
        calculateItemTotal(index) {
            const item = this.items[index];
            item.line_total = parseFloat((item.qty * item.unit_price).toFixed(2));
            this.calculateTotals();
        },
        
        calculateTotals() {
            // Calculate subtotal
            this.totals.subtotal = this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.line_total) || 0);
            }, 0);
            
            // Calculate discount
            let discount = 0;
            if (this.discountType === 'amount') {
                discount = parseFloat(this.discountValue) || 0;
            } else if (this.discountType === 'percent') {
                discount = (this.totals.subtotal * (parseFloat(this.discountValue) || 0)) / 100;
            }
            this.totals.discount = discount;
            
            // Calculate after discount
            const afterDiscount = this.totals.subtotal - discount;
            
            // Calculate VAT
            const vat = (afterDiscount * (parseFloat(this.vatPercent) || 0)) / 100;
            this.totals.vat = vat;
            
            // Calculate total
            this.totals.total = afterDiscount + vat;
            
            // Update total in words
            this.updateTotalInWords();
        },
        
        updateTotalInWords() {
            if (this.totals.total > 0) {
                fetch('/api/convert-number-to-words?number=' + this.totals.total)
                    .then(response => response.json())
                    .then(data => {
                        this.totalInWords = data.words || '';
                    })
                    .catch(() => {
                        // Fallback: simple conversion
                        this.totalInWords = this.totals.total.toFixed(2) + ' ريال';
                    });
            } else {
                this.totalInWords = '';
            }
        },
        
        updateTermsHtml() {
            const termsEditor = document.getElementById('terms-editor');
            if (termsEditor) {
                this.termsHtml = termsEditor.innerHTML;
            }
        },
        
        formatNumber(num) {
            return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        
        async loadClientData() {
            if (!this.clientId) {
                this.clientData = {};
                return;
            }
            
            try {
                const response = await fetch('/api/clients/' + this.clientId);
                const data = await response.json();
                this.clientData = {
                    name: data.name || '',
                    phone: data.phone || '',
                    email: data.email || '',
                    address: data.address || ''
                };
            } catch (error) {
                console.error('Error loading client data:', error);
            }
        },
        
        async loadProjectData() {
            // Auto-fill client from project
            if (this.projectId) {
                try {
                    const response = await fetch('/api/projects/' + this.projectId);
                    const data = await response.json();
                    if (data.client_id) {
                        this.clientId = data.client_id;
                        this.loadClientData();
                    }
                } catch (error) {
                    console.error('Error loading project data:', error);
                }
            }
        },
        
        async submitForm(e) {
            // Prevent default form submission
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Validate client
            if (!this.clientId) {
                alert('يرجى اختيار العميل');
                this.loading = false;
                return false;
            }
            
            // Validate items - filter out empty items
            const validItems = this.items
                .filter(item => {
                    return item && 
                           item.item_name && 
                           typeof item.item_name === 'string' && 
                           item.item_name.trim() !== '';
                })
                .map((item, index) => {
                    // Recalculate line_total
                    const qty = parseFloat(item.qty) || 0;
                    const unitPrice = parseFloat(item.unit_price) || 0;
                    const lineTotal = parseFloat((qty * unitPrice).toFixed(2));
                    
                    return {
                        item_name: item.item_name.trim(),
                        description: (item.description || '').trim(),
                        qty: qty,
                        unit: item.unit || 'قطعة',
                        unit_price: unitPrice,
                        line_total: lineTotal,
                        position: index,
                    };
                });
            
            if (validItems.length === 0) {
                alert('يرجى إضافة بند واحد على الأقل مع اسم البند');
                this.loading = false;
                return false;
            }
            
            this.loading = true;
            this.updateTermsHtml();
            
            // Get form element
            const form = this.$el;
            if (!form || form.tagName !== 'FORM') {
                alert('خطأ في النموذج');
                this.loading = false;
                return false;
            }
            
            // Update the hidden input with valid items only
            const itemsInput = document.getElementById('items-input');
            if (itemsInput) {
                itemsInput.value = JSON.stringify(validItems);
            }
            
            // Also update title if empty
            const titleInput = form.querySelector('input[name="title"]');
            if (titleInput && !titleInput.value.trim()) {
                titleInput.value = 'عرض سعر - ' + new Date().toLocaleDateString('ar-SA');
            }
            
            // Update terms_html textarea
            const termsTextarea = form.querySelector('textarea[name="terms_html"]');
            if (termsTextarea) {
                termsTextarea.value = this.termsHtml;
            }
            
            // Wait for Alpine to update DOM
            await this.$nextTick();
            await new Promise(resolve => setTimeout(resolve, 200));
            
            // Double check items input
            const itemsInputFinal = document.getElementById('items-input');
            if (itemsInputFinal) {
                itemsInputFinal.value = JSON.stringify(validItems);
            }
            
            // Use FormData to submit
            const formData = new FormData(form);
            formData.set('items', JSON.stringify(validItems));
            formData.set('terms_html', this.termsHtml);
            
            // Submit using fetch to handle errors properly
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                const contentType = response.headers.get('content-type');
                
                // Handle JSON response (errors)
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();
                    if (result.errors) {
                        // Validation errors
                        let errorMsg = 'حدثت الأخطاء التالية:\n';
                        Object.keys(result.errors).forEach(key => {
                            errorMsg += result.errors[key].join('\n') + '\n';
                        });
                        alert(errorMsg);
                        this.loading = false;
                        return false;
                    } else if (result.message) {
                        alert('خطأ: ' + result.message);
                        this.loading = false;
                        return false;
                    }
                }
                
                // Handle redirect (success)
                if (response.redirected) {
                    window.location.href = response.url;
                    return false;
                }
                
                // Try to get redirect URL from response
                const text = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, 'text/html');
                
                // Check for redirect in meta tag or script
                const redirectMatch = text.match(/window\.location\s*=\s*['"]([^'"]+)['"]/) || 
                                      text.match(/location\.href\s*=\s*['"]([^'"]+)['"]/);
                
                if (redirectMatch) {
                    window.location.href = redirectMatch[1];
                } else if (response.ok) {
                    // Assume success and redirect
                    const url = new URL(form.action);
                    window.location.href = url.pathname.replace('/create', '');
                } else {
                    throw new Error('حدث خطأ أثناء الحفظ');
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('حدث خطأ أثناء حفظ العرض: ' + error.message);
                this.loading = false;
            }
            
            return false;
        },
        
        previewPdf() {
            if (this.items.length === 0) {
                alert('يرجى إضافة بند واحد على الأقل');
                return;
            }
            
            // This will be handled after saving
            alert('يرجى حفظ العرض أولاً ثم معاينة PDF');
        }
    }));
});
</script>
@endpush
