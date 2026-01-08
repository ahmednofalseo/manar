<!-- Decide Approval Modal -->
<div 
    id="decideApprovalModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="decideApprovalModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-2xl w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white" x-text="form.decision === 'approve' ? 'الموافقة على الطلب' : 'رفض الطلب'"></h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">
                    <span x-show="form.decision === 'reject'">سبب الرفض</span>
                    <span x-show="form.decision === 'approve'">ملاحظة (اختياري)</span>
                    <span x-show="form.decision === 'reject'" class="text-red-400">*</span>
                </label>
                <textarea 
                    x-model="form.note"
                    :required="form.decision === 'reject'"
                    rows="5"
                    :class="errors.note ? 'border-red-500' : 'border-white/10'"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    :placeholder="form.decision === 'reject' ? 'يرجى كتابة سبب الرفض...' : 'ملاحظة (اختياري)...'"
                ></textarea>
                <p x-show="errors.note" class="text-red-400 text-xs mt-1" x-text="errors.note"></p>
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
                    :disabled="loading"
                    :class="form.decision === 'approve' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                    class="px-6 py-3 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : (form.decision === 'approve' ? 'fa-check' : 'fa-times')" class="ml-2"></i>
                    <span x-text="loading ? 'جاري المعالجة...' : (form.decision === 'approve' ? 'موافقة' : 'رفض')"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<script>
function decideApprovalModal() {
    return {
        isOpen: false,
        loading: false,
        errors: {},
        form: {
            approvalId: null,
            decision: 'approve',
            note: ''
        },
        init() {
            window.addEventListener('open-decide-modal', (e) => {
                this.form.approvalId = e.detail.approvalId;
                this.form.decision = e.detail.decision;
                this.form.note = '';
                this.errors = {};
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.form = {
                approvalId: null,
                decision: 'approve',
                note: ''
            };
            this.errors = {};
        },
        async submit() {
            if (this.form.decision === 'reject' && !this.form.note.trim()) {
                this.errors.note = 'سبب الرفض مطلوب';
                return;
            }

            this.loading = true;
            this.errors = {};

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                formData.append('decision', this.form.decision);
                formData.append('note', this.form.note);
                formData.append('_token', csrfToken);

                const response = await fetch(`/approvals/${this.form.approvalId}/decide`, {
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
                    showToast('success', result.message || 'تم اتخاذ القرار بنجاح');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                    } else {
                        this.errors.note = result.message || 'حدث خطأ أثناء اتخاذ القرار';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors.note = 'حدث خطأ أثناء اتخاذ القرار';
            } finally {
                this.loading = false;
            }
        }
    }
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 left-4 z-50 p-4 rounded-lg shadow-lg max-w-md text-white animate-slide-in ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
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
</script>




