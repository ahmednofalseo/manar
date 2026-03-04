@extends('layouts.dashboard')

@section('title', __('Edit') . ' ' . __('Expenses') . ' - ' . \App\Helpers\SettingsHelper::systemName())
@section('page-title', __('Edit') . ' ' . __('Expenses'))

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Edit') }} {{ __('Expenses') }}</h1>
    <div class="flex items-center gap-3">
        <a href="{{ route('expenses.show', $expense->id) }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-eye {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('View') }}
        </a>
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
            <i class="fas fa-arrow-right {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
            {{ __('Back') }}
        </a>
    </div>
</div>

<!-- Form -->
<form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data" x-data="expenseForm()">
    @csrf
    @method('PUT')

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">{{ __('Basic Information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">رقم السند</label>
                <div class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="voucher_number" 
                        value="{{ old('voucher_number', $expense->voucher_number) }}"
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                        readonly
                    >
                </div>
                <p class="text-gray-400 text-xs mt-1">رقم السند</p>
                @error('voucher_number')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">التاريخ <span class="text-red-400">*</span></label>
                <input 
                    type="date" 
                    name="date" 
                    required
                    value="{{ old('date', $expense->date->format('Y-m-d')) }}"
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
                    <option value="إدارة" {{ old('department', $expense->department) == 'إدارة' ? 'selected' : '' }}>إدارة</option>
                    <option value="مشاريع" {{ old('department', $expense->department) == 'مشاريع' ? 'selected' : '' }}>مشاريع</option>
                    <option value="مالية" {{ old('department', $expense->department) == 'مالية' ? 'selected' : '' }}>مالية</option>
                    <option value="مبيعات" {{ old('department', $expense->department) == 'مبيعات' ? 'selected' : '' }}>مبيعات</option>
                    <option value="تسويق" {{ old('department', $expense->department) == 'تسويق' ? 'selected' : '' }}>تسويق</option>
                </select>
                @error('department')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">نوع المصروف <span class="text-red-400">*</span></label>
                <select name="type" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">{{ __('Select Type') }}</option>
                    <option value="رواتب" {{ old('type', $expense->type) == 'رواتب' ? 'selected' : '' }}>رواتب</option>
                    <option value="إيجار" {{ old('type', $expense->type) == 'إيجار' ? 'selected' : '' }}>إيجار</option>
                    <option value="كهرباء" {{ old('type', $expense->type) == 'كهرباء' ? 'selected' : '' }}>كهرباء</option>
                    <option value="مياه" {{ old('type', $expense->type) == 'مياه' ? 'selected' : '' }}>مياه</option>
                    <option value="صيانة" {{ old('type', $expense->type) == 'صيانة' ? 'selected' : '' }}>صيانة</option>
                    <option value="معدات" {{ old('type', $expense->type) == 'معدات' ? 'selected' : '' }}>معدات</option>
                    <option value="نقل" {{ old('type', $expense->type) == 'نقل' ? 'selected' : '' }}>نقل</option>
                    <option value="أخرى" {{ old('type', $expense->type) == 'أخرى' ? 'selected' : '' }}>أخرى</option>
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
                >{{ old('description', $expense->description) }}</textarea>
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
                        value="{{ old('amount', $expense->amount) }}"
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
                    <option value="نقدي" {{ old('payment_method', $expense->payment_method) == 'نقدي' ? 'selected' : '' }}>نقدي</option>
                    <option value="تحويل بنكي" {{ old('payment_method', $expense->payment_method) == 'تحويل بنكي' ? 'selected' : '' }}>تحويل بنكي</option>
                    <option value="شيك" {{ old('payment_method', $expense->payment_method) == 'شيك' ? 'selected' : '' }}>شيك</option>
                    <option value="إلكتروني" {{ old('payment_method', $expense->payment_method) == 'إلكتروني' ? 'selected' : '' }}>إلكتروني</option>
                </select>
                @error('payment_method')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="pending" {{ old('status', $expense->status->value) == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                    <option value="approved" {{ old('status', $expense->status->value) == 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="rejected" {{ old('status', $expense->status->value) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
        
        <!-- Existing Attachments -->
        @if($expense->attachments->count() > 0)
        <div class="mb-4 space-y-2">
            <p class="text-gray-300 text-sm mb-2">المرفقات الحالية:</p>
            @foreach($expense->attachments as $attachment)
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file text-primary-400"></i>
                    <span class="text-white text-sm">{{ $attachment->name }}</span>
                    <span class="text-gray-400 text-xs">{{ $attachment->formatted_size }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('expenses.attachments.download', ['id' => $expense->id, 'attachmentId' => $attachment->id]) }}" class="text-primary-400 hover:text-primary-300" title="تحميل">
                        <i class="fas fa-download"></i>
                    </a>
                    <form action="{{ route('expenses.attachments.delete', ['id' => $expense->id, 'attachmentId' => $attachment->id]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المرفق؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Upload New Attachments -->
        <div class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">رفع مرفقات جديدة (اختياري)</label>
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
            >{{ old('notes', $expense->notes) }}</textarea>
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

