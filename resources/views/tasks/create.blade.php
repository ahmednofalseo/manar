@extends('layouts.dashboard')

@section('title', 'إنشاء مهمة جديدة - المنار')
@section('page-title', 'إنشاء مهمة جديدة')

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
    <h1 class="text-2xl md:text-3xl font-bold text-white">إنشاء مهمة جديدة</h1>
    <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
        <i class="fas fa-arrow-right ml-2"></i>
        رجوع
    </a>
</div>

<!-- Form -->
<form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" x-data="taskForm()">
    @csrf

    <!-- Basic Information -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">البيانات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">اسم المهمة <span class="text-red-400">*</span></label>
                <input 
                    type="text" 
                    name="title" 
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="مثال: مراجعة المخططات المعمارية"
                >
                @error('title')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">المشروع المرتبط <span class="text-red-400">*</span></label>
                <select name="project_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر المشروع</option>
                    <option value="1">مشروع فيلا رقم 1</option>
                    <option value="2">مشروع مجمع سكني</option>
                    <option value="3">مشروع مبنى تجاري</option>
                </select>
                @error('project_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">المرحلة <span class="text-red-400">*</span></label>
                <select name="stage" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر المرحلة</option>
                    <option value="معماري">معماري</option>
                    <option value="إنشائي">إنشائي</option>
                    <option value="كهربائي">كهربائي</option>
                    <option value="ميكانيكي">ميكانيكي</option>
                    <option value="تقديم للبلدية">تقديم للبلدية</option>
                    <option value="إشراف">إشراف</option>
                    <option value="تسليم">تسليم</option>
                </select>
                @error('stage')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">المهندس / الموظف <span class="text-red-400">*</span></label>
                <select name="assignee_id" required class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="">اختر المهندس</option>
                    <option value="1">محمد أحمد</option>
                    <option value="2">فاطمة سالم</option>
                    <option value="3">خالد مطر</option>
                    <option value="4">سارة علي</option>
                </select>
                @error('assignee_id')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الأولوية</label>
                <select name="priority" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="low">منخفضة</option>
                    <option value="medium" selected>متوسطة</option>
                    <option value="high">عالية</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ البدء</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ date('Y-m-d') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">تاريخ الانتهاء</label>
                <input 
                    type="date" 
                    name="due_date" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">وصف المهمة <span class="text-red-400">*</span></label>
                <textarea 
                    name="description" 
                    required
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="وصف تفصيلي للمهمة..."
                ></textarea>
                @error('description')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-300 text-sm mb-2">ملاحظات مدير المشروع</label>
                <textarea 
                    name="manager_notes" 
                    rows="3"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="ملاحظات إضافية من مدير المشروع..."
                ></textarea>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40">
                    <option value="new" selected>جديد</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="completed">منجز</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 mb-4 md:mb-6">
        <h2 class="text-xl font-bold text-white mb-6">المرفقات</h2>
        <div>
            <label class="block text-gray-300 text-sm mb-2">رفع المرفقات (اختياري)</label>
            <div class="flex items-center gap-4">
                <input 
                    type="file" 
                    name="attachments[]"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png,.dwg"
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
            <p class="text-gray-400 text-xs mt-1">يمكن رفع عدة ملفات (PDF, JPG, PNG, DWG)</p>
        </div>

        <!-- Selected Files List -->
        <div x-show="selectedFiles.length > 0" class="mt-4 space-y-2">
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

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('tasks.index') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
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
function taskForm() {
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


