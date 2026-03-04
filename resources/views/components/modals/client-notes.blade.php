<!-- Client Notes Modal -->
<div 
    id="clientNotesModal" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
    x-data="clientNotesModal()"
    x-init="init()"
    x-show="isOpen"
    @click.away="close()"
    x-cloak
    x-transition
>
    <div class="glass-card rounded-2xl p-6 max-w-2xl w-full" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">ملاحظات العميل</h2>
            <button @click="close()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit()" class="space-y-6">
            <div>
                <label class="block text-gray-300 text-sm mb-2">الملاحظة <span class="text-red-400">*</span></label>
                <textarea 
                    x-model="form.note"
                    required
                    rows="5"
                    :class="errors.body ? 'border-red-500' : 'border-white/10'"
                    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary-400/40"
                    placeholder="اكتب ملاحظة حول العميل..."
                ></textarea>
                <p x-show="errors.body" class="text-red-400 text-xs mt-1" x-text="errors.body"></p>
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
                    class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'" class="ml-2"></i>
                    <span x-text="loading ? 'جاري الحفظ...' : 'حفظ'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

<script>
function clientNotesModal() {
    return {
        isOpen: false,
        form: {
            note: '',
            status: 'pending',
            clientId: null
        },
        init() {
            window.addEventListener('open-client-notes-modal', (e) => {
                this.form.clientId = e.detail.clientId;
                this.open();
            });
        },
        open() {
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.form = {
                note: '',
                status: 'pending',
                clientId: null
            };
        },
        loading: false,
        errors: {},
        async submit() {
            if (!this.form.note.trim()) {
                this.errors.body = 'نص الملاحظة مطلوب';
                return;
            }

            this.loading = true;
            this.errors = {};

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                formData.append('body', this.form.note);
                formData.append('_token', csrfToken);

                const response = await fetch(`/clients/${this.form.clientId}/notes`, {
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
                    this.showToast('success', result.message || 'تم إضافة الملاحظة بنجاح');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (result.errors) {
                        this.errors = result.errors;
                    } else {
                        this.errors.body = result.message || 'حدث خطأ أثناء إضافة الملاحظة';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.errors.body = 'حدث خطأ أثناء إضافة الملاحظة';
            } finally {
                this.loading = false;
            }
        },
        showToast(type, message) {
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
    }
}
</script>


