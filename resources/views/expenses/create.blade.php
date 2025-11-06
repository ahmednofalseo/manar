@extends('layouts.dashboard')

@section('title', 'إضافة مصروف جديد - المنار')
@section('page-title', 'إضافة مصروف جديد')

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">إضافة مصروف جديد</h1>
    <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right ml-2"></i>
        رجوع
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" x-data="expenseForm()">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">البيانات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم السند</label>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="voucher_number" 
                        value="EXP-{{ date('Y') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        readonly
                    >
                    <button type="button" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg text-sm">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <p class="text-gray-400 text-xs mt-1">سيتم توليد الرقم تلقائياً</p>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">التاريخ <span class="text-red-400">*</span></label>
                <input 
                    type="date" 
                    name="date" 
                    required
                    value="{{ date('Y-m-d') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                @error('date')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">القسم <span class="text-red-400">*</span></label>
                <select name="department" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر القسم</option>
                    <option value="إدارة">إدارة</option>
                    <option value="مشاريع">مشاريع</option>
                    <option value="مالية">مالية</option>
                    <option value="مبيعات">مبيعات</option>
                    <option value="تسويق">تسويق</option>
                </select>
                @error('department')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">نوع المصروف <span class="text-red-400">*</span></label>
                <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر النوع</option>
                    <option value="رواتب">رواتب</option>
                    <option value="إيجار">إيجار</option>
                    <option value="كهرباء">كهرباء</option>
                    <option value="مياه">مياه</option>
                    <option value="صيانة">صيانة</option>
                    <option value="معدات">معدات</option>
                    <option value="نقل">نقل</option>
                    <option value="أخرى">أخرى</option>
                </select>
                @error('type')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">الوصف <span class="text-red-400">*</span></label>
                <textarea 
                    name="description" 
                    required
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="وصف تفصيلي للمصروف..."
                ></textarea>
                @error('description')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">المبلغ <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input 
                        type="number" 
                        name="amount" 
                        required
                        step="0.01"
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
                <label class="block text-gray-300 text-sm mb-2">طريقة الدفع <span class="text-red-400">*</span></label>
                <select name="payment_method" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر طريقة الدفع</option>
                    <option value="نقدي">نقدي</option>
                    <option value="تحويل بنكي">تحويل بنكي</option>
                    <option value="شيك">شيك</option>
                    <option value="إلكتروني">إلكتروني</option>
                </select>
                @error('payment_method')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="pending" selected>بانتظار الموافقة</option>
                    <option value="approved">معتمد</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">رفع المرفقات (اختياري)</label>
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
                        <i class="fas fa-upload ml-2"></i>
                        اختر الملفات
                    </label>
                    <span x-show="selectedFiles.length > 0" class="text-gray-300 text-sm" x-text="selectedFiles.length + ' ملف محدد'"></span>
                </div>
                <p class="text-gray-400 text-xs mt-1">يمكن رفع عدة ملفات (PDF, JPG, PNG)</p>
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
        <h2 class="text-xl font-bold text-white mb-6">ملاحظات إضافية</h2>
        <div>
            <textarea 
                name="notes" 
                rows="4"
                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                placeholder="أي ملاحظات إضافية حول المصروف..."
            ></textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
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
function expenseForm() {
    return {
        selectedFiles: [],
        handleFilesSelect(event) {
            const files = Array.from(event.target.files);
            this.selectedFiles = files.map(file => ({
                name: file.name,
                size: file.size
            }));
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
            // Reset input
            const input = document.getElementById('attachmentsInput');
            if (input) {
                input.value = '';
            }
        },
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    }
}
</script>
@endpush

@endsection


