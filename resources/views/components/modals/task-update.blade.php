<!-- Task Status Update Modal -->
<div 
    id="taskStatusModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="taskStatusModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-md w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">تحديث حالة المهمة</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الحالة الجديدة <span class="text-red-400">*</span></label>
                <select 
                    x-model="form.status"
                    required
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                >
                    <option value="">اختر الحالة</option>
                    <option value="new">جديد</option>
                    <option value="in_progress">قيد التنفيذ</option>
                    <option value="done">منجز</option>
                    <option value="rejected">مرفوض</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-300 text-sm mb-2">
                    سبب التغيير
                    <span x-show="form.status === 'rejected'" class="text-red-400">*</span>
                </label>
                <textarea 
                    x-model="form.notes"
                    :required="form.status === 'rejected'"
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    :placeholder="form.status === 'rejected' ? 'يرجى إدخال سبب الرفض (إجباري)...' : 'ملاحظات حول تغيير الحالة (اختياري)...'"
                ></textarea>
            </div>

            <!-- Warning for Reject -->
            <div x-show="form.status === 'rejected'" class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                    <p class="text-red-400 text-sm">يرجى إدخال سبب الرفض قبل المتابعة</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                <button 
                    type="button" 
                    @click="close()"
                    class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white rounded-lg transition-all duration-200"
                >
                    إلغاء
                </button>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200"
                >
                    <i class="fas fa-save ml-2"></i>
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function taskStatusModal() {
    return {
        isOpen: false,
        form: {
            status: '',
            notes: '',
            taskId: null
        },
        init() {
            // Listen for open event
            window.addEventListener('open-task-status-modal', (e) => {
                this.form.taskId = e.detail.taskId;
                if (e.detail.action) {
                    this.form.status = e.detail.action === 'approve' ? 'done' : 'rejected';
                }
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            // Reset form
            this.form = {
                status: '',
                notes: '',
                taskId: null
            };
        },
        async submit() {
            // Validate reject action
            if (this.form.status === 'rejected' && !this.form.notes.trim()) {
                alert('يرجى إدخال سبب الرفض');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('status', this.form.status);
                formData.append('reason', this.form.status === 'rejected' ? this.form.notes : '');
                formData.append('completion_notes', this.form.status === 'done' ? this.form.notes : '');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

                const response = await fetch(`/tasks/${this.form.taskId}/status`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    this.close();
                    
                    // إظهار رسالة نجاح
                    const statusMap = {
                        'new': 'جديد',
                        'in_progress': 'قيد التنفيذ',
                        'done': 'منجز',
                        'rejected': 'مرفوض'
                    };
                    
                    // إعادة تحميل الصفحة
                    window.location.reload();
                } else {
                    let errorMessage = 'حدث خطأ أثناء تحديث الحالة';
                    try {
                        const error = await response.json();
                        errorMessage = error.message || errorMessage;
                    } catch (e) {
                        // If response is not JSON, use default message
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحديث الحالة');
            }
        }
    }
}
</script>


