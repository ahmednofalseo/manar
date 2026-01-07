<div 
    x-data="taskCommentModal()" 
    x-init="init()"
    x-show="isOpen" 
    x-cloak
    x-transition
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
    style="display: none;" 
    @keydown.escape.window="close()"
>
    <div class="glass-card rounded-xl md:rounded-2xl p-4 md:p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" @click.away="close()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl md:text-2xl font-bold text-white">إضافة ملاحظة</h3>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form @submit.prevent="submit()">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-300 text-sm mb-2">الملاحظة <span class="text-red-400">*</span></label>
                    <textarea 
                        x-model="form.notes"
                        required
                        rows="6"
                        class="w-full bg-white/5 border rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 @if($errors->has('notes')) border-red-500 bg-red-500/10 focus:ring-red-500/40 @else border-white/10 focus:ring-primary-400/40 @endif"
                        placeholder="اكتب ملاحظتك هنا..."
                    ></textarea>
                    <p x-show="errors.notes" class="text-red-400 text-xs mt-1" x-text="errors.notes"></p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" @click="close()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200" :disabled="loading">
                        <span x-show="!loading">
                            <i class="fas fa-save ml-2"></i>
                            حفظ
                        </span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            جاري الحفظ...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function taskCommentModal() {
    return {
        isOpen: false,
        loading: false,
        form: {
            taskId: null,
            notes: ''
        },
        errors: {},
        init() {
            // Listen for open event
            window.addEventListener('open-task-comment-modal', (e) => {
                this.form.taskId = e.detail.taskId || e.detail.id;
                this.open();
            });
        },
        open() {
            this.isOpen = true;
            this.errors = {};
            this.form.notes = '';
        },
        close() {
            this.isOpen = false;
            this.errors = {};
            this.form = {
                taskId: null,
                notes: ''
            };
        },
        async submit() {
            if (!this.form.notes.trim()) {
                this.errors.notes = 'الملاحظة مطلوبة';
                return;
            }

            // منع إرسال الطلب مرتين
            if (this.loading) {
                return;
            }

            this.loading = true;
            this.errors = {};

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                formData.append('notes', this.form.notes);
                formData.append('_token', csrfToken);

                const response = await fetch(`/tasks/${this.form.taskId}/comment`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success !== false) {
                    this.close();
                    // إظهار رسالة نجاح
                    this.showToast('success', result.message || 'تم إضافة الملاحظة بنجاح');
                    // منع إعادة الإرسال
                    this.loading = true;
                    // إعادة تحميل الصفحة بعد ثانية
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // عرض الأخطاء
                    if (result.errors) {
                        this.errors = result.errors;
                    } else {
                        this.errors.notes = result.message || 'حدث خطأ أثناء إضافة الملاحظة';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors.notes = 'حدث خطأ أثناء إضافة الملاحظة';
            } finally {
                this.loading = false;
            }
        },
        showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white animate-slide-in`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
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
    }
}
</script>
@endpush

