<!-- Expense Approval Modal -->
<div 
    id="expenseApprovalModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="expenseApprovalModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-md w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white" x-text="modalTitle"></h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">
                    الملاحظات
                    <span x-show="action === 'reject'" class="text-red-400">*</span>
                </label>
                <textarea 
                    x-model="form.notes"
                    :required="action === 'reject'"
                    rows="4"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    :placeholder="action === 'approve' ? 'ملاحظات حول الاعتماد (اختياري)...' : 'يرجى إدخال سبب الرفض (إجباري)...'"
                ></textarea>
            </div>

            <!-- Warning for Reject -->
            <div x-show="action === 'reject'" class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
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
                    {{ __('Cancel') }}
                </button>
                <button 
                    type="submit"
                    class="px-6 py-3 rounded-lg transition-all duration-200 text-white font-semibold"
                    :class="action === 'approve' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                >
                    <i :class="action === 'approve' ? 'fas fa-circle-check' : 'fas fa-ban'" class="ml-2"></i>
                    <span x-text="action === 'approve' ? 'اعتماد' : 'رفض'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function expenseApprovalModal() {
    return {
        isOpen: false,
        action: 'approve', // 'approve' or 'reject'
        form: {
            notes: '',
            expenseId: null
        },
        get modalTitle() {
            return this.action === 'approve' ? 'اعتماد المصروف' : 'رفض المصروف';
        },
        init() {
            // Listen for open event
            window.addEventListener('open-expense-approval', (e) => {
                this.action = e.detail.action;
                this.form.expenseId = e.detail.expenseId;
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
                notes: '',
                expenseId: null
            };
            this.action = 'approve';
        },
        submit() {
            // Validate reject action
            if (this.action === 'reject' && !this.form.notes.trim()) {
                alert('يرجى إدخال سبب الرفض');
                return;
            }

            const url = this.action === 'approve' 
                ? `/expenses/${this.form.expenseId}/approve`
                : `/expenses/${this.form.expenseId}/reject`;
            
            const formData = new FormData();
            formData.append('notes', this.form.notes);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json().catch(() => ({ success: true }));
                }
            })
            .then(data => {
                if (data && data.success !== false) {
                    this.close();
                    window.location.reload();
                } else {
                    alert(data?.message || 'حدث خطأ أثناء ' + (this.action === 'approve' ? 'اعتماد' : 'رفض') + ' المصروف');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء ' + (this.action === 'approve' ? 'اعتماد' : 'رفض') + ' المصروف');
            });
        }
    }
}
</script>


